<?php
// controllers/CamisetaController.php
require_once __DIR__ . '/../models/Camiseta.php';

class CamisetaController {

    // Listar todo el catálogo
    public static function index() {
        $camisetas = Camiseta::all();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $camisetas
        ]);
    }

    // Mostrar detalle de una camiseta con cálculo de precio dinámico
    public static function show(int $id) {
        // Capturamos el parámetro opcional ?cliente_id=X desde la URL query string
        $clienteId = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : null;

        $camiseta = Camiseta::findWithPrice($id, $clienteId);

        if (!$camiseta) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "La camiseta solicitada no existe."
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $camiseta
        ]);
    }

    // Guardar una nueva camiseta con validación de campos obligatorios
    public static function store() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validación estricta exigida en rúbrica (título, club, precio, sku)
        if (empty($data['titulo']) || empty($data['club']) || empty($data['precio']) || empty($data['sku'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan campos mandatorios: titulo, club, precio, sku."
            ]);
            return;
        }

        if (Camiseta::create($data)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Camiseta ingresada correctamente al inventario."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo crear el registro (RUT/SKU posiblemente duplicado)."
            ]);
        }
    }
}