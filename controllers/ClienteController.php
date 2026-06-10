<?php
// controllers/ClienteController.php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {

    // Listar todos los clientes
    public static function index() {
        $clientes = Cliente::all();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $clientes
        ]);
    }

    // Crear un nuevo cliente con validaciones estrictas
    public static function store() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        
        if (empty($data['nombre_comercial']) || empty($data['rut']) || empty($data['contacto_correo'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios: nombre_comercial, rut o contacto_correo."
            ]);
            return;
        }

        if (Cliente::create($data)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Cliente B2B registrado con éxito."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo guardar el cliente debido a un problema interno o RUT duplicado."
            ]);
        }
    }

   
    public static function destroy(int $id) {
        $resultado = Cliente::delete($id);

        if ($resultado === "not_found") {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "El cliente con ID $id no existe."
            ]);
        } elseif ($resultado === "restricted") {
            http_response_code(400); // Bad Request por regla de negocio violada
            echo json_encode([
                "status" => "error",
                "message" => "No se puede eliminar este cliente porque está vinculado a la lógica de cotizaciones de camisetas."
            ]);
        } elseif ($resultado === "success") {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Cliente eliminado exitosamente."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error de base de datos al intentar eliminar."
            ]);
        }
    }
}