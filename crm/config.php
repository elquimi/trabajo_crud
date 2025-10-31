<?php
/**
 * Fichero de Configuración y Conexión PDO
 * Ubicado en: /crm/config.php
 */

// 1. Configuración de la Base de Datos
// (¡Asegúrate de rellenar esto con tus datos reales de WAMP/XAMPP!)
$host = '127.0.0.1';        // O 'localhost'
$dbname = 'crm_db';         // El nombre de tu base de datos (¡créala en phpMyAdmin!)
$user = 'root';             // Usuario común en WAMP/XAMPP
$pass = '';                 // Contraseña (vacía por defecto en WAMP/XAMPP)
$charset = 'utf8mb4';       // Recomendado para soporte completo de acentos y emojis

// 2. DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// 3. Opciones de PDO
$options = [
    // Lanzar excepciones en lugar de errores fatales o warnings
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Devolver los resultados como arrays asociativos (ej: $fila['nombre'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Desactivar emulación de sentencias preparadas (usa las nativas de MySQL)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Conexión PDO
try {
    // Intentamos crear la instancia de PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    
} catch (\PDOException $e) {
    // Si la conexión falla, detenemos la ejecución y mostramos el error
    // (En un entorno de producción, esto debería registrarse en un log y no mostrarse al usuario)
    http_response_code(500);
    exit('Error de conexión a la base de datos: ' . $e->getMessage());
}

// IMPORTANTE:
// No cierres la etiqueta PHP  si este archivo contiene *solo* código PHP. 
// Esto previene errores de "headers already sent" causados por espacios en blanco accidentales
// al final del archivo, que es una causa común de errores de sintaxis (Parse errors)
// cuando se incluye en otros ficheros.