<?php
header("Content-Type: application/json");
include "../config/koneksi.php";

$method = $_SERVER['REQUEST_METHOD'];
$data = ($method !== 'GET') ? json_decode(file_get_contents("php://input"), true) : null;

switch ($method) {
    case 'GET':
        $q = mysqli_query($conn, "SELECT * FROM users");
        echo json_encode(["status" => "success", "data" => mysqli_fetch_all($q, MYSQLI_ASSOC)]);
        break;
    case 'POST':
        $pass = password_hash($data['password'], PASSWORD_DEFAULT);
        $q = "INSERT INTO users (username, password, role) VALUES ('{$data['username']}', '$pass', '{$data['role']}')";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success"]) : json_encode(["status" => "error"]);
        break;
    case 'PUT':
        $q = "UPDATE users SET role = '{$data['role']}' WHERE id_user = '{$data['id_user']}'";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success"]) : json_encode(["status" => "error"]);
        break;
    case 'DELETE':
        $q = "DELETE FROM users WHERE id_user = '{$data['id_user']}'";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success"]) : json_encode(["status" => "error"]);
        break;
}