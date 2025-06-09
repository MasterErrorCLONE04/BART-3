<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Appointment.php';

requireRole('cliente');

$appointment = new Appointment();
$appointments = $appointment->getByClient($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="text-xl font-bold text-gray-900"><?php echo SITE_NAME; ?></h1>
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

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Mensajes -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Acciones Rápidas -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="book-appointment.php" class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition duration-200 text-center">
                        <i class="fas fa-calendar-plus text-2xl mb-2"></i>
                        <div class="font-semibold">Agendar Nueva Cita</div>
                    </a>
                    <a href="appointments.php" class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition duration-200 text-center">
                        <i class="fas fa-calendar-alt text-2xl mb-2"></i>
                        <div class="font-semibold">Ver Mis Citas</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Próximas Citas -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Mis Próximas Citas</h3>
                
                <?php if (empty($appointments)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No tienes citas agendadas</p>
                        <a href="book-appointment.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Agendar Primera Cita
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbero</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach (array_slice($appointments, 0, 5) as $apt): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('d/m/Y', strtotime($apt['appointment_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('H:i', strtotime($apt['appointment_time'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($apt['service_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($apt['barber_name']); ?>
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
                                            $<?php echo number_format($apt['price'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($appointments) > 5): ?>
                        <div class="mt-4 text-center">
                            <a href="appointments.php" class="text-blue-600 hover:text-blue-800">
                                Ver todas las citas (<?php echo count($appointments); ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
