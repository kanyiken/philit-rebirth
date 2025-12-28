<?php
require_once __DIR__ . '/../config.php';

/**
 * Run a series of health checks for Pipii.co.ke.
 *
 * Returns an associative array with:
 *  - environment: current environment key
 *  - duration_ms: total runtime
 *  - status: ok|warn|error
 *  - checks: list of check results
 */
function run_health_checks(): array
{
    $start = microtime(true);
    $checks = [];
    $statusRank = ['ok' => 0, 'warn' => 1, 'error' => 2];

    // Database connectivity
    $dbStatus = 'ok';
    $dbMessage = 'Database connected';
    try {
        $pdo = db();
        $pdo->query('SELECT 1');
    } catch (Throwable $e) {
        $dbStatus = 'error';
        $dbMessage = 'Engine stalled: DB unreachable';
        $checks[] = [
            'name' => 'database',
            'status' => $dbStatus,
            'user_message' => 'Our garage systems are offline. Please try again shortly.',
            'dev_message' => 'DB connection failed: ' . $e->getMessage(),
        ];
        return finalize_health($start, $checks);
    }

    // Tables present
    $tablesOk = true;
    $missingTables = [];
    $expected = ['users', 'listings', 'payments'];
    try {
        $result = $pdo->query('SHOW TABLES');
        $existing = $result->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($expected as $table) {
            if (!in_array($table, $existing, true)) {
                $tablesOk = false;
                $missingTables[] = $table;
            }
        }
    } catch (Throwable $e) {
        $tablesOk = false;
        $missingTables = $expected;
    }

    $checks[] = [
        'name' => 'database',
        'status' => $dbStatus,
        'user_message' => $tablesOk ? 'Garage systems are green.' : 'Our pit crew needs to install some parts.',
        'dev_message' => $tablesOk ? 'Connected and tables detected.' : 'Missing tables: ' . implode(', ', $missingTables),
    ];

    // Migration placeholder (no migration system yet)
    $checks[] = [
        'name' => 'migrations',
        'status' => $tablesOk ? 'ok' : 'warn',
        'user_message' => $tablesOk ? 'No pending upgrades for the cars.' : 'Setup required before the showroom opens.',
        'dev_message' => $tablesOk ? 'Migrations not implemented; ensure schema.sql applied.' : 'Apply schema.sql to provision missing tables.',
    ];

    // CDN reachability (Tailwind)
    $cdnStatus = 'ok';
    $cdnUser = 'Design fuel loaded.';
    $cdnDev = 'Tailwind CDN reachable.';
    $cdnUrl = 'https://cdn.tailwindcss.com';
    $reachable = url_reachable($cdnUrl);
    if (!$reachable) {
        $cdnStatus = 'warn';
        $cdnUser = 'Our styling fuel is delayed; the ride may look plain.';
        $cdnDev = 'Tailwind CDN unreachable; confirm internet access or host locally.';
    }
    $checks[] = [
        'name' => 'cdn',
        'status' => $cdnStatus,
        'user_message' => $cdnUser,
        'dev_message' => $cdnDev,
    ];

    // Security hints
    $securityStatus = 'ok';
    $securityUser = 'Safety harness secured.';
    $securityDev = 'HTTPS recommended for prod; admin key present.';
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) === 443);
    if (environment() === 'prod' && !$isHttps) {
        $securityStatus = 'warn';
        $securityUser = 'We suggest driving over HTTPS for safety.';
        $securityDev = 'Prod mode without HTTPS. Terminate TLS or set PIPII_ENV=local for dev.';
    }
    if (!ADMIN_API_KEY) {
        $securityStatus = 'error';
        $securityUser = 'Admin key missing. The showroom gate won’t open.';
        $securityDev = 'Define ADMIN_API_KEY in config.php or environment.';
    }
    $checks[] = [
        'name' => 'security',
        'status' => $securityStatus,
        'user_message' => $securityUser,
        'dev_message' => $securityDev,
    ];

    // Frontend assets quick check (hero images)
    $heroStatus = 'ok';
    $heroUser = 'Showroom visuals ready.';
    $heroDev = 'Unsplash accessible for hero slides.';
    $heroUrl = 'https://images.unsplash.com';
    if (!url_reachable($heroUrl)) {
        $heroStatus = 'warn';
        $heroUser = 'Car photos may not load; the lights are dimmed.';
        $heroDev = 'Unsplash unreachable; consider local image hosting.';
    }
    $checks[] = [
        'name' => 'assets',
        'status' => $heroStatus,
        'user_message' => $heroUser,
        'dev_message' => $heroDev,
    ];

    return finalize_health($start, $checks);
}

function url_reachable(string $url): bool
{
    $timeout = 1.5;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        return $errno === 0;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => $timeout,
        ],
    ]);
    try {
        $fp = @fopen($url, 'r', false, $context);
        if ($fp) {
            fclose($fp);
            return true;
        }
    } catch (Throwable $e) {
        return false;
    }
    return false;
}

function finalize_health(float $start, array $checks): array
{
    $rank = ['ok' => 0, 'warn' => 1, 'error' => 2];
    $overall = 'ok';
    foreach ($checks as $check) {
        if ($rank[$check['status']] > $rank[$overall]) {
            $overall = $check['status'];
        }
    }
    return [
        'environment' => environment(),
        'duration_ms' => (int) ((microtime(true) - $start) * 1000),
        'status' => $overall,
        'checks' => $checks,
    ];
}
