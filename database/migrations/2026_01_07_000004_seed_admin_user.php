<?php

declare(strict_types=1);

use Core\Migration;


return new class extends Migration
{
    public function up(): void
    {
        // Check if admin user already exists
        $existing = db()->table('users')->where('email', 'admin@intent.com')->first();
        
        if ($existing === null) {
            $password = password_hash('password', PASSWORD_DEFAULT);
            db()->table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@intent.com',
                'password' => $password,
                'role' => 'admin'
            ]);
        }
    }

    public function down(): void
    {
        db()->table('users')->where('email', 'admin@intent.com')->delete();
    }
};
