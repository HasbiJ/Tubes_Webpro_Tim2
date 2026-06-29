<?php
session_start();
include "config/koneksi.php";

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ======================
// UPDATE DATA (POST)
// ======================
if (isset($_POST['update_profile'])) {

    $nama   = $_POST['nama'];
    $nisn   = $_POST['nisn'];
    $email  = $_POST['email'];
    $no_hp  = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    // Ambil foto lama
    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);
    $foto_lama = $data['foto'];

    $nama_foto = $foto_lama;

    // Upload foto baru
    if (!empty($_FILES['foto_baru']['name'])) {
        $nama_file = $_FILES['foto_baru']['name'];
        $tmp       = $_FILES['foto_baru']['tmp_name'];

        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_foto = "foto_" . time() . "." . $ext;

        if (!is_dir("uploads")) {
            mkdir("uploads");
        }

        move_uploaded_file($tmp, "uploads/" . $nama_foto);

        // hapus foto lama
        if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)) {
            unlink("uploads/" . $foto_lama);
        }
    }

    mysqli_query($conn, "
        UPDATE siswa SET
        nama='$nama',
        nisn='$nisn',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        foto='$nama_foto'
        WHERE id_user='$id_user'
    ");

    header("Location: profile.php?msg=update");
    exit;
}

// ======================
// DELETE FOTO (GET)
// ======================
if (isset($_GET['action']) && $_GET['action'] == 'hapus_foto') {

    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);

    if (!empty($data['foto'])) {
        unlink("uploads/" . $data['foto']);
        mysqli_query($conn, "UPDATE siswa SET foto='' WHERE id_user='$id_user'");
    }

    header("Location: profile.php?msg=hapus");
    exit;
}

// ======================
// READ DATA
// ======================
$q = mysqli_query($conn, "
    SELECT * FROM siswa WHERE id_user='$id_user'
");
$data = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>

<h2>Profile Saya (PHP Native)</h2>

<?php if (isset($_GET['msg'])): ?>
    <p style="color:green;">Berhasil!</p>
<?php endif; ?>

<!-- FOTO -->
<?php if (empty($data['foto'])): ?>
    <img src="default.png" width="120">
<?php else: ?>
    <img src="uploads/<?php echo $data['foto']; ?>" width="120"><br>
    <a href="profile.php?action=hapus_foto" onclick="return confirm('Hapus foto?')">Hapus Foto</a>
<?php endif; ?>

<hr>

<!-- FORM -->
<form method="POST" enctype="multipart/form-data">

    Nama:<br>
    <input type="text" name="nama" value="<?php echo $data['nama']; ?>"><br>

    NISN:<br>
    <input type="text" name="nisn" value="<?php echo $data['nisn']; ?>"><br>

    Email:<br>
    <input type="email" name="email" value="<?php echo $data['email']; ?>"><br>

    No HP:<br>
    <input type="text" name="no_hp" value="<?php echo $data['no_hp']; ?>"><br>

    Alamat:<br>
    <textarea name="alamat"><?php echo $data['alamat']; ?></textarea><br>

    Foto Baru:<br>
    <input type="file" name="foto_baru"><br><br>

    <button type="submit" name="update_profile">Simpan</button>

</form>

</body>
</html>