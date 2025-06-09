<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Service.php';

requireRole('admin');

$appointment = new Appointment();
$user = new User();
$service = new Service();

// Obtener estadísticas
$database = new Database();
$conn = $database->getConnection();

// Total de usuarios por rol
$query = "SELECT r.name, COUNT(u.id) as count FROM users u 
          JOIN roles r ON u.role_id = r.id 
          WHERE u.is_active = 1 
          GROUP BY r.name";
$stmt = $conn->prepare($query);
$stmt->execute();
$userStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Citas del mes actual
$query = "SELECT COUNT(*) as total FROM appointments 
          WHERE MONTH(appointment_date) = MONTH(CURRENT_DATE()) 
          AND YEAR(appointment_date) = YEAR(CURRENT_DATE())";
$stmt = $conn->prepare($query);
$stmt->execute();
$monthlyAppointments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Ingresos del mes
$query = "SELECT SUM(total_amount) as total FROM appointments 
          WHERE status = 'completed' 
          AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) 
          AND YEAR(appointment_date) = YEAR(CURRENT_DATE())";
$stmt = $conn->prepare($query);
$stmt->execute();
$monthlyRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Citas recientes
$query = "SELECT a.*, s.name as service_name, 
          CONCAT(c.first_name, ' ', c.last_name) as client_name,
          CONCAT(b.first_name, ' ', b.last_name) as barber_name
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          JOIN users c ON a.client_id = c.id
          JOIN users b ON a.barber_id = b.id
          ORDER BY a.created_at DESC
          LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$recentAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <i class="fas fa-cut text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900"><?php echo SITE_NAME; ?> - Panel Administrador</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Bienvenido, <?php echo $_SESSION['full_name']; ?></span>
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
                <a href="dashboard.php" class="text-white px-3 py-4 text-sm font-medium border-b-2 border-blue-500">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="users.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-users mr-2"></i>Usuarios
                </a>
                <a href="services.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
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
        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clientes</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $userStats['cliente'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-tie text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Barberos</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $userStats['barbero'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Citas Este Mes</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $monthlyAppointments; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ingresos Este Mes</dt>
                                <dd class="text-lg font-medium text-gray-900">$<?php echo number_format($monthlyRevenue, 2); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="users.php?action=create&role=barbero" class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition duration-200 text-center">
                        <i class="fas fa-user-plus text-2xl mb-2"></i>
                        <div class="font-semibold">Agregar Barbero</div>
                    </a>
                    <a href="services.php?action=create" class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition duration-200 text-center">
                        <i class="fas fa-plus text-2xl mb-2"></i>
                        <div class="font-semibold">Nuevo Servicio</div>
                    </a>
                    <a href="appointments.php" class="bg-yellow-600 text-white p-4 rounded-lg hover:bg-yellow-700 transition duration-200 text-center">
                        <i class="fas fa-calendar-check text-2xl mb-2"></i>
                        <div class="font-semibold">Ver Citas</div>
                    </a>
                    <a href="reports.php" class="bg-purple-600 text-white p-4 rounded-lg hover:bg-purple-700 transition duration-200 text-center">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <div class="font-semibold">Generar Reporte</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Citas Recientes -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Citas Recientes</h3>
                
                <?php if (empty($recentAppointments)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No hay citas registradas</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbero</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentAppointments as $apt): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('d/m/Y H:i', strtotime($apt['appointment_date'] . ' ' . $apt['appointment_time'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($apt['client_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($apt['barber_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($apt['service_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $statusColors = [
                                                'scheduled' => 'bg-yellow-100 text-yellow-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'no_show' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusTexts = [
                                                'scheduled' => 'Agendada',
                                                'completed' => 'Completada',
                                                'cancelled' => 'Cancelada',
                                                'no_show' => 'No asistió'
                                            ];
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColors[$apt['status']]; ?>">
                                                <?php echo $statusTexts[$apt['status']]; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?php echo number_format($apt['total_amount'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="appointments.php" class="text-blue-600 hover:text-blue-800">
                            Ver todas las citas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
