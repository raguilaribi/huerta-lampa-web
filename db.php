<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

const DB_PATH = __DIR__ . '/data/huerta.sqlite';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $needInit = !file_exists(DB_PATH);
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($needInit) {
            initSchema($pdo);
        }
    }
    return $pdo;
}

function initSchema(PDO $pdo): void {
    $schemaSql = <<<SQL
CREATE TABLE crops (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  category TEXT NOT NULL,
  description TEXT
);

CREATE TABLE actions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  crop_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  action_type TEXT NOT NULL,
  description TEXT,
  month_start INTEGER NOT NULL,
  month_end INTEGER NOT NULL,
  week_of_month INTEGER,
  FOREIGN KEY (crop_id) REFERENCES crops(id)
);

CREATE TABLE fertilization_inputs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  crop_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  unit TEXT NOT NULL,
  amount_per_plant REAL DEFAULT 0,
  amount_per_m2 REAL DEFAULT 0,
  notes TEXT,
  FOREIGN KEY (crop_id) REFERENCES crops(id)
);
SQL;
    $pdo->exec($schemaSql);

    // Seed crops
    $crops = [
        ['Ciruelo','ciruelo','Frutal de carozo','Ciruelo para clima mediterráneo semiárido de Lampa.'],
        ['Durazno conservero','durazno-conservero','Frutal de carozo','Durazno para conserva, con poda invernal intensa.'],
        ['Vid de mesa blanca','vid-mesa-blanca','Vid','Vid de mesa blanca para parrón o espaldera.'],
        ['Limonero Eureka','limonero-eureka','Cítrico','Limonero sensible a heladas juveniles en Lampa.'],
        ['Tomate','tomate','Hortaliza de fruto','Tomate de primavera–verano para la huerta.'],
        ['Pimentón','pimenton','Hortaliza de fruto','Pimentón dulce de verano.'],
        ['Ají cristal','aji-cristal','Hortaliza de fruto','Ají cristal para consumo fresco o seco.'],
        ['Lechuga','lechuga','Hortaliza de hoja','Lechuga para siembras escalonadas todo el año.'],
        ['Acelga','acelga','Hortaliza de hoja','Acelga de corte continuo.'],
        ['Zanahoria','zanahoria','Hortaliza de raíz','Zanahoria de otoño–invierno.'],
    ];
    $stmt = $pdo->prepare('INSERT INTO crops (name,slug,category,description) VALUES (?,?,?,?)');
    foreach ($crops as $c) {
        $stmt->execute($c);
    }

    // Map slugs to ids
    $slugToId = [];
    $rows = $pdo->query('SELECT id, slug FROM crops')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $slugToId[$r['slug']] = (int)$r['id'];
    }

    // Seed actions (very resumido basado en el plan de Lampa)
    $actions = [
        // Ciruelo
        [$slugToId['ciruelo'], 'Poda de invierno', 'poda', 'Poda de formación y fructificación en reposo invernal, abriendo la copa y eliminando ramas cruzadas o enfermas.', 7, 8, null],
        [$slugToId['ciruelo'], 'Fertilización de postcosecha', 'fertilizacion', 'Aplicar compost y abono rico en potasio después de la cosecha y antes de la caída total de hojas.', 3, 4, null],
        [$slugToId['ciruelo'], 'Aplicación de cobre', 'tratamiento', 'Aplicación de caldo cúprico al 50 % de caída de hojas para prevenir cloca y tiro de munición.', 4, 5, null],

        // Durazno conservero
        [$slugToId['durazno-conservero'], 'Poda de invierno', 'poda', 'Poda intensa de ramas de 2–3 años para renovar madera productiva.', 7, 8, null],
        [$slugToId['durazno-conservero'], 'Raleo de frutos', 'manejo', 'Raleo de frutos en primavera para mejorar calibre y sanidad.', 10, 11, null],

        // Vid de mesa blanca
        [$slugToId['vid-mesa-blanca'], 'Poda invernal de vid', 'poda', 'Poda de cargadores y pitones en parrón o espaldera durante el reposo.', 7, 8, null],
        [$slugToId['vid-mesa-blanca'], 'Deshoje ligero en racimos', 'manejo', 'Deshoje ligero alrededor de racimos para mejorar ventilación y color.', 12, 1, null],

        // Limonero Eureka
        [$slugToId['limonero-eureka'], 'Poda de limpieza', 'poda', 'Poda suave de ramas secas, cruzadas y chupones vigorosos a finales de invierno.', 8, 9, null],
        [$slugToId['limonero-eureka'], 'Fertilización de primavera', 'fertilizacion', 'Primera fracción de fertilizante completo para cítricos al inicio de brotación.', 9, 10, null],

        // Tomate
        [$slugToId['tomate'], 'Siembra de almácigos protegidos', 'siembra', 'Siembra en almácigos bajo abrigo para trasplante de primavera.', 8, 9, null],
        [$slugToId['tomate'], 'Trasplante a suelo', 'trasplante', 'Trasplante al suelo cuando pasan las heladas y el suelo está templado.', 10, 11, null],
        [$slugToId['tomate'], 'Deschuponado y tutorado', 'manejo', 'Eliminación de brotes laterales y tutorado para conducción en 1–2 tallos.', 11, 1, null],
        [$slugToId['tomate'], 'Fertilización en floración', 'fertilizacion', 'Aporte de fertilizante con mayor potasio al inicio de floración y cuaja.', 11, 12, null],

        // Pimentón
        [$slugToId['pimenton'], 'Almácigos protegidos', 'siembra', 'Siembra en almácigos protegidos para trasplante de primavera–verano.', 8, 9, null],
        [$slugToId['pimenton'], 'Trasplante a suelo', 'trasplante', 'Trasplante a terreno definitivo cuando el suelo está templado.', 10, 11, null],
        [$slugToId['pimenton'], 'Riegos regulares y abonado', 'fertilizacion', 'Riegos frecuentes y abonado balanceado en crecimiento y floración.', 11, 1, null],

        // Ají cristal
        [$slugToId['aji-cristal'], 'Siembra en almácigo', 'siembra', 'Siembra de ají en almácigo con buena temperatura, evitando heladas.', 8, 10, null],
        [$slugToId['aji-cristal'], 'Trasplante y protección', 'trasplante', 'Trasplante a suelo con protección frente a noches frías.', 10, 11, null],
        [$slugToId['aji-cristal'], 'Cosecha de ají verde', 'cosecha', 'Cosecha de ají verde para consumo fresco.', 1, 2, null],

        // Lechuga
        [$slugToId['lechuga'], 'Siembra escalonada', 'siembra', 'Siembras escalonadas para disponer de lechugas todo el año.', 3, 11, null],
        [$slugToId['lechuga'], 'Aporte ligero de nitrógeno', 'fertilizacion', 'Aporte de humus o té de compost a mitad de ciclo.', 4, 9, null],

        // Acelga
        [$slugToId['acelga'], 'Siembra de otoño–invierno', 'siembra', 'Siembra de acelga para producción continua en la temporada fría.', 3, 6, null],
        [$slugToId['acelga'], 'Corte de hojas externas', 'cosecha', 'Cosecha continua por corte de hojas externas.', 5, 10, null],

        // Zanahoria
        [$slugToId['zanahoria'], 'Siembra directa de otoño', 'siembra', 'Siembra directa en suelo suelto y profundo para raíces rectas.', 3, 5, null],
        [$slugToId['zanahoria'], 'Raleo de plántulas', 'manejo', 'Raleo oportuno para dar espacio a cada raíz.', 4, 6, null],
    ];

    $stmtA = $pdo->prepare('INSERT INTO actions (crop_id,title,action_type,description,month_start,month_end,week_of_month) VALUES (?,?,?,?,?,?,?)');
    foreach ($actions as $a) {
        $stmtA->execute($a);
    }

    // Seed fertilization inputs
    $fert = [
        [$slugToId['ciruelo'], 'Compost de otoño', 'kg/m2', 0, 3.0, 'Compost bien maduro alrededor de la proyección de la copa en postcosecha.'],
        [$slugToId['durazno-conservero'], 'Compost de otoño', 'kg/m2', 0, 3.0, 'Aporte orgánico de base tras la cosecha.'],
        [$slugToId['vid-mesa-blanca'], 'Compost de otoño', 'kg/m2', 0, 2.5, 'Aporte en toda la línea de plantación.'],
        [$slugToId['limonero-eureka'], 'Fertilizante cítricos primavera', 'g/planta', 250, 0, 'Dosis referencial por planta adulta, fraccionada en 2–3 aplicaciones.'],
        [$slugToId['tomate'], 'Compost de plantación', 'kg/m2', 0, 4.0, 'Incorporar al preparar el bancal.'],
        [$slugToId['tomate'], 'Fertilizante NPK equilibrio', 'g/planta', 40, 0, 'Aplicar a las 3–4 semanas del trasplante.'],
        [$slugToId['pimenton'], 'Compost de plantación', 'kg/m2', 0, 4.0, 'Suelo fértil y bien estructurado.'],
        [$slugToId['aji-cristal'], 'Compost de plantación', 'kg/m2', 0, 4.0, 'Suelo con buena materia orgánica.'],
        [$slugToId['lechuga'], 'Compost de fondo', 'kg/m2', 0, 3.0, 'Aporte antes de la siembra.'],
        [$slugToId['acelga'], 'Compost de fondo', 'kg/m2', 0, 3.0, 'Suelo fértil para cortes continuos.'],
        [$slugToId['zanahoria'], 'Compost maduro', 'kg/m2', 0, 2.0, 'Aplicar meses antes de la siembra, bien descompuesto.'],
    ];

    $stmtF = $pdo->prepare('INSERT INTO fertilization_inputs (crop_id,name,unit,amount_per_plant,amount_per_m2,notes) VALUES (?,?,?,?,?,?)');
    foreach ($fert as $f) {
        $stmtF->execute($f);
    }
}

