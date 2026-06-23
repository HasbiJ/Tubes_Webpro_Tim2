<?php
header("Content-Type: application/json");
include "config/koneksi.php";

$headers = getallheaders();
if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== 'eduflex2026_secret') {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = ($method !== 'GET') ? json_decode(file_get_contents("php://input"), true) : null;

switch ($method) {
    case 'GET': // Ambil data
        $query = mysqli_query($conn, "SELECT id_user, username, role FROM users");
        echo json_encode(["status" => "success", "data" => mysqli_fetch_all($query, MYSQLI_ASSOC)]);
        break;

    case 'POST': // Tambah User
        $pass = password_hash($data['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, role) VALUES ('{$data['username']}', '$pass', '{$data['role']}')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "User berhasil ditambahkan"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;

    case 'PUT': // Update User
        $query = "UPDATE users SET role = '{$data['role']}' WHERE id_user = '{$data['id_user']}'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "User berhasil diupdate"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;

    case 'DELETE': // Hapus User
        if (mysqli_query($conn, "DELETE FROM users WHERE id_user = '{$data['id_user']}'")) {
            echo json_encode(["status" => "success", "message" => "User berhasil dihapus"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;
}
?>