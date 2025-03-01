<?php
function getDatabaseConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'backpack_store';
        $user = 'root';
        $password = 'root';

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'Database connection error']));
        }
    }
    return $pdo;
}
?>
