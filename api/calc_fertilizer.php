<?php
// Endpoint sencillo para cálculo de fertilización vía AJAX
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

$cropId = isset($_POST['crop_id']) ? (int) $_POST['crop_id'] : 0;
$numPlants = isset($_POST['num_plants']) ? (float) $_POST['num_plants'] : 0;
$areaM2 = isset($_POST['area_m2']) ? (float) $_POST['area_m2'] : 0;

if ($cropId <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Cultivo no válido']);
    exit;
}

$fert = getFertilizationForCrop($cropId);

$results = [];
foreach ($fert as $f) {
    $perPlant = (float) $f['amount_per_plant'];
    $perM2 = (float) $f['amount_per_m2'];
    $total = $perPlant * $numPlants + $perM2 * $areaM2;
    $results[] = [
        'name' => $f['name'],
        'unit' => $f['unit'],
        'total' => $total,
    ];
}

echo json_encode([
    'ok' => true,
    'results' => $results,
]);
