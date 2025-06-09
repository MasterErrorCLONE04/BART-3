<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';

requireRole('admin');

// Obtener todas las citas
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT a.*, s.name as service_name, 
          CONCAT(c.first_name, ' ', c.last_name) as client_name,
          CONCAT(b.first_name, ' ', b.last_name) as barber_name,
          c.phone as client_phone
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          JOIN users c ON a.client_id = c.id
          JOIN users b ON a.barber_id = b.id
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - <?php echo SITE_NAME; ?></title>
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
                    <i class="fas fa-calendar text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Gestión de Citas</h1>
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
                <a href="appointments.php" class="text-white px-3 py-4 text-sm font-medium border-b-2 border-blue-500">
                    <i class="fas fa-calendar mr-2"></i>Citas
                </a>
                <a href="reports.php" class="text-gray-300 hover:text-white px-3 py-4 text-sm font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Reportes
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Lista de Citas -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Todas las Citas</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbero</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div class="font-medium"><?php echo date('d/m/Y', strtotime($apt['appointment_date'])); ?></div>
                                            <div class="text-gray-500"><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                        </div>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($apt['client_phone'] ?? 'N/A'); ?>
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
