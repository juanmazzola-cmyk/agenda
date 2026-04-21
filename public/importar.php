<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('CLAVE_SECRETA', 'andrea2025');

if (($_GET['clave'] ?? '') !== CLAVE_SECRETA) {
    http_response_code(403);
    die('Acceso denegado.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Bootstrap OK\n"; flush();

$jsonPath = __DIR__ . '/agenda_andrea_backup.json';
if (!file_exists($jsonPath)) die('JSON no encontrado.');

$data = json_decode(file_get_contents($jsonPath), true);
if (!$data) die('JSON inválido.');

echo "JSON OK\n"; flush();

// Limpiar en orden correcto por FK
DB::table('turnos')->delete();
DB::table('clientes')->delete();
DB::table('tratamientos')->delete();
echo "Tablas limpiadas\n"; flush();

$now = now()->toDateTimeString();

// Clientes
$clienteMap = [];
foreach ($data['clients'] as $c) {
    $celular = $c['phone'];
    if (!str_starts_with($celular, '+54')) {
        $celular = '+54' . ltrim($celular, '0');
    }
    $id = DB::table('clientes')->insertGetId([
        'nombre'     => $c['firstName'],
        'apellido'   => $c['lastName'],
        'celular'    => $celular,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $clienteMap[$c['id']] = $id;
}
echo "Clientes: " . count($clienteMap) . "\n"; flush();

// Tratamientos
$tratamientoMap = [];
foreach ($data['services'] as $s) {
    $id = DB::table('tratamientos')->insertGetId([
        'nombre'     => $s['name'],
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $tratamientoMap[$s['id']] = $id;
}
echo "Tratamientos: " . count($tratamientoMap) . "\n"; flush();

// Turnos
$insertados = 0;
$omitidos   = 0;
foreach ($data['appointments'] as $i => $a) {
    $cid = $clienteMap[$a['clientId']]      ?? null;
    $tid = $tratamientoMap[$a['serviceId']] ?? null;
    if (!$cid || !$tid) { $omitidos++; continue; }

    try {
        DB::table('turnos')->insert([
            'cliente_id'     => $cid,
            'tratamiento_id' => $tid,
            'fecha'          => $a['date'],
            'hora'           => $a['time'],
            'valor'          => is_numeric($a['price'] ?? null) ? $a['price'] : 0,
            'cobrado'        => !empty($a['paid']) ? 1 : 0,
            'notas'          => $a['notes'] ?? null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $insertados++;
    } catch (\Throwable $e) {
        echo "ERROR turno $i: " . $e->getMessage() . "\n"; flush();
    }
}

echo "Turnos insertados: $insertados\n";
if ($omitidos) echo "Turnos omitidos: $omitidos\n";
echo "LISTO\n"; flush();
