<?php
header("Content-Type: application/json");
session_start();
include "config/koneksi.php";

// Validasi Otentikasi & Session
if(!isset($_SESSION['id_user'])){
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($conn, "SELECT id_siswa FROM siswa WHERE id_user='$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$id_siswa = $data_user['id_siswa'];

$action = $_GET['action'] ?? '';

// ==========================================
// 1. ENDPOINT: CREATE / UPDATE CRUD (POST)
// ==========================================
if($action == 'upload' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tugas = $_POST['id_tugas'];
    $nama_file = $_FILES['file_resmi']['name'];
    $tmp_file = $_FILES['file_resmi']['tmp_name'];
    
    if($nama_file == "") {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada file yang dipilih']);
        exit;
    }
    
    $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
    $nama_file_baru = "Tugas_" . $id_tugas . "_" . $id_siswa . "." . $ekstensi;
    $folder_tujuan = "uploads_tugas/" . $nama_file_baru;
    
    if (!is_dir('uploads_tugas')) {
        mkdir('uploads_tugas', 0777, true);
    }
    
    if(move_uploaded_file($tmp_file, $folder_tujuan)){
        // Cek apakah data sudah ada sebelumnya di database
        $cek = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
        
        if(mysqli_num_rows($cek) > 0){
            // Jika sudah ada = UPDATE CRUD
            $query = mysqli_query($conn, "UPDATE pengumpulan_tugas SET file_tugas='$nama_file_baru', tgl_kumpul=NOW() WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
            $msg = "Berkas tugas berhasil diperbarui!";
        } else {
            // Jika belum ada = CREATE CRUD
            $query = mysqli_query($conn, "INSERT INTO pengumpulan_tugas (id_tugas, id_siswa, file_tugas, tgl_kumpul) VALUES ('$id_tugas', '$id_siswa', '$nama_file_baru', NOW())");
            $msg = "Tugas berhasil dikirim!";
        }
        
        if($query) {
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah berkas ke server']);
    }
    exit;
}

// ==========================================
// 2. ENDPOINT: DELETE CRUD (POST)
// ==========================================
if($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tugas = $_POST['id_tugas'];
    
    $cek = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
    $data_kumpul = mysqli_fetch_assoc($cek);
    
    if($data_kumpul){
        $file_lama = "uploads_tugas/" . $data_kumpul['file_tugas'];
        if(file_exists($file_lama)){
            unlink($file_lama); // Hapus file fisik dari folder storage
        }
        
        // Hapus record di database = DELETE CRUD
        $query = mysqli_query($conn, "DELETE FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
        
        if($query) {
            echo json_encode(['status' => 'success', 'message' => 'Pengumpulan tugas berhasil dibatalkan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data dari database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data pengumpulan tidak ditemukan']);
    }
    exit;
}