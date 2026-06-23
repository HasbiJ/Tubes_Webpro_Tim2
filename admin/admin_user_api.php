<?php
header('Content-Type: application/json');
include "../config/koneksi.php";

// Autentikasi API Key
$headers = getallheaders();
$apiKey = $headers['API-KEY'] ?? ($_SERVER['HTTP_API_KEY'] ?? '');

if ($apiKey !== 'EduFlexKey2026') {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $res = mysqli_query($conn, "SELECT * FROM users");
        echo json_encode(mysqli_fetch_all($res, MYSQLI_ASSOC));
        break;

    case 'POST':
        $u = $input['username'];
        $r = $input['role'];
        $p = password_hash($input['password'] ?? '123456', PASSWORD_DEFAULT);
        $query = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$u', '$p', '$r')");
        echo json_encode(['status' => $query ? 'sukses' : 'gagal']);
        break;

        // METHOD 3: PUT (UPDATE)
    case 'PUT':
        $id = $input['id_user'];
        $u = $input['username'];
        $r = $input['role'];
        
        $query = mysqli_query($conn, "UPDATE users SET username='$u', role='$r' WHERE id_user='$id'");
        
        if ($query) {
            echo json_encode(['status' => 'sukses']);
        } else {
            echo json_encode(['status' => 'gagal', 'error' => mysqli_error($conn)]);
        }
        break;
        
    case 'DELETE':
        $id = $input['id_user'] ?? 0;
        // Matikan foreign key sementara agar tidak error saat hapus relasi
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
        $query = mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id'");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
        echo json_encode(['status' => $query ? 'sukses' : 'gagal']);
        break;
}
?>