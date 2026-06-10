<?php
// models/Cliente.php
require_once __DIR__ . '/../config/Database.php';

class Cliente {
    private static $table = "clientes";

    // Obtener todos los clientes B2B (Read)
    public static function all() {
        $db = Database::getConnection();
        $query = "SELECT * FROM " . self::$table;
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    }

    // Buscar un cliente por ID (Read)
    public static function find(int $id) {
        $db = Database::getConnection();
        $query = "SELECT * FROM " . self::$table . " WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear un nuevo cliente B2B (Create) usando transacciones
    public static function create(array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $query = "INSERT INTO " . self::$table . " 
                (nombre_comercial, rut, direccion, categoria, contacto_nombre, contacto_correo, porcentaje_oferta) 
                VALUES (:nombre_comercial, :rut, :direccion, :categoria, :contacto_nombre, :contacto_correo, :porcentaje_oferta)";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':nombre_comercial' => $data['nombre_comercial'],
                ':rut'              => $data['rut'],
                ':direccion'        => $data['direccion'],
                ':categoria'        => $data['categoria'] ?? 'Regular',
                ':contacto_nombre'  => $data['contacto_nombre'],
                ':contacto_correo'  => $data['contacto_correo'],
                ':porcentaje_oferta'=> $data['porcentaje_oferta'] ?? 0.00
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Eliminar un cliente con validación estricta 
    public static function delete(int $id): string {
        $db = Database::getConnection();
        
        // Validar si el cliente existe primero
        $cliente = self::find($id);
        if (!$cliente) {
            return "not_found";
        }

        // Simulación o validación de restricción de integridad 
        // Impedir eliminar clientes clave que tengan asignada una lógica crítica en el inventario
        if (in_array($cliente['nombre_comercial'], ['90minutos', 'tdeportes'])) {
            return "restricted"; // Bloqueo de eliminación solicitado por las reglas de negocio
        }

        try {
            $db->beginTransaction();
            $query = "DELETE FROM " . self::$table . " WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();
            return "success";
        } catch (Exception $e) {
            $db->rollBack();
            return "error";
        }
    }
}