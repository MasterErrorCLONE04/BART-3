<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';

requireRole('admin');

$database = new Database();
$conn = $database->getConnection();

// Estadísticas generales
$stats = [];

// Ingresos por mes
$query = "SELECT 
    MONTH(appointment_date) as month,
    YEAR(appointment_date) as year,
    SUM(total_amount) as total_revenue,
    COUNT(*) as total_appointments
    FROM appointments 
    WHERE status = 'completed' 
    AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(appointment_date), MONTH(appointment_date)
    ORDER BY year DESC, month DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Top barberos
$query = "SELECT 
    CONCAT(u.first_name, ' ', u.last_name) as barber_name,
    COUNT(a.id) as total_appointments,
    SUM(a.total_amount) as total_revenue,
    SUM(a.commission_amount) as total_commission
    FROM appointments a
    JOIN users u ON a.barber_id = u.id
    WHERE a.status = 'completed'
    GROUP BY a.barber_id
    ORDER BY total_revenue DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$barberStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Servicios más populares
$query = "SELECT 
    s.name as service_name,
    COUNT(a.id) as total_bookings,
    SUM(a.total_amount) as total_revenue
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.status = 'completed'
    GROUP BY a.service_id
    ORDER BY total_bookings DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$serviceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - <?php echo SITE_NAME; ?></title>
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
                    <i class="fas fa-chart-bar text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Reportes</h1>
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
                <a href="services.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-cut mr-2"></i>Servicios
                </a>
                <a href="appointments.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-calendar mr-2"></i>Citas
                </a>
                <a href="reports.php" class="text-white px-3 py-4 text-sm font-medium border-b-2 border-blue-500">
                    <i class="fas fa-chart-bar mr-2"></i>Reportes
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Ingresos Mensuales -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ingresos por Mes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mes/Año</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citas Completadas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos Totales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($monthlyStats as $stat): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php 
                                        $months = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                        ];
                                        echo $months[$stat['month']] . ' ' . $stat['year'];
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stat['total_appointments']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($stat['total_revenue'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rendimiento de Barberos -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Rendimiento de Barberos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbero</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citas Completadas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos Generados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisiones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($barberStats as $stat): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($stat['barber_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stat['total_appointments']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($stat['total_revenue'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($stat['total_commission'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Servicios Más Populares -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Servicios Más Populares</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Reservas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos Totales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($serviceStats as $stat): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($stat['service_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stat['total_bookings']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($stat['total_revenue'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
