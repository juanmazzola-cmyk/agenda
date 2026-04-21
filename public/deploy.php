<?php

define('DEPLOY_TOKEN', 'andrea2025');

if (($_GET['token'] ?? '') !== DEPLOY_TOKEN) {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain');

$base = dirname(__DIR__);
$git  = trim(shell_exec('which git 2>&1') ?: '/usr/bin/git');

echo "=== DEPLOY ===\n";
echo "Base: $base\n";
echo "Git:  $git\n";

function run($cmd) {
    $out = shell_exec($cmd . ' 2>&1');
    echo $out ?: "(sin salida)\n";
}

echo "\n-- git reset --hard HEAD --\n";
run("cd " . escapeshellarg($base) . " && $git reset --hard HEAD");

echo "\n-- git clean -fd --\n";
run("cd " . escapeshellarg($base) . " && $git clean -fd");

echo "\n-- git pull origin main --\n";
run("cd " . escapeshellarg($base) . " && $git pull origin main");

echo "\n=== DONE ===\n";
