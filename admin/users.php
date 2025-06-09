<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/User.php';

requireRole('admin');

$user = new User();
$action = $_GET['action'] ?? 'list';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'create') {
        $user->username = trim($_POST['username']);
        $user->email = trim($_POST['email']);
        $user->password = trim($_POST['password']);
        $user->first_name = trim($_POST['first_name']);
        $user->last_name = trim($_POST['last_name']);
        $user->phone = trim($_POST['phone']);
        $user->role_id = intval($_POST['role_id']);

        // Validaciones
        if ($user->emailExists($user->email)) {
            $_SESSION['error'] = 'El email ya está registrado';
        } elseif ($user->usernameExists($user->username)) {
            $_SESSION['error'] = 'El nombre de usuario ya está en uso';
        } elseif ($user->register()) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
            header('Location: users.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
        }
    }
}

// Obtener usuarios
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT u.*, r.name as role_name 
          FROM users u 
          JOIN roles r ON u.role_id = r.id 
          ORDER BY u.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener roles
$query = "SELECT * FROM roles ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - <?php echo SITE_NAME; ?></title>
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
                    <i class="fas fa-users text-2xl text-blue-600 mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Gestión de Usuarios</h1>
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
                <a href="users.php" class="text-white px-3 py-4 text-sm font-medium border-b-2 border-blue-500">
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
            <!-- Formulario de Crear Usuario -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Crear Nuevo Usuario</h3>
                    
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            <label for="role_id" class="block text-sm font-medium text-gray-700">
                                Rol *
                            </label>
                            <select id="role_id" name="role_id" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un rol</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo ($_GET['role'] ?? '') == $role['name'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Contraseña *
                            </label>
                            <input id="password" name="password" type="password" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex justify-between">
                            <a href="users.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Lista de Usuarios -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Lista de Usuarios</h3>
                        <div class="space-x-2">
                            <a href="users.php?action=create&role=barbero" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class="fas fa-user-plus mr-2"></i>Nuevo Barbero
                            </a>
                            <a href="users.php?action=create" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $usr): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($usr['first_name'] . ' ' . $usr['last_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($usr['username']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($usr['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($usr['phone'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $roleColors = [
                                                'admin' => 'bg-red-100 text-red-800',
                                                'barbero' => 'bg-blue-100 text-blue-800',
                                                'cliente' => 'bg-green-100 text-green-800'
                                            ];
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $roleColors[$usr['role_name']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                                <?php echo ucfirst($usr['role_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $usr['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $usr['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('d/m/Y', strtotime($usr['created_at'])); ?>
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
