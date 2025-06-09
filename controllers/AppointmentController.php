<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/User.php';

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
            $appointment_id = $_POST['appointment_id'];
            $status = $_POST['status'];

            if ($this->appointment->updateStatus($appointment_id, $status)) {
                $_SESSION['success'] = 'Estado de la cita actualizado';
            } else {
                $_SESSION['error'] = 'Error al actualizar el estado';
            }

            // Redirigir según el rol
            if ($_SESSION['role'] == 'barbero') {
                header('Location: barber/dashboard.php');
            } else {
                header('Location: admin/appointments.php');
            }
        }
    }
}
?>
