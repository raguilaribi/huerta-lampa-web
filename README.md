# Huerta Lampa Web

Aplicación web minimalista en PHP + SQLite para planificar una huerta familiar en Lampa (Región Metropolitana, Chile).

## Características

- Calendario anual de labores por cultivo (poda, fertilización, siembra, trasplante, cosecha y tratamientos).
- Panel de inicio que muestra el plan para la semana y el mes actual, agrupado por especie.
- Fichas de cada cultivo con su calendario y una calculadora de insumos de fertilización.
- Datos semilla basados en un plan anual de huerta para Lampa (clima mediterráneo semiárido de Chile central).
- Frontend con Bootstrap 5, Bootstrap Icons y jQuery.
- Base de datos SQLite auto-inicializable (sin pasos manuales).

## Requisitos

- PHP 8.1+ con extensión PDO SQLite habilitada.

## Instalación rápida

```bash
git clone https://github.com/raguilaribi/huerta-lampa-web.git
cd huerta-lampa-web
php -S localhost:8000
```

Luego abre <http://localhost:8000> en tu navegador.

La primera vez que ingreses, la aplicación creará automáticamente el archivo `data/huerta.sqlite` con las tablas y algunos cultivos y labores de ejemplo, basados en el calendario técnico para Lampa.

## Estructura de carpetas

- `config.php`: configuración básica.
- `db.php`: conexión SQLite, creación de esquema y datos semilla.
- `index.php`: panel con el plan del mes y semana actual.
- `crops.php`: listado de cultivos.
- `crop.php`: ficha de un cultivo con calendario y calculadora de fertilización.
- `calendar.php`: calendario anual filtrable por mes y cultivo.
- `api/calc_fertilizer.php`: endpoint AJAX para el cálculo de insumos.
- `partials/`: cabecera y pie de página compartidos.
- `assets/js/main.js`: lógica frontend con jQuery.
- `assets/css/custom.css`: estilos personalizados.

## Personalización

- Puedes editar `db.php` para ajustar cultivos, labores y dosis referenciales de fertilización a la realidad de tu huerta.
- Agrega más cultivos insertando filas en las tablas `crops`, `actions` y `fertilization_inputs`.
- Integra la información completa de tu plan de huerta (por ejemplo, a partir de un archivo Markdown) ampliando los datos semilla.
