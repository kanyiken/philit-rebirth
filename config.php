<?php
// Basic configuration and shared utilities for the Pipii.co.ke platform.

// Toggle between environments with a single variable when testing: set PIPII_ENV=local.
// Everything else is pre-wired below for convenience.
const APP_ENV = 'local'; // default

function environment(): string
{
    return getenv('PIPII_ENV') ?: APP_ENV;
}

$CONFIG = [
    'prod' => [
        'db_host' => getenv('PIPII_DB_HOST') ?: 'localhost',
        'db_name' => getenv('PIPII_DB_NAME') ?: 'pipii',
        'db_user' => getenv('PIPII_DB_USER') ?: 'pipii_user',
        'db_pass' => getenv('PIPII_DB_PASS') ?: 'change_me',
    ],
    'local' => [
        'db_host' => getenv('PIPII_DB_HOST') ?: 'localhost',
        'db_name' => getenv('PIPII_DB_NAME') ?: 'pipii',
        'db_user' => getenv('PIPII_DB_USER') ?: 'root',
        'db_pass' => getenv('PIPII_DB_PASS') ?: 'root',
    ],
];

// A very simple admin key gate for the prototype environment.
// In production, use proper authentication and secret storage.
const ADMIN_API_KEY = 'super-secret-admin-key';

function db(): PDO {
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $env = environment();
    $cfg = $GLOBALS['CONFIG'][$env] ?? $GLOBALS['CONFIG']['prod'];

    $dsn = 'mysql:host=' . $cfg['db_host'] . ';dbname=' . $cfg['db_name'] . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], $options);
    return $pdo;
}

function json_response(array $payload, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function require_admin(): void {
    $headerKey = $_SERVER['HTTP_X_ADMIN_KEY'] ?? '';
    if ($headerKey !== ADMIN_API_KEY) {
        json_response(['error' => 'Unauthorized'], 401);
    }
}

function authenticated_user(): ?array {
    if (!isset($_SERVER['HTTP_X_USER_EMAIL'], $_SERVER['HTTP_X_USER_TOKEN'])) {
        return null;
    }

    $email = $_SERVER['HTTP_X_USER_EMAIL'];
    $token = $_SERVER['HTTP_X_USER_TOKEN'];

    $stmt = db()->prepare('SELECT id, name, email, role, membership_expires_at FROM users WHERE email = :email AND api_token = :token');
    $stmt->execute([':email' => $email, ':token' => $token]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function require_user(): array {
    $user = authenticated_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
    }
    return $user;
}

function ensure_membership_limits(array $user): void {
    if ($user['role'] !== 'agent') {
        return;
    }

    // Check active membership window
    $expires = $user['membership_expires_at'] ? new DateTime($user['membership_expires_at']) : null;
    $now = new DateTime('now');

    if (!$expires || $expires < $now) {
        json_response(['error' => 'Membership expired. Please renew.'], 402);
    }

    // Ensure they have not exceeded 30 listings
    $stmt = db()->prepare('SELECT COUNT(*) AS total FROM listings WHERE user_id = :user_id AND status = "active"');
    $stmt->execute([':user_id' => $user['id']]);
    $total = (int) $stmt->fetchColumn();

    if ($total >= 30) {
        json_response(['error' => 'Listing limit reached (30).'], 409);
    }
}
