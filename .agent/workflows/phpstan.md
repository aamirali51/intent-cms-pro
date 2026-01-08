---
description: PHPStan Level 9 compliance rule - run after every feature implementation
---

# PHPStan Level 9 Workflow

## MANDATORY: Run After Every Feature

After implementing ANY new feature, handler, or service:

```powershell
// turbo
vendor/bin/phpstan analyse --memory-limit=512M --no-progress
```

If errors are found, FIX THEM IMMEDIATELY before moving on.

## Type-Safe Coding Patterns

### 1. Request Parameters (ALWAYS validate mixed types)
```php
// ❌ WRONG - Will fail PHPStan Level 9
$limit = (int) $req->get('limit');

// ✅ CORRECT
$limitRaw = $req->get('limit');
$limit = is_numeric($limitRaw) ? (int) $limitRaw : 20;
```

### 2. Array Parameters (ALWAYS add @param annotation)
```php
// ❌ WRONG
public function show(Request $req, Response $res, array $params): Response

// ✅ CORRECT
/**
 * @param array<string, string> $params
 */
public function show(Request $req, Response $res, array $params): Response
```

### 3. JSON Body (ALWAYS validate is_array)
```php
// ❌ WRONG
$data = $req->json();
$title = $data['title'];

// ✅ CORRECT
$data = $req->json();
if (!is_array($data)) {
    return $res->json(['error' => 'Invalid request body'], 400);
}
$title = isset($data['title']) && is_string($data['title']) ? trim($data['title']) : '';
```

### 4. preg_replace (ALWAYS handle null return)
```php
// ❌ WRONG
$text = preg_replace('/pattern/', '-', $text);

// ✅ CORRECT  
$result = preg_replace('/pattern/', '-', $text);
$text = $result !== null ? $result : $text;
```

### 5. Database Results (ALWAYS validate before array access)
```php
// ❌ WRONG
return $result[0]['slug'] ?? null;

// ✅ CORRECT
if (!empty($result) && isset($result[0]['slug']) && is_string($result[0]['slug'])) {
    return $result[0]['slug'];
}
return null;
```

## Checklist Before Committing

- [ ] Ran `vendor/bin/phpstan analyse --memory-limit=512M`
- [ ] All new methods have proper `@param` annotations
- [ ] All `$req->get()` calls use `is_numeric()` or `is_string()` checks
- [ ] All `$req->json()` calls validate `is_array()`
- [ ] All `preg_replace()` handle null returns
