<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AppointmentController.php';

requireRole('cliente');

$service = new Service();
$user = new User();

$services = $service->getAll();
$barbers = $user->getBarbers();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointmentController = new AppointmentController();
    $appointmentController->create();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="text-xl font-bold text-gray-900">Agendar Nueva Cita</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700"><?php echo $_SESSION['full_name']; ?></span>
                    <a href="../logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" class="space-y-6">
                    <!-- Seleccionar Servicio -->
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700">
                            Servicio *
                        </label>
                        <select id="service_id" name="service_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione un servicio</option>
                            <?php foreach ($services as $srv): ?>
                                <option value="<?php echo $srv['id']; ?>" 
                                        data-price="<?php echo $srv['price']; ?>"
                                        data-duration="<?php echo $srv['duration_minutes']; ?>">
                                    <?php echo htmlspecialchars($srv['name']); ?> - $<?php echo number_format($srv['price'], 2); ?> (<?php echo $srv['duration_minutes']; ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Seleccionar Barbero -->
                    <div>
                        <label for="barber_id" class="block text-sm font-medium text-gray-700">
                            Barbero *
                        </label>
                        <select id="barber_id" name="barber_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione un barbero</option>
                            <?php foreach ($barbers as $barber): ?>
                                <option value="<?php echo $barber['id']; ?>">
                                    <?php echo htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label for="appointment_date" class="block text-sm font-medium text-gray-700">
                            Fecha *
                        </label>
                        <input id="appointment_date" name="appointment_date" type="date" required 
                               min="<?php echo date('Y-m-d'); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Hora -->
                    <div>
                        <label for="appointment_time" class="block text-sm font-medium text-gray-700">
                            Hora *
                        </label>
                        <select id="appointment_time" name="appointment_time" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione una hora</option>
                            <?php
                            // Generar horarios de 9:00 AM a 6:00 PM cada 30 minutos
                            $start_time = strtotime('09:00');
                            $end_time = strtotime('18:00');
                            
                            for ($time = $start_time; $time <= $end_time; $time += 1800) { // 1800 segundos = 30 minutos
                                $time_str = date('H:i', $time);
                                echo "<option value='$time_str'>$time_str</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Resumen -->
                    <div id="appointment-summary" class="bg-gray-50 p-4 rounded-lg hidden">
                        <h4 class="font-medium text-gray-900 mb-2">Resumen de la Cita</h4>
                        <div id="summary-content"></div>
                    </div>

                    <div class="flex justify-between">
                        <a href="dashboard.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Agendar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Actualizar resumen cuando cambian los campos
        function updateSummary() {
            const serviceSelect = document.getElementById('service_id');
            const barberSelect = document.getElementById('barber_id');
            const dateInput = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('appointment_time');
            const summaryDiv = document.getElementById('appointment-summary');
            const summaryContent = document.getElementById('summary-content');

            if (serviceSelect.value && barberSelect.value && dateInput.value && timeSelect.value) {
                const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
                const serviceName = serviceOption.text.split(' - ')[0];
                const servicePrice = serviceOption.dataset.price;
                const serviceDuration = serviceOption.dataset.duration;
                const barberName = barberSelect.options[barberSelect.selectedIndex].text;
                const date = new Date(dateInput.value).toLocaleDateString('es-ES');
                const time = timeSelect.value;

                summaryContent.innerHTML = `
                    <p><strong>Servicio:</strong> ${serviceName}</p>
                    <p><strong>Barbero:</strong> ${barberName}</p>
                    <p><strong>Fecha:</strong> ${date}</p>
                    <p><strong>Hora:</strong> ${time}</p>
                    <p><strong>Duración:</strong> ${serviceDuration} minutos</p>
                    <p><strong>Precio:</strong> $${parseFloat(servicePrice).toFixed(2)}</p>
                `;
                summaryDiv.classList.remove('hidden');
            } else {
                summaryDiv.classList.add('hidden');
            }
        }

        // Agregar event listeners
        document.getElementById('service_id').addEventListener('change', updateSummary);
        document.getElementById('barber_id').addEventListener('change', updateSummary);
        document.getElementById('appointment_date').addEventListener('change', updateSummary);
        document.getElementById('appointment_time').addEventListener('change', updateSummary);
    </script>
</body>
</html>
