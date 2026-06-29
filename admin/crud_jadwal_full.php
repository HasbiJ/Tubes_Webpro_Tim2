<?php
session_start();
include "../config/koneksi.php";

$id_user_login = $_SESSION['id_user'];
$query_admin = mysqli_query($conn, "SELECT * FROM admin WHERE id_user = '$id_user_login'");
$data_admin = mysqli_fetch_assoc($query_admin);

$nama_admin = isset($data_admin['nama']) ? $data_admin['nama'] : "Admin";
$foto_admin = isset($data_admin['foto']) ? $data_admin['foto'] : "default.jpg";


// 1. PROSES HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM jadwal WHERE id_jadwal = '$id'";
    $hasil = mysqli_query($conn, $query);

    if ($hasil) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href='crud_jadwal_full.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}

// Contoh perbaikan untuk proses tambah di crud_jadwal_full.php
if (isset($_POST['submit'])) {
    $id_kelas = $_POST['id_kelas'];
    $id_mapel = $_POST['id_mapel'];
    $hari     = $_POST['hari'];
    $jam_m    = $_POST['jam_mulai'];
    $jam_s    = $_POST['jam_selesai'];
    $ruang    = $_POST['ruang'];

    $query = "INSERT INTO jadwal (id_kelas, id_mapel, hari, jam_mulai, jam_selesai, ruang) 
              VALUES ('$id_kelas', '$id_mapel', '$hari', '$jam_m', '$jam_s', '$ruang')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Jadwal berhasil disimpan!'); window.location='crud_jadwal_full.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_jadwal']);
    $k = mysqli_real_escape_string($conn, $_POST['id_kelas']);
    $m = mysqli_real_escape_string($conn, $_POST['id_mapel']);
    $h = mysqli_real_escape_string($conn, $_POST['hari']);
    $jm = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
    $js = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
    $r = mysqli_real_escape_string($conn, $_POST['ruang']);

    $q = "UPDATE jadwal SET id_kelas='$k', id_mapel='$m', hari='$h', jam_mulai='$jm', jam_selesai='$js', ruang='$r' WHERE id_jadwal='$id'";
    if (mysqli_query($conn, $q)) {
        echo "<script>
                alert('Data berhasil diupdate!');
                window.location.href = 'crud_jadwal_full.php';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>EduFlex - Manajemen Jadwal</title>
    <link rel="stylesheet" href="../frontend_js/dashboard_admin.css">
    <link rel="stylesheet" href="../frontend_js/buat_jadwal.css">
    <style>
        .table-jadwal {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        .table-jadwal th {
            background: #0b1a3a;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .table-jadwal td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .btn-act {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-edit {
            background: #e8f4fd;
            color: #2196f3;
            border: 1px solid #2196f3;
        }

        .btn-hapus {
            background: #fde8e8;
            color: #f44336;
            border: 1px solid #f44336;
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">
            <img src="../logo project website uid.png" alt="logo" class="logo">
            <div>
                <div>EduFlex</div>
                <div class="sub">Education Flexible</div>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="../admin/dashboard_admin.php">Beranda</a></li>
                <li><a href="../admin/crud_jadwal_full.php" class="active">Buat Jadwal</a></li>
                <li><a href="../admin/crud_user_full.php">Buat User</a></li>
            </ul>
        </nav>
        <div class="right-section">
            <div class="language">
                <img src="language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
                <select class="select">
                    <option>ID</option>
                    <option>ENG</option>
                </select>
            </div>
            <div class="account">
                <img src="<?= htmlspecialchars($foto_admin); ?>" class="avatar" alt="avatar">
                <div><?= htmlspecialchars($nama_admin); ?></div>
                <a href="logout.php"
                    style="margin-left: 15px; color: #ff6b00; text-decoration: none; font-weight: bold;">
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
        </aside>

        <main class="main">
            <div class="form-container">
                <h2>Daftar Jadwal Pelajaran</h2>
                <table class="table-jadwal" border="1">
                    <tr>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Ruang</th>
                        <th>Aksi</th>
                    </tr>
                    <?php
                    $res = mysqli_query($conn, "SELECT j.*, k.nama_kelas, m.nama_mapel FROM jadwal j JOIN kelas k ON j.id_kelas = k.id_kelas JOIN mata_pelajaran m ON j.id_mapel = m.id_mapel");
                    while ($j = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td><?= $j['nama_kelas']; ?></td>
                            <td><?= $j['nama_mapel']; ?></td>
                            <td><?= $j['hari']; ?>, <?= $j['jam_mulai']; ?> - <?= $j['jam_selesai']; ?></td>
                            <td><?= $j['ruang']; ?></td>
                            <td>
                                <a href="../edit_jadwal.php?id=<?= $j['id_jadwal']; ?>" class="btn-act btn-edit">
                                    Edit
                                </a>
                                <a href="crud_jadwal_full.php?hapus=<?= $j['id_jadwal']; ?>" class="btn-act btn-hapus"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <div class="form-container">
                <?php
                // Logika untuk menentukan apakah ini mode Tambah atau Edit
                $isEdit = isset($_GET['edit']);
                $data = null;
                if ($isEdit) {
                    $id = $_GET['edit'];
                    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id_jadwal='$id'"));
                }
                ?>

                <h2><?= $isEdit ? "Edit Jadwal" : "Tambah Jadwal Baru" ?></h2>
                <form method="POST" id="formJadwal">
                    <?php if ($isEdit)
                        echo '<input type="hidden" name="id_jadwal" value="' . $data['id_jadwal'] . '">'; ?>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php
                            $q_k = mysqli_query($conn, "SELECT * FROM kelas");
                            while ($k = mysqli_fetch_assoc($q_k)) {
                                echo "<option value='{$k['id_kelas']}'>{$k['nama_kelas']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="id_mapel" required>
                            <option value="">-- Pilih Mapel --</option>
                            <?php
                            $q_m = mysqli_query($conn, "SELECT * FROM mata_pelajaran");
                            while ($m = mysqli_fetch_assoc($q_m)) {
                                echo "<option value='{$m['id_mapel']}'>{$m['nama_mapel']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Hari</label>
                        <select name="hari" required>
                            <option value="">-- Pilih Hari --</option>
                            <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h) {
                                echo "<option value='$h'>$h</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jam Mulai & Selesai</label>
                        <div style="display:flex; gap:10px;">
                            <input type="time" name="jam_mulai" value="" required>
                            <input type="time" name="jam_selesai" value="" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ruangan</label>
                        <input type="text" name="ruang" placeholder="Contoh: Lab 1" value="" required>
                    </div>

                    <button type="submit" name="<?= $isEdit ? 'update' : 'submit' ?>" class="btn-submit">
                        <?= $isEdit ? 'Update Jadwal' : 'Simpan Jadwal' ?>
                </form>
            </div>
        </main>
    </div>

    <footer>
        <div class="leftfooter">
            <div class="textfooter">
                <div>Navigasi</div>
                <div>Sumber Daya</div>
                <div>Hubungi Kami</div>
            </div>
        </div>
        <div class="rightfooter">
            <div class="logososmed">
                <img src="../instagram.png" alt="ig">
                <img src="../facebook (1).png" alt="fb">
                <img src="../youtube.png" alt="yt">
                <img src="../twitter.png" alt="x">
                <img src="../linkedin.png" alt="linkedin">
            </div>
        </div>
    </footer>
    <script src="../js/jadwal_api.js"></script>
</body>

</html>