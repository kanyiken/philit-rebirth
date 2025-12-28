<?php
require_once __DIR__ . '/../config.php';

// Expire outdated listings.
$now = (new DateTime('now'))->format('Y-m-d H:i:s');
db()->prepare('UPDATE listings SET status = "expired" WHERE expires_at < :now AND status = "active"')->execute([':now' => $now]);

$stmt = db()->query('SELECT l.id, l.title, l.description, l.price, l.location, l.image_url, l.expires_at, u.name AS seller_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.status = "active" ORDER BY l.posted_at DESC');
$listings = $stmt->fetchAll();

json_response(['data' => $listings]);
