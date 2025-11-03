<?php
// /crm/public/index.php

// 1. Incluimos la configuración y el header
require '../config.php'; // $pdo está disponible
require '../templates/header.php';

// 2. Operación READ (Leer)
// Hacemos un LEFT JOIN para obtener el nombre del 'tipo_cliente' [cite: 40]
$sql = "
    SELECT 
        c.*, 
        t.tipo AS tipo_nombre 
    FROM cliente c
    LEFT JOIN tipo_cliente t ON c.tipo_id = t.id
    ORDER BY c.id DESC
";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll();

?>

<h1>Gestión de Clientes</h1>
<a href="cliente_crear.php" class="btn btn-create">Añadir Nuevo Cliente</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Logo</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Tipo</th>
            <th>Etiqueta</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="8">No hay clientes registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['id']) ?></td>
                    <td>
                        <?php if (!empty($cliente['nombre_fisico_imagen'])): ?>
                            <img src="../uploads/clients/<?= htmlspecialchars($cliente['nombre_fisico_imagen']) ?>" alt="Logo">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                    <td><?= htmlspecialchars($cliente['email']) ?></td>
                    <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                    <td><?= htmlspecialchars($cliente['tipo_nombre'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($cliente['etiqueta']) ?></td>
                    <td>
                        <a href="cliente_editar.php?id=<?= $cliente['id'] ?>">Editar</a>
                        <a href="cliente_borrar.php?id=<?= $cliente['id'] ?>">Borrar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
// 3. Incluimos el footer
require '../templates/footer.php';
?>