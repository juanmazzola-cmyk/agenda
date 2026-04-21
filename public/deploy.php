<?php

define('DEPLOY_TOKEN', 'andrea2025');

if (($_GET['token'] ?? '') !== DEPLOY_TOKEN) {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain');

$base = dirname(__DIR__);

echo "=== DEPLOY ===\n";

echo "\n-- git reset --hard HEAD --\n";
echo shell_exec("cd " . escapeshellarg($base) . " && git reset --hard HEAD 2>&1");

echo "\n-- git pull origin main --\n";
echo shell_exec("cd " . escapeshellarg($base) . " && git pull origin main 2>&1");

echo "\n=== DONE ===\n";