function getAllCrops(): array {
    return db()->query('SELECT * FROM crops ORDER BY category, name')->fetchAll(PDO::FETCH_ASSOC);
}

function getCropById(int $id): ?array {
    $stmt = db()->prepare('SELECT * FROM crops WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function getCropBySlug(string $slug): ?array {
    $stmt = db()->prepare('SELECT * FROM crops WHERE slug = ?');
    $stmt->execute([$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function getActionsForMonth(int $month, ?int $cropId = null): array {
    $sql = 'SELECT a.*, c.name AS crop_name FROM actions a JOIN crops c ON a.crop_id = c.id WHERE a.month_start <= :m AND a.month_end >= :m';
    $params = [':m' => $month];
    if ($cropId !== null) {
        $sql .= ' AND a.crop_id = :cid';
        $params[':cid'] = $cropId;
    }
    $sql .= ' ORDER BY c.name, a.action_type';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActionsForCrop(int $cropId): array {
    $stmt = db()->prepare('SELECT * FROM actions WHERE crop_id = ? ORDER BY month_start, action_type');
    $stmt->execute([$cropId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFertilizationForCrop(int $cropId): array {
    $stmt = db()->prepare('SELECT * FROM fertilization_inputs WHERE crop_id = ? ORDER BY name');
    $stmt->execute([$cropId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
