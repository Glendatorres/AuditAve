<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Aves</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Gestión de Aves</h1>

        <!-- Formulario para Agregar Aves -->
        <h2>Agregar Ave</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="nombre_cientifico" class="form-label">Nombre Científico:</label>
                <input type="text" class="form-control" name="nombre_cientifico" required>
            </div>
            <div class="mb-3">
                <label for="nombre_comun" class="form-label">Nombre Común:</label>
                <input type="text" class="form-control" name="nombre_comun" required>
            </div>
            <div class="mb-3">
                <label for="habitat" class="form-label">Hábitat:</label>
                <input type="text" class="form-control" name="habitat">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" name="descripcion" rows="4"></textarea>
            </div>
            <input type="submit" name="agregar" class="btn btn-primary" value="Agregar Ave">
        </form>

       


        <?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Aquí va el código de tu CRUD (el mismo que ya tienes en index.php)

        
        $conexion = mysqli_connect("localhost", "root", "", "ave_db");

        if (!$conexion) {
            die("Error en la conexión a la base de datos: " . mysqli_connect_error());
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
            $nombre_cientifico = trim($_POST['nombre_cientifico']);
            $nombre_comun = trim($_POST['nombre_comun']);
            $habitat = trim($_POST['habitat']);
            $descripcion = trim($_POST['descripcion']);

            if (!empty($nombre_cientifico) && !empty($nombre_comun)) {
                $stmt = mysqli_prepare($conexion, "INSERT INTO aves (nombre_cientifico, nombre_comun, habitat, descripcion) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $nombre_cientifico, $nombre_comun, $habitat, $descripcion);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<div class='alert alert-success mt-3'>Ave agregada correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>Error al agregar ave: " . mysqli_error($conexion) . "</div>";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "<div class='alert alert-warning mt-3'>Por favor, completa los campos obligatorios.</div>";
            }
        }

        // Proceso para Eliminar Ave
        if (isset($_GET['eliminar'])) {
            $id_eliminar = intval($_GET['eliminar']);
            $stmt = mysqli_prepare($conexion, "DELETE FROM aves WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_eliminar);

            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success mt-3'>Ave eliminada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error al eliminar ave: " . mysqli_error($conexion) . "</div>";
            }

            mysqli_stmt_close($stmt);
        }

        // Proceso para Actualizar Ave
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
            $id_actualizar = intval($_POST['id']);
            $nombre_cientifico = trim($_POST['nombre_cientifico']);
            $nombre_comun = trim($_POST['nombre_comun']);
            $habitat = trim($_POST['habitat']);
            $descripcion = trim($_POST['descripcion']);

            if (!empty($nombre_cientifico) && !empty($nombre_comun)) {
                $stmt = mysqli_prepare($conexion, "UPDATE aves SET nombre_cientifico = ?, nombre_comun = ?, habitat = ?, descripcion = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssssi", $nombre_cientifico, $nombre_comun, $habitat, $descripcion, $id_actualizar);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<div class='alert alert-success mt-3'>Ave actualizada correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>Error al actualizar ave: " . mysqli_error($conexion) . "</div>";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "<div class='alert alert-warning mt-3'>Por favor, completa los campos obligatorios.</div>";
            }
        }
        ?>

        <!-- Listado de Aves -->
        <h2 class="mt-5">Lista de Aves</h2>
        <?php
        $sql = "SELECT * FROM aves";
        $resultado = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>ID</th><th>Nombre Científico</th><th>Nombre Común</th><th>Hábitat</th><th>Descripción</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";

            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>";
                echo "<td>" . $fila['id'] . "</td>";
                echo "<td>" . $fila['nombre_cientifico'] . "</td>";
                echo "<td>" . $fila['nombre_comun'] . "</td>";
                echo "<td>" . $fila['habitat'] . "</td>";
                echo "<td>" . $fila['descripcion'] . "</td>";
                echo "<td>";
                echo "<a href='?editar=" . $fila['id'] . "' class='btn btn-warning btn-sm'>Editar</a> ";
                echo "<a href='?eliminar=" . $fila['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta ave?\");'>Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning'>No hay aves registradas.</div>";
        }

        // Mostrar formulario de edición si se selecciona "Editar"
        if (isset($_GET['editar'])) {
            $id_editar = intval($_GET['editar']);
            $sql_editar = "SELECT * FROM aves WHERE id = $id_editar";
            $resultado_editar = mysqli_query($conexion, $sql_editar);
            $ave_editar = mysqli_fetch_assoc($resultado_editar);

            if ($ave_editar) {
                echo "<h2 class='mt-5'>Editar Ave</h2>";
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='id' value='" . $ave_editar['id'] . "'>";
                echo "<div class='mb-3'>";
                echo "<label for='nombre_cientifico' class='form-label'>Nombre Científico:</label>";
                echo "<input type='text' class='form-control' name='nombre_cientifico' value='" . $ave_editar['nombre_cientifico'] . "' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='nombre_comun' class='form-label'>Nombre Común:</label>";
                echo "<input type='text' class='form-control' name='nombre_comun' value='" . $ave_editar['nombre_comun'] . "' required>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='habitat' class='form-label'>Hábitat:</label>";
                echo "<input type='text' class='form-control' name='habitat' value='" . $ave_editar['habitat'] . "'>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='descripcion' class='form-label'>Descripción:</label>";
                echo "<textarea class='form-control' name='descripcion' rows='4'>" . $ave_editar['descripcion'] . "</textarea>";
                echo "</div>";
                echo "<input type='submit' name='actualizar' class='btn btn-success' value='Actualizar Ave'>";
                echo "</form>";
            }
        }

        mysqli_close($conexion);
      
        ?>

        <br>

<a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
   
</body>
</html>
