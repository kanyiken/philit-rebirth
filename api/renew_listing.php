<?php
require_once __DIR__ . '/../config.php';

$user = require_user();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$listingId = (int) ($input['listing_id'] ?? 0);

if ($listingId <= 0) {
    json_response(['error' => 'listing_id is required'], 422);
}

$stmt = db()->prepare('SELECT id, user_id, status FROM listings WHERE id = :id');
$stmt->execute([':id' => $listingId]);
$listing = $stmt->fetch();

if (!$listing) {
    json_response(['error' => 'Listing not found'], 404);
}

if ($user['role'] !== 'admin' && (int) $listing['user_id'] !== (int) $user['id']) {
    json_response(['error' => 'Forbidden'], 403);
}

$expiresAt = (new DateTime('+1 month'))->format('Y-m-d H:i:s');
$status = 'active';

$update = db()->prepare('UPDATE listings SET expires_at = :expires_at, status = :status WHERE id = :id');
$update->execute([
    ':expires_at' => $expiresAt,
    ':status' => $status,
    ':id' => $listingId,
]);

json_response([
    'message' => 'Listing renewed for 1 month',
    'expires_at' => $expiresAt,
]);
