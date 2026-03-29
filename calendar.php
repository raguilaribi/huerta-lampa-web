<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$monthParam = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');
if ($monthParam < 1 || $monthParam > 12) {
    $monthParam = (int) date('n');
}
$cropIdParam = isset($_GET['crop']) ? (int) $_GET['crop'] : 0;

$monthNames = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$crops = getAllCrops();
$actions = getActionsForMonth($monthParam, $cropIdParam ?: null);
?>
<h1 class="h3 mb-3">Calendario anual de labores</h1>
<p>Filtra por mes y por especie de cultivo para ver qué tareas recomienda el plan anual de huerta en Lampa. Puedes usar esta vista como base para planificar tus semanas.</p>

<form class="row g-3 mb-3" method="get" id="calendar-filter">
  <div class="col-md-4">
    <label class="form-label">Mes</label>
    <select name="month" id="month" class="form-select">
      <?php foreach ($monthNames as $m => $label): ?>
        <option value="<?php echo $m; ?>" <?php if ($m === $monthParam) echo 'selected'; ?>><?php echo $label; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Cultivo</label>
    <select name="crop" id="crop" class="form-select">
      <option value="0">Todos</option>
      <?php foreach ($crops as $c): ?>
        <option value="<?php echo (int)$c['id']; ?>" <?php if ((int)$c['id'] === $cropIdParam) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4 d-flex align-items-end">
    <button type="submit" class="btn btn-success me-2"><i class="bi bi-funnel"></i> Aplicar filtros</button>
    <button type="button" id="btn-week-highlight" class="btn btn-outline-secondary"><i class="bi bi-calendar-week"></i> Resaltar semana actual</button>
  </div>
</form>

<?php if (!$actions): ?>
  <div class="alert alert-info">No hay acciones registradas para los filtros seleccionados.</div>
<?php else: ?>
  <?php
  $currentWeek = (int) ceil(((int) date('j')) / 7);
  $grouped = [];
  foreach ($actions as $a) {
      $grouped[$a['crop_id']][] = $a;
  }
  foreach ($grouped as $cid => $items):
      $cropName = $items[0]['crop_name'];
  ?>
    <div class="card mb-3" data-crop-id="<?php echo (int)$cid; ?>">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span><i class="bi bi-flower3"></i> <strong><?php echo htmlspecialchars($cropName); ?></strong></span>
        <a href="/crop.php?slug=<?php echo urlencode(getCropById((int)$cid)['slug']); ?>" class="btn btn-sm btn-outline-success">Ver ficha</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0 align-middle">
          <thead>
            <tr>
              <th style="width: 12%">Tipo</th>
              <th>Acción</th>
              <th style="width: 25%">Meses recomendados</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $a): ?>
            <?php
              $weekClass = '';
              if (!empty($a['week_of_month']) && (int)$a['week_of_month'] === $currentWeek) {
                  $weekClass = 'table-success week-highlight';
              }
            ?>
            <tr class="<?php echo $weekClass; ?>">
              <td><span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($a['action_type']); ?></span></td>
              <td>
                <strong><?php echo htmlspecialchars($a['title']); ?></strong><br>
                <small class="text-muted"><?php echo htmlspecialchars($a['description']); ?></small>
              </td>
              <td><?php echo $monthNames[$a['month_start']] . ' – ' . $monthNames[$a['month_end']]; ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php';
