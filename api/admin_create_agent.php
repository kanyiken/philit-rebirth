<?php
require_once __DIR__ . '/../config.php';

require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($input['name'] ?? '');
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $input['password'] ?? '';

if (!$name || !$email || !$password) {
    json_response(['error' => 'Name, valid email, and password are required'], 422);
}

$membershipExpires = (new DateTime('+1 year'))->format('Y-m-d H:i:s');
$hash = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

try {
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, api_token, role, membership_expires_at) VALUES (:name, :email, :password_hash, :api_token, "agent", :expires)');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password_hash' => $hash,
        ':api_token' => $token,
        ':expires' => $membershipExpires,
    ]);
} catch (PDOException $e) {
    json_response(['error' => 'Could not create agent', 'details' => $e->getMessage()], 400);
}

json_response([
    'message' => 'Agent created',
    'api_token' => $token,
    'membership_expires_at' => $membershipExpires,
]);
