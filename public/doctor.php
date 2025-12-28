<?php
require_once __DIR__ . '/../health/checks.php';
$health = run_health_checks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pipii Health Doctor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background:#0b1224; color:#e5e7eb; margin:0; }
        .wrap { max-width: 960px; margin: 40px auto; padding: 24px; background: #111827; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,0.35); border: 1px solid #1f2937; }
        .badge { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
        .ok { background: #10b98133; color: #34d399; border: 1px solid #10b98155; }
        .warn { background: #f59e0b33; color: #fbbf24; border: 1px solid #f59e0b55; }
        .error { background: #ef444433; color: #f87171; border: 1px solid #ef444455; }
        .card { background:#0f172a; padding:16px; border-radius:14px; border:1px solid #1f2937; margin-bottom:12px; }
        .title { margin:0 0 8px 0; font-size: 18px; font-weight:700; }
        .subtitle { margin:0; color:#cbd5e1; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1 style="margin:0 0 10px 0; font-size:28px;">Pipii Systems Doctor</h1>
        <p style="margin:0 0 18px 0; color:#cbd5e1;">Environment: <strong><?php echo htmlspecialchars($health['environment']); ?></strong> — Lap time: <?php echo $health['duration_ms']; ?>ms</p>
        <div class="badge <?php echo $health['status']; ?>">Overall: <?php echo strtoupper($health['status']); ?></div>
        <div style="margin-top:18px;">
            <?php foreach ($health['checks'] as $check): ?>
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <p class="title"><?php echo ucfirst($check['name']); ?></p>
                        <span class="badge <?php echo $check['status']; ?>"><?php echo strtoupper($check['status']); ?></span>
                    </div>
                    <p class="subtitle">User view: <?php echo htmlspecialchars($check['user_message']); ?></p>
                    <p class="subtitle">Dev view: <?php echo htmlspecialchars($check['dev_message']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
