<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </script>
    <link rel="stylesheet" href="../css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
</head>


<body class="d-flex flex-column min-vh-100">

<?php
    require_once 'header.php';
    require_once '../models/MeteoModel.php';
    require_once '../models/HistorialModel.php';
    require_once '../controllers/MeteoController.php';

    
    $ciudad = null;
    $errores = array();

    if (isset($_POST['enviar'])) {
        $ciudad = htmlspecialchars(trim($_POST['poblacion'] ?? ''));
        if (!empty($ciudad)) {
            
            $vista = $_GET['vista'] ?? 'resultados';
            header("Location: {$vista}.php?ciudad=" . urlencode($ciudad));
            exit;
        }
    }


?>

    <!--Introducción de texto-->
    <div class="container mt-5">
        <form class="row g-3 justify-content-center needs-validation w-100" id="formusuario" name="formusuario" method="post">
            <div class="col-md-4">
                <label for="poblacion" class="form-label">Población</label>
                <input type="text" class="form-control" id="poblacion" name="poblacion" value="<?php if (isset($errores) && isset($errores['poblacion'])) echo $ciudad ?>">
                
                <?php if (isset($errores) && isset($errores['poblacion'])): ?>
                <p class="text-danger">Debes introducir una población</p>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-center my-3">
            <input class="btn btn-enviar" type="submit" name="enviar" value="Enviar">
            </div>
        </form>
        
    </div>
</body>
</html>