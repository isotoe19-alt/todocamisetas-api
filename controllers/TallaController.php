<?php
// controllers/TallaController.php
require_once __DIR__ . '/../models/Talla.php';

class TallaController {

    public static function index() {
        $tallas = Talla::all();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $tallas
        ]);
    }

    public static function store() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "El campo 'nombre' es obligatorio para la talla."
            ]);
            return;
        }

        if (Talla::create($data)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Talla registrada correctamente."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error interno o la talla ya se encuentra registrada."
            ]);
        }
    }

    // Endpoint para asociar una camiseta existente con una talla
    public static function attach() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data['camiseta_id']) || empty($data['talla_id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Los campos 'camiseta_id' y 'talla_id' son obligatorios."
            ]);
            return;
        }

        if (Talla::linkCamisetaTalla((int)$data['camiseta_id'], (int)$data['talla_id'])) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Relación muchos a muchos establecida con éxito."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo establecer el vínculo relacional."
            ]);
        }
    }
}