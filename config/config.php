<?php
// Configuración general del sistema
define('BASE_URL', 'http://barbershop.test');  // URL típica de Laragon
define('SITE_NAME', 'BarberShop Management');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Autoload de clases
spl_autoload_register(function ($class_name) {
    $base_dir = dirname(__DIR__);
    $directories = [
        $base_dir . '/models/',
        $base_dir . '/controllers/',
        $base_dir . '/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
