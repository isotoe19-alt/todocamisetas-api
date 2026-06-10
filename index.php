<?php
// index.php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/todocamisetas-api';
$uri = str_replace($basePath, '', $requestUri);

if (strpos($uri, '/index.php') === 0) {
    $uri = substr($uri, strlen('/index.php'));
}
if (empty($uri)) { $uri = '/'; }

// Importar todos los controladores obligatorios
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/CamisetaController.php';
require_once __DIR__ . '/controllers/TallaController.php';

// --- ENRUTADOR CON EXPRESIONES REGULARES ---

if ($method === 'GET' && $uri === '/test') {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "¡API activa!"]);
    exit;
} 

// 1. Rutas de Clientes B2B
elseif ($method === 'GET' && $uri === '/clientes') {
    ClienteController::index();
} 
elseif ($method === 'POST' && $uri === '/clientes') {
    ClienteController::store();
} 
elseif ($method === 'DELETE' && preg_match('#^/clientes/([0-9]+)$#', $uri, $matches)) {
    ClienteController::destroy((int)$matches[1]);
}

// 2. Rutas de Camisetas (Stock)
elseif ($method === 'GET' && $uri === '/camisetas') {
    CamisetaController::index();
}
elseif ($method === 'POST' && $uri === '/camisetas') {
    CamisetaController::store();
}
elseif ($method === 'GET' && preg_match('#^/camisetas/([0-9]+)$#', $uri, $matches)) {
    CamisetaController::show((int)$matches[1]);
}

// 3. Rutas de Tallas (Muchos a Muchos)
elseif ($method === 'GET' && $uri === '/tallas') {
    TallaController::index();
}
elseif ($method === 'POST' && $uri === '/tallas') {
    TallaController::store();
}
elseif ($method === 'POST' && $uri === '/camisetas-tallas') {
    TallaController::attach();
}

// 4. Error 404 por defecto
else {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Endpoint no encontrado.",
        "uri_detectada" => $uri
    ]);
}