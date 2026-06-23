<?php
// Set header agar merespon dalam format JSON
header("Content-Type: application/json");
include "../config/koneksi.php";

$method = $_SERVER['REQUEST_METHOD'];
// Mengambil input data dari request (bukan form tradisional)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

switch ($method) {
    // 1. READ (GET)
    case 'GET':
        $query = mysqli_query($conn, "SELECT * FROM jadwal");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        echo json_encode(["status" => "success", "data" => $result]);
        break;

    // 2. CREATE (POST)
    case 'POST':
        $q = "INSERT INTO jadwal (id_kelas, id_mapel, hari, jam_mulai, jam_selesai, ruang) 
              VALUES ('{$data['id_kelas']}', '{$data['id_mapel']}', '{$data['hari']}', '{$data['jam_mulai']}', '{$data['jam_selesai']}', '{$data['ruang']}')";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success", "message" => "Data ditambah"]) : json_encode(["status" => "error"]);
        break;

    // 3. UPDATE (PUT)
    case 'PUT':
        $q = "UPDATE jadwal SET id_kelas='{$data['id_kelas']}', id_mapel='{$data['id_mapel']}', hari='{$data['hari']}', 
              jam_mulai='{$data['jam_mulai']}', jam_selesai='{$data['jam_selesai']}', ruang='{$data['ruang']}' 
              WHERE id_jadwal='{$data['id_jadwal']}'";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success", "message" => "Data diupdate"]) : json_encode(["status" => "error"]);
        break;

    // 4. DELETE (DELETE)
    case 'DELETE':
        $id = $data['id_jadwal'];
        $q = "DELETE FROM jadwal WHERE id_jadwal = '$id'";
        echo mysqli_query($conn, $q) ? json_encode(["status" => "success", "message" => "Data dihapus"]) : json_encode(["status" => "error"]);
        break;
}
?>