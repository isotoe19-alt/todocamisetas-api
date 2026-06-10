<?php
// config/Database.php

class Database {
    private static $host = "localhost";
    private static $db_name = "todocamisetas_db";
    private static $username = "root";
    private static $password = ""; // Por defecto viene vacío en XAMPP
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                    self::$username,
                    self::$password
                );
                // Obligatorio para la rúbrica: Activar excepciones en errores SQL
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Error crítico de conexión: " . $exception->getMessage()
                ]);
                exit;
            }
        }
        return self::$conn;
    }
}