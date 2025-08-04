<?php
// Incluir archivo de configuración y conexión a la base de datos
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar los datos del formulario
    $numero_hermano = filter_input(INPUT_POST, 'numero_hermano', FILTER_SANITIZE_NUMBER_INT);
    $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_STRING);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $apellidos = filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING);
    $sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
    $fecha_nacimiento = filter_input(INPUT_POST, 'fecha_nac', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $codigo_postal = filter_input(INPUT_POST, 'cod_postal', FILTER_SANITIZE_STRING);
    $poblacion = filter_input(INPUT_POST, 'poblacion', FILTER_SANITIZE_STRING);
    $provincia = filter_input(INPUT_POST, 'provincia', FILTER_SANITIZE_STRING);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $observaciones = filter_input(INPUT_POST, 'observaciones', FILTER_SANITIZE_STRING);

    // Validar datos (ejemplo básico)
    if (empty($numero_hermano) || empty($dni) || empty($nombre) || empty($apellidos) || empty($sexo) || empty($fecha_nacimiento) || empty($direccion) || empty($codigo_postal) || empty($poblacion) || empty($provincia)) {
        die("Por favor, complete todos los campos obligatorios.");
    }

    // Manejo de la foto (si se subió)
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = basename($_FILES['foto']['name']);
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . $foto;

        // Asegúrate de que el directorio de subidas existe
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            die("Error al subir la foto.");
        }
    }

    try {
        // Preparar la consulta SQL para insertar los datos
        $sql = "INSERT INTO hermanos (numero_hermano, numero_orden, dni, nombre, apellidos, sexo, fecha_nacimiento, fecha_alta_inicial, fecha_alta_actual, direccion, codigo_postal, poblacion, provincia, telefono, email, foto, observaciones)
                VALUES (:numero_hermano, :numero_orden, :dni, :nombre, :apellidos, :sexo, :fecha_nacimiento, CURDATE(), CURDATE(), :direccion, :codigo_postal, :poblacion, :provincia, :telefono, :email, :foto, :observaciones)";

        $stmt = $conn->prepare($sql);

        // Vincular parámetros
        $stmt->bindParam(':numero_hermano', $numero_hermano);
        $stmt->bindParam(':numero_orden', $numero_hermano); // Aquí puedes cambiar el número de orden si es necesario
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        $stmt->bindParam(':poblacion', $poblacion);
        $stmt->bindParam(':provincia', $provincia);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':observaciones', $observaciones);

        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir a una página de éxito o a la lista de hermanos
        header("Location: ../gestion_hermanos.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error al registrar el hermano: " . $e->getMessage());
    }
} else {
    // Si no se ha enviado el formulario, redirigir a la página del formulario
    header("Location: alta_hermano.php");
    exit();
}
?>
