<?php
$host = '127.0.0.1';
$dbname = 'attendance_system_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Add parent columns if not exists
    $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS parent_name VARCHAR(100) DEFAULT ''");
    $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS parent_contact VARCHAR(20) DEFAULT '09056689672'");
    
    // Create teachers table if not exists, with age & address
    $pdo->exec("CREATE TABLE IF NOT EXISTS teachers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        subject VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        contact VARCHAR(20),
        profile_picture TEXT,
        bio TEXT,
        age INT,
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Add columns if they don't exist (for existing installations)
    $pdo->exec("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS age INT");
    $pdo->exec("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS address TEXT");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>