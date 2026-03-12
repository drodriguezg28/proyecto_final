<?php
if (!isset($datosClima) && !isset($mensajeError)) {
    require_once '../models/MeteoModel.php';
    require_once '../models/HistorialModel.php';
    require_once '../controllers/MeteoController.php';

    $ciudad = htmlspecialchars(trim($_GET['ciudad'] ?? ''));
    $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
    $lon = isset($_GET['lon']) ? $_GET['lon'] : null;

    if (!empty($ciudad)) {
        $controlador = new MeteoController(
            new MeteoModel(getenv('API_KEY')),
            new HistorialModel()
        );

        $controlador->procesarBusqueda($ciudad, 'actual', $lat, $lon);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiempo Actual — <?= htmlspecialchars($ciudad ?? 'Buscador') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php require_once '../views/header.php'; ?>


    <div class="container">

        <!--Error-->
        <?php if (isset($mensajeError)): ?>
            <?= $mensajeError ?>
        
        <?php elseif (isset($datosClima) && $datosClima !== null): ?>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <h2 class="text-center mb-4">
                        Tiempo actual en <span class="fw-bold"><?= htmlspecialchars(ucfirst($ciudad??'')) ?></span>
                    </h2>

                    <!--Meteorología-->
                    <div class="weather-card p-5 text-center shadow-lg">
                        <img src="https://openweathermap.org/img/wn/<?= $datosClima['icono'] ?>@2x.png"
                            alt="<?= htmlspecialchars($datosClima['descripcion']) ?>" width="100">
                        <div class="display-1 fw-bold mt-2"><?= round($datosClima['temperatura']) ?>°C</div>
                        <p class="fs-5 text-capitalize mt-2 mb-0"><?= htmlspecialchars($datosClima['descripcion']) ?></p>
                    </div>
                    <!--Botones-->
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-search"></i> Nueva búsqueda
                        </a>
                        <a href="horas.php?ciudad=<?= urlencode($ciudad)?>" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-clock"></i> Por horas
                        </a>
                        <a href="semana.php?ciudad=<?= urlencode($ciudad)?>" class="btn btn-outline-secondary">
                            <i class="bi bi-calendar-week"></i> Semana
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-cloud-sun display-1 text-secondary"></i>
                <h3 class="mt-3 text-muted">Busca una ciudad</h3>
                <a href="index.php" class="btn btn-secondary mt-3">Ir al buscador</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>