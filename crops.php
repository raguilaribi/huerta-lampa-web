<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/partials/header.php';

$crops = getAllCrops();
?>
<h1 class="h3 mb-3">Cultivos de tu huerta</h1>
<p>Esta sección organiza las especies de tu huerta (frutales y hortalizas) y permite acceder a una ficha con calendario de labores y calculadora de fertilización para cada una.</p>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Filtrar por categoría</label>
    <select id="filter-category" class="form-select">
      <option value="">Todas</option>
      <option value="Frutal">Frutales</option>
      <option value="Hortaliza">Hortalizas</option>
    </select>
  </div>
</div>

<div class="row" id="crops-list">
<?php foreach ($crops as $crop): ?>
  <div class="col-md-6 col-lg-4 mb-3 crop-card" data-category="<?php echo htmlspecialchars(substr($crop['category'], 0, 7)); ?>">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 card-title"><?php echo htmlspecialchars($crop['name']); ?></h2>
        <p class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($crop['category']); ?></p>
        <?php if (!empty($crop['description'])): ?>
          <p class="card-text small"><?php echo htmlspecialchars($crop['description']); ?></p>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/crop.php?slug=<?php echo urlencode($crop['slug']); ?>" class="btn btn-sm btn-success">Ver ficha</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/partials/footer.php';
