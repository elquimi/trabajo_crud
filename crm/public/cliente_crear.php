<?php
// 1. INCLUIR CONFIGURACIÓN Y HEADER
// Subimos un nivel (../) para encontrar config.php y templates/
require_once '../config.php';
require_once '../templates/header.php';

// Variables para manejar errores y datos
$errores = [];
$datos = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'direccion' => '',
    'etiqueta' => 'activo',
    'tipo_id' => null,
    'comentarios' => ''
];

// 2. LÓGICA PARA CARGAR TIPOS DE CLIENTE (PARA EL <SELECT>)
// Esto se necesita tanto para mostrar el formulario (GET) como para procesarlo (POST)
try {
    $stmt_tipos = $pdo->query("SELECT id, tipo FROM tipo_cliente ORDER BY tipo");
    $tipos_cliente = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errores[] = "Error al cargar los tipos de cliente: " . $e->getMessage();
    $tipos_cliente = []; // Aseguramos que sea un array para el foreach
}


// 3. PROCESAR EL FORMULARIO (SI SE ENVIÓ POR POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Recogida y saneamiento básico de datos ---
    $datos['nombre'] = trim($_POST['nombre'] ?? '');
    $datos['email'] = trim($_POST['email'] ?? '');
    $datos['telefono'] = trim($_POST['telefono'] ?? '');
    $datos['direccion'] = trim($_POST['direccion'] ?? '');
    $datos['etiqueta'] = trim($_POST['etiqueta'] ?? 'activo');
    $datos['tipo_id'] = filter_var($_POST['tipo_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $datos['comentarios'] = trim($_POST['comentarios'] ?? '');

    $imagen_original = '';
    $imagen_fisica = '';

    [cite_start]// --- Validaciones de datos --- [cite: 12]
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }
    if ($datos['tipo_id'] === false) { // Si el filtro falla
        $datos['tipo_id'] = null; // Guardar como NULL si no es válido
    }

    [cite_start]// --- Procesamiento de la imagen --- [cite: 83-89]
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        
        $imagen_tmp = $_FILES['imagen']['tmp_name']; [cite_start]// [cite: 106]
        $imagen_original = $_FILES['imagen']['name']; [cite_start]// [cite: 107]
        $imagen_size = $_FILES['imagen']['size'];

        [cite_start]// Validación 1: Tamaño (< 3MB) [cite: 12]
        if ($imagen_size > 3 * 1024 * 1024) {
            $errores[] = "La imagen es demasiado grande (máximo 3 MB).";
        }

        [cite_start]// Validación 2: Tipo MIME real (JPG o PNG) [cite: 12, 86]
        $finfo = new finfo(FILEINFO_MIME_TYPE); [cite_start]// [cite: 108]
        $mime_real = $finfo->file($imagen_tmp); [cite_start]// [cite: 109]
        $tipos_permitidos = ['image/jpeg', 'image/png'];

        if (!in_array($mime_real, $tipos_permitidos)) {
            $errores[] = "Tipo de imagen no permitido (solo JPG o PNG).";
        }

        // Si la imagen es válida, generar nombre y mover
        if (empty($errores)) {
            $extension = pathinfo($imagen_original, PATHINFO_EXTENSION); [cite_start]// [cite: 112]
            $hash = bin2hex(random_bytes(16)); [cite_start]// [cite: 111]
            $imagen_fisica = $hash . '.' . $extension; [cite_start]// [cite: 113]

            // Ruta de destino (subiendo un nivel desde 'public/' a 'uploads/clients/')
            $destino = '../uploads/clients/' . $imagen_fisica; [cite_start]// [cite: 77, 114]

            [cite_start]if (!move_uploaded_file($imagen_tmp, $destino)) { // [cite: 88, 115]
                $errores[] = "Error al mover el fichero subido.";
                $imagen_original = '';
                $imagen_fisica = '';
            }
        }

    } // Fin procesamiento imagen

    // --- Inserción en Base de Datos (si no hay errores) ---
    if (empty($errores)) {
        try {
            [cite_start]// [cite: 117-119]
            $sql = "INSERT INTO cliente (nombre, email, telefono, direccion, etiqueta, imagen, nombre_fisico_imagen, tipo_id, comentarios)
                    VALUES (:nombre, :email, :telefono, :direccion, :etiqueta, :imagen, :nombre_fisico_imagen, :tipo_id, :comentarios)";
            
            $stmt = $pdo->prepare($sql); [cite_start]// [cite: 120]
            
            [cite_start]$stmt->execute([ // [cite: 121]
                [cite_start]':nombre' => $datos['nombre'], // [cite: 122]
                [cite_start]':email' => $datos['email'], // [cite: 123]
                [cite_start]':telefono' => $datos['telefono'], // [cite: 124]
                [cite_start]':direccion' => $datos['direccion'], // [cite: 125]
                [cite_start]':etiqueta' => $datos['etiqueta'], // [cite: 126]
                [cite_start]':imagen' => $imagen_original, // [cite: 127]
                [cite_start]':nombre_fisico_imagen' => $imagen_fisica, // [cite: 128]
                [cite_start]':tipo_id' => $datos['tipo_id'], // [cite: 129]
                [cite_start]':comentarios' => $datos['comentarios'] // [cite: 130]
            ]);

            // Redirigir al listado (index.php)
            header("Location: index.php?status=created");
            exit; // Importante salir después de una redirección

        } catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
} // Fin del IF POST

?>

<div class="container">
    <h2>Añadir Nuevo Cliente</h2>

    <?php
    // Mostrar errores si los hay (después de un intento de POST fallido)
    if (!empty($errores)) {
        echo '<div class="alert alert-danger">';
        foreach ($errores as $error) {
            echo htmlspecialchars($error) . '<br>';
        }
        echo '</div>';
    }
    ?>

    <form action="cliente_crear.php" method="post" enctype="multipart/form-data">
        
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($datos['email']) ?>">
        </div>
        
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($datos['telefono']) ?>">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($datos['direccion']) ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="etiqueta" class="form-label">Etiqueta</label>
                <select class="form-select" id="etiqueta" name="etiqueta">
                    <option value="activo" <?= ($datos['etiqueta'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                    <option value="prospecto" <?= ($datos['etiqueta'] === 'prospecto') ? 'selected' : '' ?>>Prospecto</option>
                    <option value="inactivo" <?= ($datos['etiqueta'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="tipo_id" class="form-label">Tipo de Cliente</label>
                <select class="form-select" id="tipo_id" name="tipo_id">
                    <option value="">-- Seleccionar tipo --</option>
                    [cite_start]<?php foreach ($tipos_cliente as $tipo): //[cite: 98]?>
                        <option value="<?= htmlspecialchars($tipo['id']) ?>" <?= ($datos['tipo_id'] == $tipo['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tipo['tipo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen (Logo/Foto)</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.png">
            <div class="form-text">Solo archivos JPG o PNG, máximo 3MB.</div>
        </div>
        
        <div class="mb-3">
            <label for="comentarios" class="form-label">Comentarios</label>
            <textarea class="form-control" id="comentarios" name="comentarios" rows="3"><?= htmlspecialchars($datos['comentarios']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>

</div>

<?php
// 5. INCLUIR FOOTER
require_once '../templates/footer.php';
?>