<?php

declare(strict_types=1);

namespace App\Handlers\Settings;

use Core\Request;
use Core\Response;
use Core\Auth;
use Core\DB;

/**
 * User Handler
 * 
 * CRUD operations for user management.
 * PHPStan Level 9 compliant.
 */
class UserHandler
{
    /**
     * Available user roles
     */
    private const ROLES = ['admin', 'editor', 'author', 'user'];

    /**
     * List all users
     */
    public static function index(Request $req, Response $res): Response
    {
        try {
            $users = db()->raw(
                'SELECT id, name, email, role, avatar, created_at, updated_at 
                 FROM users 
                 ORDER BY created_at DESC'
            );

            // Filter: Allow plugins to modify users list
            if (function_exists('apply_filters')) {
                $users = apply_filters('cms.api.users', $users);
            }

            return $res->json([
                'data' => $users,
                'roles' => self::ROLES
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to fetch users: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get single user
     * 
     * @param array<string, string> $params
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $result = db()->raw(
                'SELECT id, name, email, role, avatar, created_at, updated_at 
                 FROM users WHERE id = ?', 
                [$id]
            );
            
            if (empty($result)) {
                return $res->json(['error' => 'User not found'], 404);
            }
            
            return $res->json($result[0]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Database error'], 500);
        }
    }

    /**
     * Create new user
     */
    public static function store(Request $req, Response $res): Response
    {
        try {
            $data = $req->json();
            
            if (!is_array($data)) {
                return $res->json(['error' => 'Invalid request body'], 400);
            }
            
            // Validate required fields
            $name = isset($data['name']) && is_string($data['name']) ? trim($data['name']) : '';
            $email = isset($data['email']) && is_string($data['email']) ? trim($data['email']) : '';
            $password = isset($data['password']) && is_string($data['password']) ? $data['password'] : '';
            $role = isset($data['role']) && is_string($data['role']) ? $data['role'] : 'user';
            $avatar = isset($data['avatar']) && is_string($data['avatar']) ? $data['avatar'] : null;
            
            if (empty($name)) {
                return $res->json(['error' => 'Name is required'], 422);
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $res->json(['error' => 'Valid email is required'], 422);
            }
            
            if (empty($password) || strlen($password) < 6) {
                return $res->json(['error' => 'Password must be at least 6 characters'], 422);
            }
            
            // Validate role
            if (!in_array($role, self::ROLES, true)) {
                $role = 'user';
            }
            
            // Check if email already exists
            $existing = db()->raw('SELECT id FROM users WHERE email = ?', [$email]);
            if (!empty($existing)) {
                return $res->json(['error' => 'Email already exists'], 422);
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            db()->raw(
                'INSERT INTO users (name, email, password, role, avatar, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                [$name, $email, $hashedPassword, $role, $avatar]
            );

            $id = DB::connection()->lastInsertId();
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.user.created', (int) $id, [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]);
            }
            
            return $res->json([
                'id' => $id, 
                'message' => 'User created successfully'
            ], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update user
     * 
     * @param array<string, string> $params
     */
    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            $data = $req->json();

            if (!is_array($data)) {
                return $res->json(['error' => 'Invalid request body'], 400);
            }

            // Check user exists
            $existing = db()->raw('SELECT id, email FROM users WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'User not found'], 404);
            }

            $currentEmail = isset($existing[0]['email']) && is_string($existing[0]['email']) 
                ? $existing[0]['email'] 
                : '';

            // Build update query
            $fields = [];
            $values = [];

            if (isset($data['name']) && is_string($data['name'])) {
                $name = trim($data['name']);
                if (!empty($name)) {
                    $fields[] = 'name = ?';
                    $values[] = $name;
                }
            }
            
            if (isset($data['email']) && is_string($data['email'])) {
                $email = trim($data['email']);
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Check email uniqueness if changed
                    if ($email !== $currentEmail) {
                        $emailExists = db()->raw('SELECT id FROM users WHERE email = ? AND id != ?', [$email, $id]);
                        if (!empty($emailExists)) {
                            return $res->json(['error' => 'Email already exists'], 422);
                        }
                    }
                    $fields[] = 'email = ?';
                    $values[] = $email;
                }
            }

            if (isset($data['role']) && is_string($data['role'])) {
                if (in_array($data['role'], self::ROLES, true)) {
                    $fields[] = 'role = ?';
                    $values[] = $data['role'];
                }
            }

            if (isset($data['avatar'])) {
                $fields[] = 'avatar = ?';
                $values[] = is_string($data['avatar']) ? $data['avatar'] : null;
            }

            // Handle password change (only if provided and non-empty)
            if (isset($data['password']) && is_string($data['password']) && !empty($data['password'])) {
                if (strlen($data['password']) < 6) {
                    return $res->json(['error' => 'Password must be at least 6 characters'], 422);
                }
                $fields[] = 'password = ?';
                $values[] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            $fields[] = 'updated_at = NOW()';

            if (count($fields) === 1) {
                return $res->json(['message' => 'No changes']);
            }

            $values[] = $id;

            db()->raw(
                'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );

            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.user.updated', $id, $data);
            }

            return $res->json(['message' => 'User updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete user
     * 
     * @param array<string, string> $params
     */
    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // Prevent self-deletion
            $currentUserId = Auth::id();
            if ($currentUserId === $id) {
                return $res->json(['error' => 'You cannot delete your own account'], 400);
            }
            
            // Check user exists
            $existing = db()->raw('SELECT id, name, email, role FROM users WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'User not found'], 404);
            }

            db()->raw('DELETE FROM users WHERE id = ?', [$id]);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.user.deleted', $id, $existing[0]);
            }
            
            return $res->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete user'], 500);
        }
    }
}
