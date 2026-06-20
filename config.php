<?php
// =============================================
// KONFIGURASI KONEKSI DATABASE
// Sesuaikan jika user/password MySQL XAMPP-mu berbeda
// =============================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "nippon_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Koneksi database gagal: " . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");

// Ambil body JSON dari request POST
function getInput() {
    $data = json_decode(file_get_contents("php://input"), true);
    return $data ?? [];
}

function sendJson($data) {
    echo json_encode($data);
    exit();
}
