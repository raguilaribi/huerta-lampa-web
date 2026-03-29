$(function () {
  // Filtro simple de categoría en listado de cultivos
  $('#filter-category').on('change', function () {
    const val = $(this).val();
    $('.crop-card').each(function () {
      const cat = $(this).data('category') || '';
      if (!val) {
        $(this).show();
      } else if (val === 'Frutal' && cat.startsWith('Frutal')) {
        $(this).show();
      } else if (val === 'Hortaliza' && cat.startsWith('Hortali')) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Calculadora de fertilización con AJAX
  $('#btn-calc').on('click', function () {
    const cropId = parseInt($('#crop_id').val(), 10) || 0;
    const numPlants = parseFloat($('#num_plants').val()) || 0;
    const areaM2 = parseFloat($('#area_m2').val()) || 0;

    if (!cropId) {
      alert('Cultivo no válido');
      return;
    }

    $.post('api/calc_fertilizer.php', {
      crop_id: cropId,
      num_plants: numPlants,
      area_m2: areaM2
    }).done(function (resp) {
      if (!resp.ok) {
        alert(resp.error || 'Error en el cálculo');
        return;
      }
      const rows = $('#fert-results tbody tr[data-fert-row]');
      rows.each(function (index) {
        const r = resp.results[index];
        if (!r) return;
        const total = r.total;
        const cell = $(this).find('.fert-amount');
        if (total > 0) {
          cell.text(total.toFixed(2) + ' ' + r.unit);
        } else {
          cell.text('-');
        }
      });
      $('#fert-results').removeClass('d-none');
    }).fail(function () {
      alert('No se pudo contactar con el servidor.');
    });
  });

  // Resaltar semana actual en calendario anual (clase .week-highlight ya aplicada desde PHP)
  $('#btn-week-highlight').on('click', function () {
    $('.week-highlight').addClass('table-success');
    const first = $('.week-highlight').first();
    if (first.length) {
      $('html, body').animate({
        scrollTop: first.offset().top - 80
      }, 400);
    }
  });
});
