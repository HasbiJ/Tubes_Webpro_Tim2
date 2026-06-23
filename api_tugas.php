<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "eduflex");

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . mysqli_connect_error()
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'OPTIONS') {
    exit(0);
}

/* =========================
   GET
========================= */
if ($method == 'GET') {

    // dropdown mapel
    if (isset($_GET['action']) && $_GET['action'] == 'mapel') {
        $result = mysqli_query($conn, "
            SELECT id_mapel, nama_mapel
            FROM mata_pelajaran
            ORDER BY nama_mapel ASC
        ");

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
        exit;
    }

    // ambil 1 tugas
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = mysqli_query($conn, "
            SELECT 
                tugas.id_tugas,
                tugas.id_mapel,
                tugas.judul,
                tugas.deskripsi,
                tugas.deadline,
                mata_pelajaran.nama_mapel
            FROM tugas
            JOIN mata_pelajaran ON tugas.id_mapel = mata_pelajaran.id_mapel
            WHERE tugas.id_tugas = '$id'
        ");

        $data = mysqli_fetch_assoc($query);

        if ($data) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data tugas tidak ditemukan"
            ]);
        }
        exit;
    }

    // ambil semua tugas
    $result = mysqli_query($conn, "
        SELECT 
            tugas.id_tugas,
            tugas.id_mapel,
            tugas.judul,
            tugas.deskripsi,
            tugas.deadline,
            mata_pelajaran.nama_mapel
        FROM tugas
        JOIN mata_pelajaran ON tugas.id_mapel = mata_pelajaran.id_mapel
        ORDER BY tugas.id_tugas DESC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
}

/* =========================
   POST
========================= */
elseif ($method == 'POST') {

    $id_mapel  = $_POST['id_mapel'] ?? '';
    $judul     = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $deadline  = $_POST['deadline'] ?? '';

    if ($id_mapel == '' || $judul == '' || $deskripsi == '' || $deadline == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Semua field wajib diisi"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        INSERT INTO tugas (id_mapel, judul, deskripsi, deadline)
        VALUES ('$id_mapel', '$judul', '$deskripsi', '$deadline')
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil ditambahkan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menambahkan tugas: " . mysqli_error($conn)
        ]);
    }
}

/* =========================
   PUT
========================= */
elseif ($method == 'PUT') {

    parse_str(file_get_contents("php://input"), $putData);

    $id        = $putData['id_tugas'] ?? '';
    $id_mapel  = $putData['id_mapel'] ?? '';
    $judul     = $putData['judul'] ?? '';
    $deskripsi = $putData['deskripsi'] ?? '';
    $deadline  = $putData['deadline'] ?? '';

    if ($id == '' || $id_mapel == '' || $judul == '' || $deskripsi == '' || $deadline == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Data update tidak lengkap"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        UPDATE tugas SET
            id_mapel = '$id_mapel',
            judul = '$judul',
            deskripsi = '$deskripsi',
            deadline = '$deadline'
        WHERE id_tugas = '$id'
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil diupdate"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengupdate tugas: " . mysqli_error($conn)
        ]);
    }
}

/* =========================
   DELETE
========================= */
elseif ($method == 'DELETE') {

    parse_str(file_get_contents("php://input"), $deleteData);

    $id = $deleteData['id_tugas'] ?? '';

    if ($id == '') {
        echo json_encode([
            "status" => "error",
            "message" => "ID tugas tidak ditemukan"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas='$id'");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil dihapus"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus tugas: " . mysqli_error($conn)
        ]);
    }
}

else {
    echo json_encode([
        "status" => "error",
        "message" => "Method tidak didukung"
    ]);
}
?>