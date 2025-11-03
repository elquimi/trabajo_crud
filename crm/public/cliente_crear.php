<?php
// /crm/public/cliente_crear.php [cite: 74]

require '../config.php'; // $pdo está disponible

$error_msg = '';
$success_msg = '';

// Lógica para cargar los tipos de cliente en el <select> [cite: 98]
try {
    $tipos_stmt = $pdo->query("SELECT * FROM tipo_cliente ORDER BY tipo");
    $tipos_cliente = $tipos_stmt->fetchAll();
} catch (\PDOException $e) {
    $error_msg = "Error al cargar los tipos de cliente: " . $e->getMessage();
    $tipos_cliente = [];
}


// --- INICIO: LÓGICA DE PROCESADO DEL FORMULARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recoger datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $etiqueta = $_POST['etiqueta'] ?? 'activo';
    $tipo_id = $_POST['tipo_id'] ?? null;
    $comentarios = $_POST['comentarios'] ?? '';
    
    // Variables para la imagen
    $imagen_original = null;
    $imagen_fisica = null;

    // 2. Validación de datos obligatorios [cite: 12]
    if (empty($nombre)) {
        $error_msg = 'El nombre es obligatorio.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'El formato del email no es válido.'; //[cite: 12]
    } else {

        try {
            // --- 3. Lógica de Subida de Fichero [cite: 8, 83-89] ---
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                
                $file = $_FILES['imagen'];
                $tmp_name = $file['tmp_name'];
                $imagen_original = $file['name'];
                $file_size = $file['size'];

                // Validación de tamaño (3MB) [cite: 12]
                if ($file_size > 3 * 1024 * 1024) { // 3 MB
                    throw new Exception('El fichero es demasiado grande (Máx 3MB).');
                }

                // Validación de tipo MIME real [cite: 86, 108]
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($tmp_name);
                $allowed_types = ['image/jpeg', 'image/png'];

                if (!in_array($mime_type, $allowed_types)) { //[cite: 12]
                    throw new Exception('Tipo de fichero no permitido (Solo JPG o PNG).');
                }

                // Generar nombre físico único (hash) [cite: 9, 87, 111]
                $ext = pathinfo($imagen_original, PATHINFO_EXTENSION);
                $hash = bin2hex(random_bytes(16)); // 32 caracteres
                $imagen_fisica = $hash . '.' . $ext; 
                
                // Mover el fichero al destino [cite: 88, 115]
                $upload_dir = __DIR__ . '/../uploads/clients/'; 
                $destination = $upload_dir . $imagen_fisica;

                if (!move_uploaded_file($tmp_name, $destination)) {
                    throw new Exception('Error al mover el fichero subido.');
                }
            } // Fin de la subida de fichero

            // --- 4. Inserción en la Base de Datos (PDO) [cite: 116-131] ---
            $sql = "INSERT INTO cliente (nombre, email, telefono, direccion, etiqueta, 
                                        imagen, nombre_fisico_imagen, tipo_id, comentarios)
                    VALUES (:nombre, :email, :telefono, :direccion, :etiqueta, 
                            :imagen, :nombre_fisico_imagen, :tipo_id, :comentarios)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':etiqueta' => $etiqueta,
                ':imagen' => $imagen_original,
                ':nombre_fisico_imagen' => $imagen_fisica,
                ':tipo_id' => ($tipo_id == '0') ? null : $tipo_id, // Asumir '0' como 'ninguno'
                ':comentarios' => $comentarios
            ]);

            $success_msg = '¡Cliente creado con éxito!';
            // Opcional: Redirigir al index
            // header('Location: index.php?status=created');
            // exit;

        } catch (Exception $e) {
            $error_msg = 'Error: ' . $e->getMessage();
            // Si hubo un error y ya se subió la imagen, la borramos
            if (isset($destination) && file_exists($destination)) {
                unlink($destination);
            }
        }
    }
}
// --- FIN: LÓGICA DE PROCESADO DEL FORMULARIO ---


// --- INICIO: VISTA HTML ---
require '../templates/header.php';
?>

<h2>Añadir Nuevo Cliente</h2>

<?php if ($error_msg): ?><p class="error"><?= $error_msg ?></p><?php endif; ?>
<?php if ($success_msg): ?><p class="success"><?= $success_msg ?></p><?php endif; ?>

<form action="cliente_crear.php" method="POST" enctype="multipart/form-data">
    
    <div>
        <label for="nombre">Nombre:</label>
       <input type="text" id="nombre" name="nombre" required> 
    </div>
    
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"> 
    </div>
    
    <div>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono">
    </div>
    
    <div>
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion">
    </div>

    <div>
        <label for="tipo_id">Tipo de Cliente:</label>
       <select id="tipo_id" name="tipo_id">
            <option value="0">-- Sin tipo --</option>
            <?php foreach ($tipos_cliente as $tipo): ?>
                <option value="<?= $tipo['id'] ?>">
                    <?= htmlspecialchars($tipo['tipo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div>
        <label for="etiqueta">Etiqueta:</label>
        <select id="etiqueta" name="etiqueta">
            <option value="activo">Activo</option>
            <option value="prospecto">Prospecto</option>
            <option value="inactivo">Inactivo</option>
        </select>
    </div>

    <div>
        <label for="imagen">Imagen (Logo/Foto):</label>
     <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png"> 
    </div>
    
    <div>
        <label for="comentarios">Comentarios:</label>
        <textarea id="comentarios" name="comentarios" rows="4"></textarea> 
    </div>
    
    <div>
       <button type="submit" class="btn btn-create">Guardar Cliente</button> 
    </div>

</form>

<a href="index.php" class="btn">Volver a la lista</a>

<?php
require '../templates/footer.php';
?>