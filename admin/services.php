<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Service.php';

requireRole('admin');

$service = new Service();
$action = $_GET['action'] ?? 'list';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'create') {
        $service->name = trim($_POST['name']);
        $service->description = trim($_POST['description']);
        $service->price = floatval($_POST['price']);
        $service->duration_minutes = intval($_POST['duration_minutes']);
        $service->commission_percentage = floatval($_POST['commission_percentage']);

        if ($service->create()) {
            $_SESSION['success'] = 'Servicio creado exitosamente';
            header('Location: services.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al crear el servicio';
        }
    }
}

// Obtener servicios
$services = $service->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <i class="fas fa-cut text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Gestión de Servicios</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700"><?php echo $_SESSION['full_name']; ?></span>
                    <a href="../logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8">
                <a href="dashboard.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="users.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-users mr-2"></i>Usuarios
                </a>
                <a href="services.php" class="text-white px-3 py-4 text-sm font-medium border-b-2 border-blue-500">
                    <i class="fas fa-cut mr-2"></i>Servicios
                </a>
                <a href="appointments.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-calendar mr-2"></i>Citas
                </a>
                <a href="reports.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Reportes
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Mensajes -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'create'): ?>
            <!-- Formulario de Crear Servicio -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Crear Nuevo Servicio</h3>
                    
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre del Servicio *
                            </label>
                            <input id="name" name="name" type="text" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Descripción
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">
                                    Precio ($) *
                                </label>
                                <input id="price" name="price" type="number" step="0.01" min="0" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">
                                    Duración (minutos) *
                                </label>
                                <input id="duration_minutes" name="duration_minutes" type="number" min="1" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="commission_percentage" class="block text-sm font-medium text-gray-700">
                                    Comisión (%) *
                                </label>
                                <input id="commission_percentage" name="commission_percentage" type="number" step="0.01" min="0" max="100" value="50" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <a href="services.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Crear Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Lista de Servicios -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Lista de Servicios</h3>
                        <a href="services.php?action=create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Nuevo Servicio
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisión</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($services as $srv): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($srv['name']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo htmlspecialchars($srv['description'] ?? 'Sin descripción'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?php echo number_format($srv['price'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $srv['duration_minutes']; ?> min
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $srv['commission_percentage']; ?>%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
