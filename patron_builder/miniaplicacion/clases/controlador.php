<?php
require_once 'Monstruo.php';
require_once 'MonstruoBuilder.php';
session_start(); // Iniciamos la sesión para poder guardar monstruos temporalmente



// --- LÓGICA PARA GUARDAR/ELIMINAR MONSTRUOS ---

// Inicializamos el array de monstruos en la sesión si no existe
if (!isset($_SESSION['monstruos'])) {
    $_SESSION['monstruos'] = [];
}

// Si se envió el formulario para CREAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_monstruo'])) {
    $builder = new MonstruoBuilder();
    $nuevoMonstruo = $builder->addCabeza($_POST['tipo_cabeza'])
                             ->addNumCabezas((int)$_POST['num_cabezas'])
                             ->addTorso($_POST['tipo_torso'])
                             ->addBrazos($_POST['tipo_brazos'])
                             ->addNumBrazos((int)$_POST['num_brazos'])
                             ->addPiernas($_POST['tipo_piernas'])
                             ->addNumPiernas((int)$_POST['num_piernas'])
                             ->getMonstruo();
    
    // Guardamos el objeto monstruo en la sesión
    $_SESSION['monstruos'][] = $nuevoMonstruo;

    // Redirigimos para evitar reenvío del formulario al recargar
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Si se envió la petición para ELIMINAR
if (isset($_GET['eliminar'])) {
    $indice = (int)$_GET['eliminar'];
    if (isset($_SESSION['monstruos'][$indice])) {
        unset($_SESSION['monstruos'][$indice]);
        // Reindexamos el array para que no queden huecos en los índices
        $_SESSION['monstruos'] = array_values($_SESSION['monstruos']);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Interactivo</title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-green: #39ff14;
            --neon-blue: #00ffff;
            --dark-bg: #0a0a0f;
            --panel-bg: #11111a;
            --danger-red: #ff073a;
        }
        body {
            background-color: var(--dark-bg);
            background-image: linear-gradient(rgba(0, 255, 0, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 0, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--neon-green);
            font-family: 'Share Tech Mono', monospace;
            padding: 20px;
            text-align: center;
        }
        h1 {
            font-family: 'Creepster', cursive;
            color: var(--danger-red);
            font-size: 3.5rem;
            text-shadow: 0 0 10px rgba(255, 7, 58, 0.5);
            margin-bottom: 30px;
        }

        /* --- ESTILOS DEL FORMULARIO --- */
        .control-panel {
            background-color: var(--panel-bg);
            border: 2px solid var(--neon-blue);
            border-radius: 15px;
            padding: 25px;
            max-width: 600px;
            margin: 0 auto 50px;
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.15);
            text-align: left;
        }
        .control-panel h2 {
            color: var(--neon-blue);
            margin-top: 0;
            border-bottom: 1px dashed var(--neon-blue);
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-group label {
            flex: 1;
            color: #fff;
        }
        .form-group input, .form-group select {
            flex: 1.5;
            background: #000;
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
            padding: 8px;
            font-family: 'Share Tech Mono', monospace;
        }
        .btn-create {
            width: 100%;
            padding: 15px;
            background: var(--danger-red);
            border: none;
            color: white;
            font-family: 'Creepster', cursive;
            font-size: 1.5rem;
            cursor: pointer;
            letter-spacing: 2px;
            transition: all 0.3s;
        }
        .btn-create:hover {
            background: #ff0000;
            box-shadow: 0 0 20px var(--danger-red);
        }

        /* --- ESTILOS DE LAS FICHAS (Monstruos) --- */
        .containment-zone {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .monster-card {
            background-color: var(--panel-bg);
            border: 2px solid var(--neon-green);
            border-radius: 15px;
            padding: 20px;
            width: 300px;
            text-align: left;
            box-shadow: 0 0 15px rgba(57, 255, 20, 0.2);
            position: relative;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .monster-card h3 {
            color: var(--neon-green);
            margin-top: 0; border-bottom: 1px dashed var(--neon-green); padding-bottom: 10px;
        }
        .btn-delete {
            display: block;
            width: 100%;
            padding: 8px;
            margin-top: 15px;
            background: transparent;
            border: 1px solid var(--danger-red);
            color: var(--danger-red);
            cursor: pointer;
            font-family: 'Share Tech Mono', monospace;
            transition: all 0.2s;
        }
        .btn-delete:hover {
            background: var(--danger-red);
            color: #fff;
        }
        .empty-message {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>Laboratorio de Creación</h1>

    <div class="control-panel">
        <h2>>> INICIAR SECUENCIA DE CREACIÓN</h2>
        <form method="POST" action="">

     <!-------------------------------tipo_cabeza-------------------------------------------------->
            <div class="form-group">
                <label>Tipo de Cabeza:</label>
                <select name="tipo_cabeza" required>
                    <option value="Alien">Alien</option>
                    <option value="Dragón">Dragón</option>
                    <option value="Cíclope">Cíclope</option>
                    <option value="Zombie">Zombie</option>
                    <option value="Sin cabeza">Ninguna</option>
                </select>
            </div>
             <!-------------------------------cantidad_cabeza-------------------------------------------------->
            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="num_cabezas" min="0" max="5" value="1" required>
            </div>
 <!-------------------------------tipo_torso-------------------------------------------------->
            <div class="form-group">
                <label>Tipo de Torso:</label>
                <input type="text" name="tipo_torso" placeholder="Ej: Metálico, Escamoso..." required>
            </div>

             <!-------------------------------tipo_brazos-------------------------------------------------->
            <div class="form-group">
                <label>Tipo de Brazos:</label>
                <select name="tipo_brazos" required>
                    <option value="Tentáculos">Tentáculos</option>
                    <option value="Garras mecánicas">Garras mecánicas</option>
                    <option value="Alas de murciélago">Alas</option>
                    <option value="Brazos humanos">Humanos</option>
                </select>
            </div>

             <!-------------------------------cantidad_barzos-------------------------------------------------->
            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="num_brazos" min="0" max="8" value="2" required>
            </div>

             <!-------------------------------tipo_piernas-------------------------------------------------->
            <div class="form-group">
                <label>Tipo de Piernas:</label>
                <input type="text" name="tipo_piernas" placeholder="Ej: Patas de araña, Ruedas...">
            </div>
             <!-------------------------------cantidad_piernas-------------------------------------------------->
            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="num_piernas" min="0" max="10" value="2" required>
            </div>

            <button type="submit" name="crear_monstruo" class="btn-create">¡DAR VIDA!</button>
        </form>
    </div>

    <h2>>> ZONA DE CONTENCIÓN [<?php echo count($_SESSION['monstruos']); ?> ESPECÍMENES]</h2>
    
    <div class="containment-zone">
        <?php if (empty($_SESSION['monstruos'])): ?>
            <p class="empty-message">No hay especímenes en contención. El laboratorio está seguro... por ahora.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['monstruos'] as $indice => $monstruo): ?>
                <div class="monster-card">
                    <?php $monstruo->mostrar(); ?>
                    
                    <a href="?eliminar=<?php echo $indice; ?>">
                        <button class="btn-delete">TERMINAR ESPECÍMEN.</button>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>