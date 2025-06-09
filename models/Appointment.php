<?php
require_once __DIR__ . '/../config/database.php';

class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $client_id;
    public $barber_id;
    public $service_id;
    public $appointment_date;
    public $appointment_time;
    public $status;
    public $notes;
    public $total_amount;
    public $commission_amount;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (client_id, barber_id, service_id, appointment_date, appointment_time, total_amount, commission_amount) 
                  VALUES (:client_id, :barber_id, :service_id, :appointment_date, :appointment_time, :total_amount, :commission_amount)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':client_id', $this->client_id);
        $stmt->bindParam(':barber_id', $this->barber_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':appointment_date', $this->appointment_date);
        $stmt->bindParam(':appointment_time', $this->appointment_time);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':commission_amount', $this->commission_amount);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getByClient($client_id) {
        $query = "SELECT a.*, s.name as service_name, s.price, s.duration_minutes,
                         CONCAT(b.first_name, ' ', b.last_name) as barber_name
                  FROM " . $this->table_name . " a
                  JOIN services s ON a.service_id = s.id
                  JOIN users b ON a.barber_id = b.id
                  WHERE a.client_id = :client_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByBarber($barber_id, $date = null) {
        $query = "SELECT a.*, s.name as service_name, s.price, s.duration_minutes,
                         CONCAT(c.first_name, ' ', c.last_name) as client_name,
                         c.phone as client_phone
                  FROM " . $this->table_name . " a
                  JOIN services s ON a.service_id = s.id
                  JOIN users c ON a.client_id = c.id
                  WHERE a.barber_id = :barber_id";
        
        if ($date) {
            $query .= " AND a.appointment_date = :date";
        }
        
        $query .= " ORDER BY a.appointment_date, a.appointment_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':barber_id', $barber_id);
        if ($date) {
            $stmt->bindParam(':date', $date);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function isTimeAvailable($barber_id, $date, $time, $duration) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE barber_id = :barber_id 
                  AND appointment_date = :date 
                  AND status != 'cancelled'
                  AND (
                      (appointment_time <= :time AND ADDTIME(appointment_time, SEC_TO_TIME(:duration * 60)) > :time)
                      OR 
                      (appointment_time < ADDTIME(:time, SEC_TO_TIME(:duration * 60)) AND appointment_time >= :time)
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':barber_id', $barber_id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':duration', $duration);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }
}
?>
