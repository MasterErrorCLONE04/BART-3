<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class AppointmentController {
    private $appointment;
    private $service;
    private $user;

    public function __construct() {
        $this->appointment = new Appointment();
        $this->service = new Service();
        $this->user = new User();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->appointment->client_id = $_SESSION['user_id'];
            $this->appointment->barber_id = $_POST['barber_id'];
            $this->appointment->service_id = $_POST['service_id'];
            $this->appointment->appointment_date = $_POST['appointment_date'];
            $this->appointment->appointment_time = $_POST['appointment_time'];

            // Obtener información del servicio
            $serviceData = $this->service->getById($this->appointment->service_id);
            $this->appointment->total_amount = $serviceData['price'];
            $this->appointment->commission_amount = ($serviceData['price'] * $serviceData['commission_percentage']) / 100;

            // Verificar disponibilidad
            if (!$this->appointment->isTimeAvailable(
                $this->appointment->barber_id,
                $this->appointment->appointment_date,
                $this->appointment->appointment_time,
                $serviceData['duration_minutes']
            )) {
                $_SESSION['error'] = 'El horario seleccionado no está disponible';
                header('Location: client/book-appointment.php');
                return;
            }

            if ($this->appointment->create()) {
                $_SESSION['success'] = 'Cita agendada exitosamente';
                header('Location: client/dashboard.php');
            } else {
                $_SESSION['error'] = 'Error al agendar la cita';
                header('Location: client/book-appointment.php');
            }
        }
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Si viene del botón de confirmar servicio
            if (isset($_POST['appointment_id']) && !isset($_POST['status'])) {
                $appointment_id = $_POST['appointment_id'];
                
                $database = new Database();
                $conn = $database->getConnection();
                
                // Primero obtener los datos de la cita y el servicio para calcular la comisión
                $query = "SELECT a.*, s.price, s.commission_percentage 
                          FROM appointments a 
                          JOIN services s ON a.service_id = s.id 
                          WHERE a.id = :appointment_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':appointment_id', $appointment_id);
                $stmt->execute();
                $appointmentData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($appointmentData) {
                    // Calcular la comisión correcta
                    $commission_amount = ($appointmentData['price'] * $appointmentData['commission_percentage']) / 100;
                    
                    // Actualizar la cita con el estado completado y la comisión calculada
                    $query = "UPDATE appointments 
                              SET status = 'completed', 
                                  confirmed_at = NOW(), 
                                  confirmed_by = :confirmed_by,
                                  commission_amount = :commission_amount
                              WHERE id = :appointment_id";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':confirmed_by', $_SESSION['user_id']);
                    $stmt->bindParam(':commission_amount', $commission_amount);
                    $stmt->bindParam(':appointment_id', $appointment_id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success'] = 'Servicio confirmado exitosamente. Comisión de $' . number_format($commission_amount, 2) . ' (' . $appointmentData['commission_percentage'] . '%) disponible para pago.';
                    } else {
                        $_SESSION['error'] = 'Error al confirmar el servicio';
                    }
                } else {
                    $_SESSION['error'] = 'No se encontró la cita';
                }
                
                header('Location: ' . ($_SESSION['role'] == 'barbero' ? '../barber/dashboard.php' : '../admin/appointments.php'));
                exit();
            }
            
            // Lógica original para otros cambios de estado
            $appointment_id = $_POST['appointment_id'];
            $status = $_POST['status'];

            if ($this->appointment->updateStatus($appointment_id, $status)) {
                $_SESSION['success'] = 'Estado de la cita actualizado';
            } else {
                $_SESSION['error'] = 'Error al actualizar el estado';
            }

            // Redirigir según el rol
            if ($_SESSION['role'] == 'barbero') {
                header('Location: ../barber/dashboard.php');
            } else {
                header('Location: ../admin/appointments.php');
            }
        }
    }
}
?>
