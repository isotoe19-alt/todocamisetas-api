<?php
// models/Talla.php
require_once __DIR__ . '/../config/Database.php';

class Talla {
    private static $table = "tallas";

    // Listar todas las tallas
    public static function all() {
        $db = Database::getConnection();
        $query = "SELECT * FROM " . self::$table;
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    }

    // Crear una nueva talla
    public static function create(array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $query = "INSERT INTO " . self::$table . " (nombre) VALUES (:nombre)";
            $stmt = $db->prepare($query);
            $stmt->execute([':nombre' => $data['nombre']]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Vincular una camiseta con una talla (Relación muchos a muchos)
    public static function linkCamisetaTalla(int $camisetaId, int $tallaId): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $query = "INSERT INTO camiseta_tallas (camiseta_id, talla_id) VALUES (:camiseta_id, :talla_id)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':camiseta_id' => $camisetaId,
                ':talla_id' => $tallaId
            ]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}