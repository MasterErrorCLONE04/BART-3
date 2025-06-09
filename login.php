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

// Procesar login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $authController = new AuthController();
    $authController->login();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        
        .bg-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, #667eea 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #764ba2 0%, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 bg-pattern relative overflow-hidden">
    <!-- Elementos decorativos de fondo -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-32 h-32 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full opacity-20 animate-float"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-gradient-to-r from-pink-400 to-red-500 rounded-full opacity-20 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-1/4 w-40 h-40 bg-gradient-to-r from-green-400 to-blue-500 rounded-full opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-40 right-1/3 w-28 h-28 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full opacity-20 animate-float" style="animation-delay: 0.5s;"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8 animate-slide-up">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-2xl animate-float">
                    <i class="fas fa-cut text-2xl text-white"></i>
                </div>
                <h2 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Bienvenido de vuelta
                </h2>
                <p class="mt-3 text-gray-600 text-lg">Inicia sesión en tu cuenta</p>
            </div>

            <!-- Formulario -->
            <div class="glass-effect rounded-2xl shadow-2xl p-8 space-y-6 animate-fade-in">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg animate-slide-up">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-red-700"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg animate-slide-up">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-green-700"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" method="POST">
                    <div class="space-y-5">
                        <div class="relative">
                            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-indigo-500"></i>Usuario o Email
                            </label>
                            <input id="username" name="username" type="text" required 
                                   class="input-focus block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white/70"
                                   placeholder="Ingresa tu usuario o email">
                        </div>
                        
                        <div class="relative">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-indigo-500"></i>Contraseña
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required 
                                       class="input-focus block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white/70 pr-12"
                                       placeholder="Ingresa tu contraseña">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i id="toggleIcon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition-colors"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Recordarme
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="btn-gradient w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Iniciar Sesión
                        </button>
                    </div>

                    <div class="text-center space-y-3">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">o</span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600">
                            ¿No tienes cuenta? 
                            <a href="register.php" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                                Regístrate aquí
                            </a>
                        </p>
                        <p class="text-sm text-gray-600">
                            <a href="index.php" class="font-medium text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-arrow-left mr-1"></i>Volver al inicio
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © 2024 <?php echo SITE_NAME; ?>. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Animación de entrada para los elementos
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate-slide-up');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>