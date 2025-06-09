<?php
require_once __DIR__ . '/../config/database.php';

class Service {
    private $conn;
    private $table_name = "services";

    public $id;
    public $name;
    public $description;
    public $price;
    public $duration_minutes;
    public $commission_percentage;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, price, duration_minutes, commission_percentage) 
                  VALUES (:name, :description, :price, :duration_minutes, :commission_percentage)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':commission_percentage', $this->commission_percentage);

        return $stmt->execute();
    }
}
?>