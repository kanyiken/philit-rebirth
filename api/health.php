<?php
require_once __DIR__ . '/../health/checks.php';

$health = run_health_checks();

$code = match ($health['status']) {
    'ok' => 200,
    'warn' => 200,
    default => 503,
};

json_response($health, $code);
