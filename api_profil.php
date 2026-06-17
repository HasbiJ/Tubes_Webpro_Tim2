<?php
header("Content-Type: application/json");
session_start();
include "config/koneksi.php";

// Validasi Otentikasi
if(!isset($_SESSION['id_user'])){
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali']);
    exit;
}

$id_user = $_SESSION['id_user'];
$action = $_GET['action'] ?? '';

// ==========================================
// ENDPOINT: UPDATE BIODATA & FOTO (UPDATE CRUD)
// ==========================================
if($action == 'update_profile' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $nisn  = mysqli_real_escape_string($conn, $_POST['nisn']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat= mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // Ambil info foto lama
    $query_lama = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data_lama  = mysqli_fetch_assoc($query_lama);
    $nama_file_baru = $data_lama['foto']; // Default pakai foto lama jika tidak ganti
    
    // Jika ada file foto baru yang diunggah
    if(isset($_FILES['foto_baru']['name']) && $_FILES['foto_baru']['name'] != "") {
        $nama_file = $_FILES['foto_baru']['name'];
        $tmp_file  = $_FILES['foto_baru']['tmp_name'];
        $ekstensi  = pathinfo($nama_file, PATHINFO_EXTENSION);
        
        // Buat nama file unik berdasarkan id_user
        $nama_file_baru = "Avatar_" . $id_user . "_" . time() . "." . $ekstensi;
        $folder_tujuan  = "uploads/" . $nama_file_baru;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        if(move_uploaded_file($tmp_file, $folder_tujuan)) {
            // Hapus foto fisik lama dari server jika bukan foto default
            if($data_lama['foto'] != "" && file_exists("uploads/" . $data_lama['foto'])) {
                unlink("uploads/" . $data_lama['foto']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah foto baru ke server']);
            exit;
        }
    }
    
    // Jalankan Query UPDATE data siswa ke database
    $update_query = "UPDATE siswa SET 
                 nama = '$nama', 
                 nisn = '$nisn', 
                 email = '$email', 
                 no_hp = '$no_hp', 
                 alamat = '$alamat', 
                 foto = '$nama_file_baru' 
                 WHERE id_user = '$id_user'";
                     
    if(mysqli_query($conn, $update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Profil Anda berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data di database']);
    }
    exit;
}

// ==========================================
// ENDPOINT: HAPUS FOTO PROFIL (DELETE CRUD VIA POST)
// ==========================================
if($action == 'delete_foto' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil nama file foto yang sekarang tersimpan
    $query_foto = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data_foto  = mysqli_fetch_assoc($query_foto);
    
    if($data_foto && $data_foto['foto'] != "") {
        $file_fisik = "uploads/" . $data_foto['foto'];
        if(file_exists($file_fisik)) {
            unlink($file_fisik); // Hapus file gambar asli dari folder uploads server
        }
        
        // Kosongkan nama file foto di database (Set string kosong "")
        $query_delete = mysqli_query($conn, "UPDATE siswa SET foto='' WHERE id_user='$id_user'");
        
        if($query_delete) {
            echo json_encode(['status' => 'success', 'message' => 'Foto profil berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data di database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Anda belum menetapkan foto profil apa pun']);
    }
    exit;
}
?>