<?php
session_start();
include "config/koneksi.php";

// Proteksi akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil ID dari URL
$id = $_GET['id'];

// Proses Update
if (isset($_POST['update'])) {
    $id_kelas = $_POST['id_kelas'];
    $id_mapel = $_POST['id_mapel'];
    $hari     = $_POST['hari'];
    $jam_m    = $_POST['jam_mulai'];
    $jam_s    = $_POST['jam_selesai'];
    $ruang    = mysqli_real_escape_string($conn, $_POST['ruang']);

    $update = mysqli_query($conn, "UPDATE jadwal SET id_kelas='$id_kelas', id_mapel='$id_mapel', 
                             hari='$hari', jam_mulai='$jam_m', jam_selesai='$jam_s', ruang='$ruang' 
                             WHERE id_jadwal='$id'");
    
    if ($update) {
        echo "<script>alert('Jadwal berhasil diupdate!'); window.location.href='../Tubes_Webpro_Tim2/admin/crud_jadwal_full.php';</script>";
    }
}

// Ambil data lama untuk ditampilkan di form
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id_jadwal='$id'"));
?>

<!DOCTYPE html>
<html lang="id">
<body>
    <head>
    <meta charset="UTF-8">
    <title>Manajemen User - EduFlex</title>
    <link rel="stylesheet" href="../frontend_js/dashboard_admin.css">
    <link rel="stylesheet" href="edit_jadwal.css">
</head>
<body>
    
  <main class="main">
    <div class="form-container">
        <h2>Edit Jadwal Pelajaran</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label>Kelas</label>
                <select name="id_kelas" required>
                    <?php
                    $q_k = mysqli_query($conn, "SELECT * FROM kelas");
                    while($k = mysqli_fetch_assoc($q_k)) {
                        $sel = ($k['id_kelas'] == $data['id_kelas']) ? "selected" : "";
                        echo "<option value='".$k['id_kelas']."' $sel>".$k['nama_kelas']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="id_mapel" required>
                    <?php
                    $q_m = mysqli_query($conn, "SELECT * FROM mata_pelajaran");
                    while($m = mysqli_fetch_assoc($q_m)) {
                        $sel = ($m['id_mapel'] == $data['id_mapel']) ? "selected" : "";
                        echo "<option value='".$m['id_mapel']."' $sel>".$m['nama_mapel']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hari</label>
                <select name="hari" required>
                    <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h) {
                        $sel = ($data['hari'] == $h) ? "selected" : "";
                        echo "<option value='$h' $sel>$h</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Waktu</label>
                <div style="display:flex; gap:10px;">
                    <input type="time" name="jam_mulai" value="<?= $data['jam_mulai'] ?>" required>
                    <input type="time" name="jam_selesai" value="<?= $data['jam_selesai'] ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Ruangan</label>
                <input type="text" name="ruang" value="<?= $data['ruang'] ?>" required>
            </div>
            <button type="submit" name="update" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
  </main>
</body>
</html>