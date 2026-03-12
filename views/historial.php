<?php
require_once '../models/HistorialModel.php';

$historialModel = new HistorialModel();
$historial = $historialModel->VerHistorial();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Búsquedas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php require_once '../views/header.php'; ?>

    <div class="container mt-5 mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="text-center mb-4 border-bottom pb-2">
                    <i class="bi bi-clock-history me-2" style="color: var(--verde-azulado);"></i>Historial de Búsquedas
                </h2>

                <?php if (!empty($historial)): ?>
                    <div class="table-responsive shadow-sm rounded">
                        <table class="table table-hover align-middle mb-0">
                            <!--Head de la tabla-->
                            <thead class="table-dark" style="background-color: var(--azul-oscuro);">
                                <tr>
                                    <th>Ciudad</th>
                                    <th>Latitud</th>
                                    <th>Longitud</th>
                                    <th>Fecha y Hora</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <!--Cuerpo de la tabla con todas las busquedas realizadas anteriormente-->
                            <tbody>
                                <?php foreach ($historial as $registro): ?>
                                    <tr>
                                        <td class="fw-bold text-capitalize"><?= htmlspecialchars($registro['ciudad']) ?></td>
                                        <td><?= htmlspecialchars($registro['latitud']) ?></td>
                                        <td><?= htmlspecialchars($registro['longitud']) ?></td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($registro['fecha_consulta'])) ?>
                                        </td>
                                        <td><?= htmlspecialchars($registro['tipo']) ?></td>
                                        <td class="text-center">
                                            <a href="actual.php?ciudad=<?= urlencode($registro['ciudad']) ?>&lat=<?= $registro['latitud'] ?>&lon=<?= $registro['longitud'] ?>&tipo=<?= $registro['tipo'] ?>" 
                                            class="btn btn-sm btn-outline-secondary" title="Volver a consultar el tiempo">
                                                <i class="bi bi-cloud-sun"></i> Ver tiempo
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <p class="fs-5 mb-0">Aún no hay búsquedas en el historial de la base de datos.</p>
                        <a href="index.php" class="btn btn-secondary mt-3">Hacer una búsqueda</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>