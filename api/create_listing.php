<?php
require_once __DIR__ . '/../config.php';

$user = require_user();

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
$location = trim($_POST['location'] ?? '');
$imageUrl = trim($_POST['image_url'] ?? '');

if (!$title || !$description || !$price || !$location) {
    json_response(['error' => 'Title, description, price, and location are required'], 422);
}

// Enforce membership + limits for agents.
ensure_membership_limits($user);

$expiresAt = (new DateTime('+1 month'))->format('Y-m-d H:i:s');

$stmt = db()->prepare('INSERT INTO listings (user_id, title, description, price, location, image_url, expires_at) VALUES (:user_id, :title, :description, :price, :location, :image_url, :expires_at)');
$stmt->execute([
    ':user_id' => $user['id'],
    ':title' => $title,
    ':description' => $description,
    ':price' => $price,
    ':location' => $location,
    ':image_url' => $imageUrl ?: null,
    ':expires_at' => $expiresAt,
]);

json_response([
    'message' => 'Listing created',
    'expires_at' => $expiresAt,
]);
