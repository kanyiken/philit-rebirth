<?php
require_once __DIR__ . '/../config.php';

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $input['password'] ?? '';

if (!$email || !$password) {
    json_response(['error' => 'Email and password are required'], 422);
}

$stmt = db()->prepare('SELECT id, name, email, password_hash, api_token, role, membership_expires_at FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    json_response(['error' => 'Invalid credentials'], 401);
}

echo json_encode([
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'membership_expires_at' => $user['membership_expires_at'],
    'token' => $user['api_token'],
]);
