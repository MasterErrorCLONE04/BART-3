<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso No Autorizado - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full text-center">
        <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Acceso No Autorizado</h1>
        <p class="text-gray-600 mb-6">No tienes permisos para acceder a esta página.</p>
        <div class="space-x-4">
            <a href="javascript:history.back()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Volver
            </a>
            <a href="index.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Ir al Inicio
            </a>
        </div>
    </div>
</body>
</html>
