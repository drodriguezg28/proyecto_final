<?php
if (!isset($datosClima) && !isset($mensajeError)) {
    require_once '../models/MeteoModel.php';
    require_once '../models/HistorialModel.php';
    require_once '../controllers/MeteoController.php';

    $ciudad = htmlspecialchars(trim($_GET['ciudad'] ?? ''));

    if (!empty($ciudad)) {
        $controlador = new MeteoController(
            new MeteoModel(getenv('API_KEY')),
            new HistorialModel()
        );

        $controlador->buscarOpciones($ciudad);
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

$opciones = $datosClima;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados para <?= htmlspecialchars($ciudad ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php require_once '../views/header.php'; ?>

    <div class="container mt-5">
        <!--Error-->
        <?php if (isset($mensajeError)): ?>
        <div class="text-center">
            <?= $mensajeError ?><br>
            <a href="index.php" class="btn btn-secondary mt-3">Volver al buscador</a>
        </div>


        <?php elseif (!empty($opciones)): ?>
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <h3 class="text-center mb-4">
                        Se han encontrado varias opciones para <?= htmlspecialchars(ucfirst($ciudad??'')) ?></span>
                    </h3>
                    <!--Opciones-->
                    <div class="list-group shadow-sm">
                        <?php foreach($opciones as $opcion): ?>
                            <a href="actual.php?ciudad=<?= urlencode($opcion['name']) ?>&lat=<?= $opcion['lat'] ?>&lon=<?= $opcion['lon'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h5 class="mb-1 text-dark fw-bold"><?= htmlspecialchars($opcion['name']) ?></h5>
                                    <?php if(isset($opcion['state'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($opcion['state']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-secondary rounded-pill fs-6"><?= htmlspecialchars($opcion['country']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <!--Botón-->
                </div>
                <div class="text-center mt-4 mb-4">
                    <a href="index.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-search"></i> Nueva búsqueda
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>