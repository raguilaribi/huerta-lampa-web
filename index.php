<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$month = (int) date('n');
$week = (int) ceil(((int) date('j')) / 7);
$monthNames = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$actions = getActionsForMonth($month, null);
?>
<div class="row mb-4">
  <div class="col-12 col-lg-8">
    <h1 class="h3 mb-3">Plan actual para tu huerta en Lampa</h1>
    <p>Este panel muestra las labores recomendadas para el mes actual (<strong><?php echo $monthNames[$month]; ?></strong>) agrupadas por cultivo, usando el plan anual diseñado para clima mediterráneo semiárido como el de Lampa.</p>
  </div>
  <div class="col-12 col-lg-4 text-lg-end">
    <div class="alert alert-success mb-0">
      <div><i class="bi bi-calendar-week"></i> Semana actual: <strong><?php echo $week; ?></strong></div>
      <div><i class="bi bi-calendar3"></i> Mes: <strong><?php echo $monthNames[$month]; ?></strong></div>
    </div>
  </div>
</div>

<?php
if (!$actions) {
    echo '<div class="alert alert-info">No hay acciones registradas para este mes. Puedes agregar más tareas editando la base de datos.</div>';
} else {
    $grouped = [];
    foreach ($actions as $a) {
        $grouped[$a['crop_name']][] = $a;
    }
    foreach ($grouped as $cropName => $items) {
        echo '<div class="card mb-3" data-crop="' . htmlspecialchars($cropName, ENT_QUOTES) . '">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div><i class="bi bi-flower3"></i> <strong>' . htmlspecialchars($cropName, ENT_QUOTES) . '</strong></div>
          </div>
          <div class="card-body">';
        echo '<ul class="list-group list-group-flush">';
        foreach ($items as $it) {
            $badgeClass = 'secondary';
            switch ($it['action_type']) {
                case 'poda': $badgeClass = 'warning'; break;
                case 'fertilizacion': $badgeClass = 'success'; break;
                case 'siembra': $badgeClass = 'primary'; break;
                case 'trasplante': $badgeClass = 'info'; break;
                case 'cosecha': $badgeClass = 'danger'; break;
                case 'tratamiento': $badgeClass = 'dark'; break;
            }
            echo '<li class="list-group-item">';
            echo '<span class="badge bg-' . $badgeClass . ' text-uppercase me-2">' . htmlspecialchars($it['action_type']) . '</span>';
            echo '<strong>' . htmlspecialchars($it['title']) . '</strong><br>';
            echo '<small class="text-muted">Meses: ' . $monthNames[$it['month_start']] . ' - ' . $monthNames[$it['month_end']] . '</small><br>';
            if (!empty($it['description'])) {
                echo '<span>' . htmlspecialchars($it['description']) . '</span>';
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div></div>';
    }
}
?>

<div class="mt-4">
  <h2 class="h5">Resumen por especie y calendario anual</h2>
  <p>Puedes profundizar en el detalle de cada cultivo, ver el calendario de todo el año y usar la calculadora de fertilización para dimensionar insumos según número de plantas o superficie:</p>
  <ul>
    <li><a href="<?php echo BASE_URL; ?>/crops.php">Listado de cultivos y fichas de manejo</a></li>
    <li><a href="<?php echo BASE_URL; ?>/calendar.php">Calendario anual de labores</a></li>
  </ul>
</div>

<?php include __DIR__ . '/partials/footer.php';
