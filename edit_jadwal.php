<?php
session_start();
include "config/koneksi.php";

$nama_admin = isset($_SESSION['nama_admin']) ? $_SESSION['nama_admin'] : 'Admin';
$foto_admin = isset($_SESSION['foto_admin']) ? $_SESSION['foto_admin'] : 'default.jpg';

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
    <link rel="stylesheet" href="../Tubes_Webpro_Tim2/frontend_js/dashboard_admin.css">
    <link rel="stylesheet" href="edit_jadwal.css">
</head>
<body>
    <header>
    <div class="brand">
      <img src="../Tubes_Webpro_Tim2/logo project website uid.png" alt="logo" class="logo">
      <div>
        <div>EduFlex</div>
        <div class="sub">Education Flexible</div>
      </div>
    </div>
    <nav>
      <ul>
        <li><a href="../Tubes_Webpro_Tim2/admin/dashboard_admin.php">Beranda</a></li>
        <li><a href="../Tubes_Webpro_Tim2/admin/crud_jadwal_full.php" class="active">Buat Jadwal</a></li>
        <li><a href="../Tubes_Webpro_Tim2/admin/crud_user_full.php">Buat User</a></li>
      </ul>
    </nav>
    <div class="right-section">
      <div class="language">
        <img src="../Tubes_Webpro_Tim2/language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
        <img src="../Tubes_Webpro_Tim2/avatars/<?= htmlspecialchars($foto_admin); ?>" class="avatar" alt="avatar">
        <div><?= htmlspecialchars($nama_admin); ?></div>
        <a href="../Tubes_Webpro_Tim2/logout.php" style="margin-left: 15px; color: #ff6b00; text-decoration: none; font-weight: bold;">
          Keluar
        </a>
      </div>
    </div>
  </header>

  <div class="container">

    <aside class="sidebar">
      <div class="profile">
        <img src="<?= htmlspecialchars($foto_admin); ?>" alt="foto admin">
        <h3><?= htmlspecialchars($nama_admin); ?></h3>
        <p class="role-badge">Administrator</p>
      </div>
    </aside>
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