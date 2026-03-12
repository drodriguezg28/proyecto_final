<?php
if (!isset($datosClima) && !isset($mensajeError)) {
    require_once '../models/MeteoModel.php';
    require_once '../models/HistorialModel.php';
    require_once '../controllers/MeteoController.php';

    $ciudad = htmlspecialchars(trim($_GET['ciudad'] ?? ''));

    if (empty($ciudad)) {
        header("Location: index.php");
        exit;
    }

    $controlador = new MeteoController(
        new MeteoModel(getenv('API_KEY')),
        new HistorialModel()
    );

    $controlador->procesarBusqueda($ciudad, 'horas');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsión por Horas en <?= htmlspecialchars($ciudad ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php require_once '../views/header.php';?>

    <div class="container my-5 flex-grow-1">

        
        <!--Error-->
        <?php if (isset($mensajeError)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <div><?= $mensajeError ?></div>
            </div>

        <?php elseif (isset($datosClima) && !empty($datosClima['lista_horas'])): ?>

        <h2 class="text-center mb-2">
            Previsión por horas en <span class="fw-bold"><?= htmlspecialchars(ucfirst($ciudad??'')) ?></span>
        </h2>
        <p class="text-center text-muted mb-4">Próximas 24 horas (intervalos de 3h)</p>

        <!--Temperatura-->
        <div class="row g-3">
            <?php foreach ($datosClima['lista_horas'] as $item): ?>
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card hora-card border-0 rounded-4 p-3 text-center">
                        <div class="fw-bold fs-5"><?= $item['hora'] ?>h</div>
                        <img src="https://openweathermap.org/img/wn/<?= $item['icono'] ?>.png"
                                alt="<?= htmlspecialchars($item['descripcion']) ?>" width="50" class="mx-auto">
                        <div class="display-6 fw-bold"><?= $item['temperatura'] ?>°C</div>
                        <small class="text-muted text-capitalize"><?= htmlspecialchars($item['descripcion']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!--Grafica-->
        <?php
            $etiquetas    = array_column($datosClima['lista_horas'], 'hora');
            $temperaturas = array_column($datosClima['lista_horas'], 'temperatura');

            $config = [
                'type' => 'line',
                'data' => [
                    'labels'   => $etiquetas,
                    'datasets' => [[
                        'label'           => 'Temperatura (°C)',
                        'data'            => $temperaturas,
                        'borderColor'     => 'rgb(60, 110, 113)',
                        'backgroundColor' => 'rgba(60, 110, 113, 0.15)',
                        'pointBackgroundColor' => 'rgb(40, 75, 99)',
                        'fill'            => true,
                        'tension'         => 0.4,
                    ]]
                ],
                'options' => [
                    'plugins' => ['legend' => ['display' => true]],
                    'scales'  => ['y' => ['title' => ['display' => true, 'text' => '°C']]]
                ]
            ];
            $urlGrafica = "https://quickchart.io/chart?width=700&height=300&c=" . urlencode(json_encode($config));
        ?>

        <div class="card shadow-sm border-0 rounded-4 p-3 mb-4 text-center">
            <img src="<?= $urlGrafica ?>" alt="Gráfica por horas" class="img-fluid rounded-3">
        </div>
        

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-search"></i> Nueva búsqueda
            </a>
            <a href="actual.php?ciudad=<?= urlencode($ciudad ?? '') ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-geo-alt"></i> Actual
            </a>
            <a href="semana.php?ciudad=<?= urlencode($ciudad ?? '') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-calendar-week"></i> Semana
            </a>
        </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-clock display-1 text-secondary"></i>
                <h3 class="mt-3 text-muted">No hay datos disponibles</h3>
                <a href="index.php" class="btn btn-secondary mt-3">Ir al buscador</a>
            </div>
        <?php endif; ?>
        

    </div>


</body>
</html>