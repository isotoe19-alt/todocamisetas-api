<?php
// models/Camiseta.php
require_once __DIR__ . '/../config/Database.php';

class Camiseta {
    private static $table = "camisetas";

    // 1. Listar todas las camisetas
    public static function all() {
        $db = Database::getConnection();
        $query = "SELECT * FROM " . self::$table;
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    }

    // 2. Buscar camiseta por ID calculando el precio dinámico (
    public static function findWithPrice(int $id, ?int $clienteId = null) {
        $db = Database::getConnection();

        // Consultar los datos base de la camiseta
        $query = "SELECT * FROM " . self::$table . " WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $camiseta = $stmt->fetch();

        if (!$camiseta) return null;

        // Regla de Negocio por defecto: el precio final es el precio base
        $camiseta['precio_final'] = (int)$camiseta['precio'];

        // Si se nos provee un cliente, evaluamos su categoría para las ofertas (
        if ($clienteId !== null) {
            $queryCliente = "SELECT nombre_comercial, categoria, porcentaje_oferta FROM clientes WHERE id = :cliente_id LIMIT 1";
            $stmtCliente = $db->prepare($queryCliente);
            $stmtCliente->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmtCliente->execute();
            $cliente = $stmtCliente->fetch();

            if ($cliente) {
                // Caso A: Si es "90minutos" (Preferencial) y la camiseta posee un precio_oferta definido
                if ($cliente['nombre_comercial'] === '90minutos' && !is_null($camiseta['precio_oferta'])) {
                    $camiseta['precio_final'] = (int)$camiseta['precio_oferta'];
                }
                // Caso B: Si es "tdeportes" (Regular) se devuelve el precio base
                // Nota opcional: si aplica porcentaje general, restamos el descuento porcentual.
                elseif ($cliente['nombre_comercial'] === 'tdeportes') {
                    $camiseta['precio_final'] = (int)$camiseta['precio'];
                }
            }
        }

        // Incorporar las tallas usando un JOIN 
        $queryTallas = "SELECT t.nombre FROM tallas t 
                        JOIN camiseta_tallas ct ON t.id = ct.talla_id 
                        WHERE ct.camiseta_id = :camiseta_id";
        $stmtTallas = $db->prepare($queryTallas);
        $stmtTallas->bindParam(':camiseta_id', $id, PDO::PARAM_INT);
        $stmtTallas->execute();
        
        $camiseta['tallas_disponibles'] = array_column($stmtTallas->fetchAll(), 'nombre');

        return $camiseta;
    }

    
    public static function create(array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $query = "INSERT INTO " . self::$table . " 
                (sku, titulo, club, pais, tipo, color, precio, precio_oferta, detalles) 
                VALUES (:sku, :titulo, :club, :pais, :tipo, :color, :precio, :precio_oferta, :detalles)";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':sku'           => $data['sku'],
                ':titulo'        => $data['titulo'],
                ':club'          => $data['club'],
                ':pais'          => $data['pais'],
                ':tipo'          => $data['tipo'],
                ':color'         => $data['color'],
                ':precio'        => $data['precio'],
                ':precio_oferta' => $data['precio_oferta'] ?? null,
                ':detalles'      => $data['detalles'] ?? null
            ]);

            $camisetaId = $db->lastInsertId();

            // Si se mandaron tallas en el request (ej: ["M", "L"]), las enlazamos en la tabla intermedia
            if (!empty($data['tallas']) && is_array($data['tallas'])) {
                foreach ($data['tallas'] as $nombreTalla) {
                    // Buscar el ID de la talla según su nombre (S, M, L, XL)
                    $qTalla = "SELECT id FROM tallas WHERE nombre = :nombre LIMIT 1";
                    $sTalla = $db->prepare($qTalla);
                    $sTalla->execute([':nombre' => $nombreTalla]);
                    $talla = $sTalla->fetch();

                    if ($talla) {
                        $qIntermedia = "INSERT INTO camiseta_tallas (camiseta_id, talla_id) VALUES (:c_id, :t_id)";
                        $sIntermedia = $db->prepare($qIntermedia);
                        $sIntermedia->execute([':c_id' => $camisetaId, ':t_id' => $talla['id']]);
                    }
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}