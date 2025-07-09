<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'testimoni_app';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $exception) {
            // Try to create database if it doesn't exist
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";charset=utf8",
                    $this->username,
                    $this->password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );
                
                // Create database
                $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8 COLLATE utf8_general_ci");
                $this->conn->exec("USE " . $this->db_name);
                
                // Create tables
                $this->createTables();
                
            } catch(PDOException $e) {
                echo "Connection error: " . $e->getMessage();
                die();
            }
        }
        
        return $this->conn;
    }
    
    private function createTables() {
        try {
            // Users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user', 'admin') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $this->conn->exec($sql);
            
            // Testimoni table
            $sql = "CREATE TABLE IF NOT EXISTS testimoni (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                judul VARCHAR(255) NOT NULL,
                testimoni TEXT NOT NULL,
                rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                kategori VARCHAR(100) NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $this->conn->exec($sql);
            
            // Create default admin user
            $this->createDefaultAdmin();
            
        } catch(PDOException $e) {
            throw new Exception("Error creating tables: " . $e->getMessage());
        }
    }
    
    private function createDefaultAdmin() {
        try {
            // Check if admin exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $stmt->execute();
            $adminExists = $stmt->fetchColumn();
            
            if ($adminExists == 0) {
                // Create default admin
                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
                $stmt->execute(['admin', 'admin@testimoniapp.com', $hashedPassword]);
            }
            
            // Create some sample users if no users exist
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'user'");
            $stmt->execute();
            $userExists = $stmt->fetchColumn();
            
            if ($userExists == 0) {
                $sampleUsers = [
                    ['johndoe', 'john@example.com', 'password123'],
                    ['janedoe', 'jane@example.com', 'password123'],
                    ['bobsmith', 'bob@example.com', 'password123']
                ];
                
                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                foreach ($sampleUsers as $user) {
                    $hashedPassword = password_hash($user[2], PASSWORD_DEFAULT);
                    $stmt->execute([$user[0], $user[1], $hashedPassword]);
                }
                
                // Create sample testimoni
                $this->createSampleTestimoni();
            }
            
        } catch(PDOException $e) {
            // Ignore if admin already exists
        }
    }
    
    private function createSampleTestimoni() {
        try {
            $sampleTestimoni = [
                [
                    'user_id' => 2,
                    'judul' => 'Pelayanan Sangat Memuaskan',
                    'testimoni' => 'Saya sangat puas dengan pelayanan yang diberikan. Tim support sangat responsif dan membantu menyelesaikan masalah dengan cepat.',
                    'rating' => 5,
                    'kategori' => 'Layanan'
                ],
                [
                    'user_id' => 3,
                    'judul' => 'Produk Berkualitas Tinggi',
                    'testimoni' => 'Kualitas produk sangat baik dan sesuai dengan ekspektasi. Pengiriman juga cepat dan packaging aman.',
                    'rating' => 4,
                    'kategori' => 'Produk'
                ],
                [
                    'user_id' => 4,
                    'judul' => 'Aplikasi User-Friendly',
                    'testimoni' => 'Interface aplikasi sangat mudah digunakan dan intuitif. Fitur-fiturnya lengkap dan berfungsi dengan baik.',
                    'rating' => 5,
                    'kategori' => 'Aplikasi'
                ],
                [
                    'user_id' => 2,
                    'judul' => 'Pengalaman Berbelanja yang Menyenangkan',
                    'testimoni' => 'Proses pembelian mudah dan payment gateway berfungsi dengan lancar. Akan berbelanja lagi di sini.',
                    'rating' => 4,
                    'kategori' => 'Layanan'
                ]
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO testimoni (user_id, judul, testimoni, rating, kategori) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($sampleTestimoni as $testi) {
                $stmt->execute([
                    $testi['user_id'],
                    $testi['judul'],
                    $testi['testimoni'],
                    $testi['rating'],
                    $testi['kategori']
                ]);
            }
            
        } catch(PDOException $e) {
            // Ignore if sample data already exists
        }
    }
}
?>