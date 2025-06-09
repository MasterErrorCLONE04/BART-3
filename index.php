<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/utils/auth.php';

// Redirige según el rol si ya hay sesión iniciada
if (isLoggedIn()) {
    $dashboards = [
        'admin' => 'admin/dashboard.php',
        'barbero' => 'barber/dashboard.php',
        'cliente' => 'client/dashboard.php'
    ];

    $role = $_SESSION['role'] ?? '';
    if (isset($dashboards[$role])) {
        header("Location: {$dashboards[$role]}");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Bienvenido</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-cut text-3xl text-blue-600"></i>
                <h1 class="text-2xl font-bold"><?= SITE_NAME ?></h1>
            </div>
            <nav class="space-x-4">
                <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Iniciar Sesión</a>
                <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Registrarse</a>
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-24">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl md:text-6xl font-bold mb-6">Tu Barbería de Confianza</h2>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">Agenda tu cita online y disfruta del mejor servicio profesional</p>
            <div class="space-x-4">
                <a href="register.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">Agendar Cita</a>
                <a href="#servicios" class="border-2 border-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">Ver Servicios</a>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios" class="py-16 bg-white">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold mb-4">Nuestros Servicios</h3>
            <p class="text-lg text-gray-600 mb-12">Ofrecemos servicios profesionales de barbería</p>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                $servicios = [
                    ['icon' => 'cut', 'titulo' => 'Corte de Cabello', 'desc' => 'Cortes modernos y clásicos', 'precio' => 25],
                    ['icon' => 'user-tie', 'titulo' => 'Arreglo de Barba', 'desc' => 'Diseño y cuidado profesional', 'precio' => 15],
                    ['icon' => 'star', 'titulo' => 'Corte + Barba', 'desc' => 'Servicio completo', 'precio' => 35],
                    ['icon' => 'shower', 'titulo' => 'Lavado', 'desc' => 'Lavado profesional', 'precio' => 10],
                ];

                foreach ($servicios as $s):
                ?>
                <div class="p-6 bg-gray-50 rounded-lg text-center">
                    <i class="fas fa-<?= $s['icon'] ?> text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2"><?= $s['titulo'] ?></h4>
                    <p class="text-gray-600 mb-4"><?= $s['desc'] ?></p>
                    <p class="text-2xl font-bold text-blue-600">$<?= $s['precio'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Por qué elegirnos -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl font-bold mb-12">¿Por qué elegirnos?</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <?php
                $features = [
                    ['icon' => 'calendar-check', 'titulo' => 'Agenda Online', 'desc' => 'Reserva tu cita las 24 horas del día'],
                    ['icon' => 'users', 'titulo' => 'Barberos Profesionales', 'desc' => 'Equipo experimentado y capacitado'],
                    ['icon' => 'clock', 'titulo' => 'Horarios Flexibles', 'desc' => 'Abierto todos los días de la semana'],
                ];

                foreach ($features as $f):
                ?>
                <div>
                    <i class="fas fa-<?= $f['icon'] ?> text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2"><?= $f['titulo'] ?></h4>
                    <p class="text-gray-600"><?= $f['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto text-center">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
