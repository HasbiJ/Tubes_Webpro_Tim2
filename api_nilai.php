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

/* Handle preflight */
if ($method == 'OPTIONS') {
    exit(0);
}

/* =========================
   GET -> Ambil data dropdown & data nilai
========================= */
if ($method == 'GET') {

    // endpoint dropdown siswa
    if (isset($_GET['action']) && $_GET['action'] == 'siswa') {
        $result = mysqli_query($conn, "
            SELECT id_siswa, nama
            FROM siswa
            ORDER BY nama ASC
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

    // endpoint dropdown mapel
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

    // ambil 1 data nilai
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = mysqli_query($conn, "
            SELECT 
                nilai.id_nilai,
                nilai.id_siswa,
                nilai.id_mapel,
                nilai.nilai,
                siswa.nama AS nama_siswa,
                mata_pelajaran.nama_mapel
            FROM nilai
            JOIN siswa ON nilai.id_siswa = siswa.id_siswa
            JOIN mata_pelajaran ON nilai.id_mapel = mata_pelajaran.id_mapel
            WHERE nilai.id_nilai = '$id'
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
                "message" => "Data tidak ditemukan"
            ]);
        }
        exit;
    }

    // ambil semua data nilai
    $result = mysqli_query($conn, "
        SELECT 
            nilai.id_nilai,
            nilai.id_siswa,
            nilai.id_mapel,
            nilai.nilai,
            siswa.nama AS nama_siswa,
            mata_pelajaran.nama_mapel
        FROM nilai
        JOIN siswa ON nilai.id_siswa = siswa.id_siswa
        JOIN mata_pelajaran ON nilai.id_mapel = mata_pelajaran.id_mapel
        ORDER BY nilai.id_nilai DESC
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
   POST -> Tambah data nilai
========================= */
elseif ($method == 'POST') {

    $id_siswa = $_POST['id_siswa'] ?? '';
    $id_mapel = $_POST['id_mapel'] ?? '';
    $nilai    = $_POST['nilai'] ?? '';

    if ($id_siswa == '' || $id_mapel == '' || $nilai == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Semua field wajib diisi"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        INSERT INTO nilai (id_siswa, id_mapel, nilai)
        VALUES ('$id_siswa', '$id_mapel', '$nilai')
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil ditambahkan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menambahkan data: " . mysqli_error($conn)
        ]);
    }
}

/* =========================
   PUT -> Update data nilai
========================= */
elseif ($method == 'PUT') {

    parse_str(file_get_contents("php://input"), $putData);

    $id       = $putData['id_nilai'] ?? '';
    $id_siswa = $putData['id_siswa'] ?? '';
    $id_mapel = $putData['id_mapel'] ?? '';
    $nilai    = $putData['nilai'] ?? '';

    if ($id == '' || $id_siswa == '' || $id_mapel == '' || $nilai == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Data update tidak lengkap"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        UPDATE nilai SET
            id_siswa = '$id_siswa',
            id_mapel = '$id_mapel',
            nilai = '$nilai'
        WHERE id_nilai = '$id'
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil diupdate"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengupdate data: " . mysqli_error($conn)
        ]);
    }
}

/* =========================
   DELETE -> Hapus data nilai
========================= */
elseif ($method == 'DELETE') {

    parse_str(file_get_contents("php://input"), $deleteData);

    $id = $deleteData['id_nilai'] ?? '';

    if ($id == '') {
        echo json_encode([
            "status" => "error",
            "message" => "ID tidak ditemukan"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "DELETE FROM nilai WHERE id_nilai='$id'");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil dihapus"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus data: " . mysqli_error($conn)
        ]);
    }
}

/* =========================
   Method tidak dikenali
========================= */
else {
    echo json_encode([
        "status" => "error",
        "message" => "Method tidak didukung"
    ]);
}
?>