<?php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Appointment.php';

class PaymentController {
    private $payment;
    private $appointment;

    public function __construct() {
        $this->payment = new Payment();
        $this->appointment = new Appointment();
    }

    public function generatePayment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barber_id = $_POST['barber_id'];
            $period_start = $_POST['period_start'];
            $period_end = $_POST['period_end'];

            // Obtener citas pendientes de pago
            $pendingCommissions = $this->payment->getPendingCommissions($barber_id, $period_start, $period_end);
            
            if (empty($pendingCommissions)) {
                $_SESSION['error'] = 'No hay comisiones pendientes para el período seleccionado';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                return;
            }

            // Calcular totales usando la comisión calculada correctamente
            $total_commissions = array_sum(array_column($pendingCommissions, 'calculated_commission'));
            $total_appointments = count($pendingCommissions);

            // Crear el pago
            $this->payment->barber_id = $barber_id;
            $this->payment->period_start = $period_start;
            $this->payment->period_end = $period_end;
            $this->payment->total_commissions = $total_commissions;
            $this->payment->total_appointments = $total_appointments;
            $this->payment->created_by = $_SESSION['user_id'];

            if ($this->payment->create()) {
                // Agregar detalles del pago
                if ($this->payment->addPaymentDetails($this->payment->id, $pendingCommissions)) {
                    $_SESSION['success'] = 'Pago generado exitosamente por $' . number_format($total_commissions, 2);
                } else {
                    $_SESSION['error'] = 'Error al agregar detalles del pago';
                }
            } else {
                $_SESSION['error'] = 'Error al generar el pago';
            }

            header('Location: payments.php');
        }
    }

    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $payment_id = $_POST['payment_id'];
            $payment_method = $_POST['payment_method'];
            $notes = $_POST['notes'] ?? '';

            if ($this->payment->updateStatus($payment_id, 'paid', $payment_method, $notes)) {
                $_SESSION['success'] = 'Pago procesado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al procesar el pago';
            }

            header('Location: payments.php');
        }
    }
}
?>
