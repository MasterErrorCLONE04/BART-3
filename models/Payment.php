<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;
    private $table_name = "barber_payments";

    public $id;
    public $barber_id;
    public $period_start;
    public $period_end;
    public $total_commissions;
    public $total_appointments;
    public $status;
    public $payment_date;
    public $payment_method;
    public $notes;
    public $created_by;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (barber_id, period_start, period_end, total_commissions, total_appointments, created_by) 
                  VALUES (:barber_id, :period_start, :period_end, :total_commissions, :total_appointments, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':barber_id', $this->barber_id);
        $stmt->bindParam(':period_start', $this->period_start);
        $stmt->bindParam(':period_end', $this->period_end);
        $stmt->bindParam(':total_commissions', $this->total_commissions);
        $stmt->bindParam(':total_appointments', $this->total_appointments);
        $stmt->bindParam(':created_by', $this->created_by);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getPendingCommissions($barber_id, $start_date = null, $end_date = null) {
        $query = "SELECT a.*, s.name as service_name, s.price, s.commission_percentage,
                         CONCAT(c.first_name, ' ', c.last_name) as client_name,
                         (s.price * s.commission_percentage / 100) as calculated_commission
                  FROM appointments a
                  JOIN services s ON a.service_id = s.id
                  JOIN users c ON a.client_id = c.id
                  WHERE a.barber_id = :barber_id 
                  AND a.status = 'completed' 
                  AND a.id NOT IN (SELECT appointment_id FROM payment_details)";
        
        if ($start_date && $end_date) {
            $query .= " AND a.appointment_date BETWEEN :start_date AND :end_date";
        }
        
        $query .= " ORDER BY a.appointment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':barber_id', $barber_id);
        
        if ($start_date && $end_date) {
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentsByBarber($barber_id) {
        $query = "SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                  FROM " . $this->table_name . " p
                  JOIN users u ON p.created_by = u.id
                  WHERE p.barber_id = :barber_id
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':barber_id', $barber_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPayments() {
        $query = "SELECT p.*, 
                         CONCAT(b.first_name, ' ', b.last_name) as barber_name,
                         CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                  FROM " . $this->table_name . " p
                  JOIN users b ON p.barber_id = b.id
                  JOIN users u ON p.created_by = u.id
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $payment_method = null, $notes = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, payment_date = NOW(), payment_method = :payment_method, notes = :notes
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function addPaymentDetails($payment_id, $appointments) {
        try {
            $this->conn->beginTransaction();
            
            foreach ($appointments as $appointment) {
                // Usar la comisión calculada correctamente
                $commission_amount = $appointment['calculated_commission'];
                
                $query = "INSERT INTO payment_details (payment_id, appointment_id, commission_amount) 
                          VALUES (:payment_id, :appointment_id, :commission_amount)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':payment_id', $payment_id);
                $stmt->bindParam(':appointment_id', $appointment['id']);
                $stmt->bindParam(':commission_amount', $commission_amount);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getPaymentDetails($payment_id) {
        $query = "SELECT pd.*, a.appointment_date, a.appointment_time, s.name as service_name, s.price, s.commission_percentage,
                         CONCAT(c.first_name, ' ', c.last_name) as client_name
                  FROM payment_details pd
                  JOIN appointments a ON pd.appointment_id = a.id
                  JOIN services s ON a.service_id = s.id
                  JOIN users c ON a.client_id = c.id
                  WHERE pd.payment_id = :payment_id
                  ORDER BY a.appointment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
