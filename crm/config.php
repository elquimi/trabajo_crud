<?php
/**
 * Fichero de Configuración y Conexión PDO
 * Ubicado en: /crm/config.php
 */

// 1. Configuración de la Base de Datos
$host = '127.0.0.1';    // O 'localhost'
$dbname = 'trabajo_crud';     // El nombre de tu base de datos
$user = 'root';         // Usuario 
$pass = '';         // Contraseña
$charset = 'utf8mb4';   // Recomendado

// 2. DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// 3. Opciones de PDO (Verificado)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Conexión PDO
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     http_response_code(500);
     exit('Error de conexión a la base de datos: ' . $e->getMessage());
}

// No cierres la etiqueta PHP