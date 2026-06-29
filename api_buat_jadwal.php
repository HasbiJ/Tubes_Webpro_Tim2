<?php
header("Content-Type: application/json");
include "config/koneksi.php";

// Verifikasi API Key
$headers = getallheaders();
if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== 'eduflex2026_secret') {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST': // Tambah Jadwal
        $query = "INSERT INTO jadwal (id_kelas, id_mapel, hari, jam_mulai, jam_selesai, ruang) 
                  VALUES ('{$data['id_kelas']}', '{$data['id_mapel']}', '{$data['hari']}', 
                          '{$data['jam_mulai']}', '{$data['jam_selesai']}', '{$data['ruang']}')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Jadwal berhasil ditambahkan"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;

    case 'PUT': // Update Jadwal
        $query = "UPDATE jadwal SET id_kelas='{$data['id_kelas']}', id_mapel='{$data['id_mapel']}', 
                  hari='{$data['hari']}', jam_mulai='{$data['jam_mulai']}', 
                  jam_selesai='{$data['jam_selesai']}', ruang='{$data['ruang']}' 
                  WHERE id_jadwal='{$data['id_jadwal']}'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Jadwal berhasil diupdate"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;

    case 'DELETE': // Hapus Jadwal
        if (mysqli_query($conn, "DELETE FROM jadwal WHERE id_jadwal = '{$data['id_jadwal']}'")) {
            echo json_encode(["status" => "success", "message" => "Jadwal berhasil dihapus"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
        break;
}
?>