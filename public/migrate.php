<?php

define('CLAVE_SECRETA', 'andrea2025');

if (($_GET['clave'] ?? '') !== CLAVE_SECRETA) {
    http_response_code(403);
    die('Acceso denegado.');
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exitCode = Artisan::call('migrate', ['--force' => true]);
$output = Artisan::output();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Migraciones</title>
    <style>
        body { font-family: monospace; background: #1a1a2e; color: #e0e0e0; padding: 2rem; }
        h1 { color: #f472b6; margin-bottom: 1.5rem; font-size: 1.2rem; }
        pre { background: #0f0f1a; border: 1px solid #333; border-radius: 8px; padding: 1.5rem;
              white-space: pre-wrap; word-break: break-all; line-height: 1.6; font-size: 0.9rem; }
        .ok { color: #4ade80; }
        .warn { color: #facc15; }
    </style>
</head>
<body>
    <h1>php artisan migrate --force</h1>
    <pre><?php
        if ($output) {
            echo htmlspecialchars($output);
        } else {
            echo '<span class="warn">Sin salida.</span>';
        }
    ?></pre>
    <p style="color:<?php echo $exitCode === 0 ? '#4ade80' : '#f87171'; ?>;font-size:0.85rem;margin-top:1rem;">
        Código de salida: <?php echo $exitCode; ?>
    </p>
    <p style="color:#6b7280;font-size:0.8rem;margin-top:0.5rem;">
        Eliminá este archivo del servidor cuando termines.
    </p>
</body>
</html>
