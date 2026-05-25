<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: X-Admin-Key, Content-Type');

$SECRET_KEY = "Omar_SecOps_2026!";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$headers = getallheaders();
$adminKey = null;
foreach ($headers as $k => $v) {
    if (strtolower($k) === 'x-admin-key') { $adminKey = $v; break; }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!file_exists('data.json')) {
        echo json_encode(["certificates" => [], "projects" => [], "labs" => []]);
    } else {
        echo file_get_contents('data.json');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($adminKey !== $SECRET_KEY) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }
    $data = file_get_contents("php://input");
    $json = json_decode($data, true);
    if ($json !== null) {
        file_put_contents('data.json', json_encode($json, JSON_PRETTY_PRINT));
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    }
}
?>