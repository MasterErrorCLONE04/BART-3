<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/utils/auth.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'barbero':
            header('Location: barber/dashboard.php');
            break;
        case 'cliente':
            header('Location: client/dashboard.php');
            break;
    }
    exit();
}

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $authController = new AuthController();
    $authController->register();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <i class="fas fa-cut text-4xl text-blue-600 mb-4"></i>
            <h2 class="text-3xl font-bold text-gray-900">Crear Cuenta</h2>
            <p class="mt-2 text-gray-600">Regístrate como cliente</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                        Nombre *
                    </label>
                    <input id="first_name" name="first_name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                        Apellido *
                    </label>
                    <input id="last_name" name="last_name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">
                    Nombre de Usuario *
                </label>
                <input id="username" name="username" type="text" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email *
                </label>
                <input id="email" name="email" type="email" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    Teléfono
                </label>
                <input id="phone" name="phone" type="tel" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Contraseña *
                </label>
                <input id="password" name="password" type="password" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Registrarse
            </button>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes cuenta? 
                    <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Inicia sesión aquí
                    </a>
                </p>
                <p class="mt-2 text-sm text-gray-600">
                    <a href="index.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Volver al inicio
                    </a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
