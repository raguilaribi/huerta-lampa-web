<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$crop = $slug !== '' ? getCropBySlug($slug) : null;

if (!$crop) {
    echo '<div class="alert alert-danger">Cultivo no encontrado.</div>';
    include __DIR__ . '/partials/footer.php';
    exit;
}

$actions = getActionsForCrop((int)$crop['id']);
$fert = getFertilizationForCrop((int)$crop['id']);

$monthNames = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>
<a href="/crops.php" class="btn btn-link mb-3"><i class="bi bi-arrow-left"></i> Volver al listado</a>

<h1 class="h3 mb-1"><?php echo htmlspecialchars($crop['name']); ?></h1>
<p class="text-muted mb-3"><?php echo htmlspecialchars($crop['category']); ?></p>
<?php if (!empty($crop['description'])): ?>
  <p><?php echo htmlspecialchars($crop['description']); ?></p>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-7">
    <h2 class="h5">Calendario anual de labores</h2>
    <?php if (!$actions): ?>
      <div class="alert alert-info">Aún no hay acciones registradas para este cultivo.</div>
    <?php else: ?>
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Acción</th>
            <th>Meses recomendados</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($actions as $a): ?>
          <tr>
            <td>
              <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($a['action_type']); ?></span>
            </td>
            <td>
              <strong><?php echo htmlspecialchars($a['title']); ?></strong><br>
              <small class="text-muted"><?php echo htmlspecialchars($a['description']); ?></small>
            </td>
            <td><?php echo $monthNames[$a['month_start']] . ' – ' . $monthNames[$a['month_end']]; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="col-lg-5">
    <h2 class="h5">Calculadora de fertilización</h2>
    <p class="small">Ingresa el número de plantas y/o la superficie en metros cuadrados para estimar las cantidades de insumos sugeridas para este cultivo. Los valores son referenciales y pueden ajustarse según la experiencia de tu huerta.</p>

    <form id="fert-form" class="card card-body mb-3">
      <div class="mb-2">
        <label for="num_plants" class="form-label">Número de plantas</label>
        <input type="number" min="0" step="1" class="form-control" id="num_plants" name="num_plants" value="10">
      </div>
      <div class="mb-2">
        <label for="area_m2" class="form-label">Superficie (m²)</label>
        <input type="number" min="0" step="0.1" class="form-control" id="area_m2" name="area_m2" value="5">
      </div>
      <input type="hidden" id="crop_id" value="<?php echo (int)$crop['id']; ?>">
      <button type="button" id="btn-calc" class="btn btn-success"><i class="bi bi-calculator"></i> Calcular insumos</button>
    </form>

    <div id="fert-results" class="d-none">
      <h3 class="h6">Resultados estimados</h3>
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Insumo</th>
            <th>Cantidad estimada</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($fert as $f): ?>
          <tr data-fert-row
              data-unit="<?php echo htmlspecialchars($f['unit']); ?>"
              data-name="<?php echo htmlspecialchars($f['name']); ?>"
              data-per-plant="<?php echo (float)$f['amount_per_plant']; ?>"
              data-per-m2="<?php echo (float)$f['amount_per_m2']; ?>">
            <td>
              <strong><?php echo htmlspecialchars($f['name']); ?></strong><br>
              <small class="text-muted"><?php echo htmlspecialchars($f['notes']); ?></small>
            </td>
            <td class="fert-amount text-end"></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <p class="small text-muted">Las dosis están expresadas en las unidades indicadas (por planta o por m²). Ajusta según la fertilidad real de tu suelo y la respuesta de las plantas.</p>
    </div>

    <?php if (!$fert): ?>
      <div class="alert alert-info">Aún no se han definido insumos de fertilización para este cultivo en la base de datos.</div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php';
