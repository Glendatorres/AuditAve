<?php
$conexion = mysqli_connect("localhost", "root", "", "ave_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    if (!empty($username) && !empty($password)) {
        // Verificar si el nombre de usuario ya existe
        $stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "El nombre de usuario ya está en uso. Por favor, elige otro.";
        } else {
            // Insertar nuevo usuario
            $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (username, password) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $username, $password_hash);

            if (mysqli_stmt_execute($stmt)) {
                echo "Usuario registrado correctamente. <a href='login.php'>Inicia sesión</a>";
            } else {
                echo "Error al registrar el usuario: " . mysqli_error($conexion);
            }
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>
        <form action="registrer.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
</body>
</html>
