<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $phone;
    public $role_id;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT u.*, r.name as role_name 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE (u.username = :username OR u.email = :username) 
                  AND u.is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, first_name, last_name, phone, role_id) 
                  VALUES (:username, :email, :password, :first_name, :last_name, :phone, :role_id)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':role_id', $this->role_id);

        return $stmt->execute();
    }

    public function getBarbers() {
        $query = "SELECT u.*, r.name as role_name 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE r.name = 'barbero' AND u.is_active = 1 
                  ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $query = "SELECT u.*, r.name as role_name 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
