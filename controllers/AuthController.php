<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos';
                header('Location: login.php');
                return;
            }

            $userData = $this->user->login($username, $password);
            
            if ($userData) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role_name'];
                $_SESSION['full_name'] = $userData['first_name'] . ' ' . $userData['last_name'];

                // Registrar sesión
                $this->logSession($userData['id']);

                // Redirigir según el rol
                switch ($userData['role_name']) {
                    case 'admin':
                        header('Location: admin/dashboard.php');
                        break;
                    case 'barbero':
                        header('Location: barber/dashboard.php');
                        break;
                    case 'cliente':
                        header('Location: client/dashboard.php');
                        break;
                    default:
                        header('Location: index.php');
                }
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas';
                header('Location: login.php');
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->username = trim($_POST['username']);
            $this->user->email = trim($_POST['email']);
            $this->user->password = trim($_POST['password']);
            $this->user->first_name = trim($_POST['first_name']);
            $this->user->last_name = trim($_POST['last_name']);
            $this->user->phone = trim($_POST['phone']);
            $this->user->role_id = 3; // Cliente por defecto

            // Validaciones
            if (empty($this->user->username) || empty($this->user->email) || 
                empty($this->user->password) || empty($this->user->first_name) || 
                empty($this->user->last_name)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
                header('Location: register.php');
                return;
            }

            if ($this->user->emailExists($this->user->email)) {
                $_SESSION['error'] = 'El email ya está registrado';
                header('Location: register.php');
                return;
            }

            if ($this->user->usernameExists($this->user->username)) {
                $_SESSION['error'] = 'El nombre de usuario ya está en uso';
                header('Location: register.php');
                return;
            }

            if ($this->user->register()) {
                $_SESSION['success'] = 'Registro exitoso. Puede iniciar sesión';
                header('Location: login.php');
            } else {
                $_SESSION['error'] = 'Error al registrar usuario';
                header('Location: register.php');
            }
        }
    }

    public function logout() {
        // Registrar logout
        if (isset($_SESSION['user_id'])) {
            $this->logLogout($_SESSION['user_id']);
        }
        
        session_destroy();
        header('Location: index.php');
    }

    private function logSession($user_id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $session_id = session_id();
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $query = "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent) 
                  VALUES (:user_id, :session_id, :ip_address, :user_agent)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
    }

    private function logLogout($user_id) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $session_id = session_id();
        
        $query = "UPDATE user_sessions SET logout_time = NOW(), is_active = 0 
                  WHERE user_id = :user_id AND session_id = :session_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
    }
}
?>
