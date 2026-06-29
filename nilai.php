<?php

$conn = mysqli_connect("localhost","root","","eduflex");

if(!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());
}

/* =========================
   CREATE
========================= */
if(isset($_POST['tambah'])){

    $id_siswa = $_POST['id_siswa'];
    $id_mapel = $_POST['id_mapel'];
    $nilai    = $_POST['nilai'];

    if($id_siswa == "" || $id_mapel == "" || $nilai == ""){
        echo "<script>alert('Semua field wajib diisi!');</script>";
    } else {

        $query = mysqli_query($conn,"
            INSERT INTO nilai (id_siswa, id_mapel, nilai)
            VALUES ('$id_siswa','$id_mapel','$nilai')
        ");

        if($query){
            header("Location: nilai.php");
            exit;
        } else {
            echo "<script>alert('Gagal menambah data: " . mysqli_error($conn) . "');</script>";
        }
    }
}

/* =========================
   DELETE
========================= */
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $query = mysqli_query($conn,"
        DELETE FROM nilai
        WHERE id_nilai='$id'
    ");

    if($query){
        header("Location: nilai.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "');</script>";
    }
}

/* =========================
   UPDATE
========================= */
if(isset($_POST['update'])){

    $id       = $_POST['id_nilai'];
    $id_siswa = $_POST['id_siswa'];
    $id_mapel = $_POST['id_mapel'];
    $nilai    = $_POST['nilai'];

    if($id_siswa == "" || $id_mapel == "" || $nilai == ""){
        echo "<script>alert('Semua field wajib diisi!');</script>";
    } else {

        $query = mysqli_query($conn,"
            UPDATE nilai SET
                id_siswa='$id_siswa',
                id_mapel='$id_mapel',
                nilai='$nilai'
            WHERE id_nilai='$id'
        ");

        if($query){
            header("Location: nilai.php");
            exit;
        } else {
            echo "<script>alert('Gagal update data: " . mysqli_error($conn) . "');</script>";
        }
    }
}

/* =========================
   AMBIL DATA EDIT
========================= */
$dataEdit = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $query = mysqli_query($conn,"
        SELECT * FROM nilai
        WHERE id_nilai='$id'
    ");

    $dataEdit = mysqli_fetch_assoc($query);
}

/* =========================
   AMBIL DATA SISWA UNTUK DROPDOWN
========================= */
$dataSiswa = mysqli_query($conn,"
    SELECT id_siswa, nama
    FROM siswa
    ORDER BY nama ASC
");

/* =========================
   AMBIL DATA MAPEL UNTUK DROPDOWN
========================= */
$dataMapel = mysqli_query($conn,"
    SELECT id_mapel, nama_mapel
    FROM mata_pelajaran
    ORDER BY nama_mapel ASC
");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD Nilai</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f4f4;
    padding:20px;
}

.container{
    width:90%;
    margin:auto;
}

h2{
    color:#ff6600;
}

form{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

input,
select{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    box-sizing:border-box;
}

button{
    background:#ff6600;
    color:white;
    border:none;
    padding:10px 15px;
    cursor:pointer;
    border-radius:5px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th{
    background:#ff6600;
    color:white;
}

th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

.edit{
    background:green;
    color:white;
    padding:5px 10px;
    text-decoration:none;
    border-radius:5px;
}

.hapus{
    background:red;
    color:white;
    padding:5px 10px;
    text-decoration:none;
    border-radius:5px;
}

.kosong{
    color:red;
    font-weight:bold;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="container">

<h2>Kelola Nilai Siswa</h2>

<?php
// cek apakah tabel siswa kosong
$cekSiswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$rowSiswa = mysqli_fetch_assoc($cekSiswa);

// cek apakah tabel mapel kosong
$cekMapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_pelajaran");
$rowMapel = mysqli_fetch_assoc($cekMapel);

if($rowSiswa['total'] == 0){
    echo "<p class='kosong'>Data siswa masih kosong. Isi tabel siswa dulu sebelum menambah nilai.</p>";
}

if($rowMapel['total'] == 0){
    echo "<p class='kosong'>Data mata pelajaran masih kosong. Isi tabel mata_pelajaran dulu sebelum menambah nilai.</p>";
}
?>

<form method="POST">

<?php if($dataEdit){ ?>
    <input type="hidden"
           name="id_nilai"
           value="<?= $dataEdit['id_nilai']; ?>">
<?php } ?>

<label>Siswa</label>
<select name="id_siswa" required>
    <option value="">-- Pilih Siswa --</option>
    <?php
    mysqli_data_seek($dataSiswa, 0);
    while($siswa = mysqli_fetch_assoc($dataSiswa)){
        $selected = ($dataEdit && $dataEdit['id_siswa'] == $siswa['id_siswa']) ? "selected" : "";
        echo "<option value='".$siswa['id_siswa']."' $selected>".$siswa['nama']." (ID: ".$siswa['id_siswa'].")</option>";
    }
    ?>
</select>

<label>Mata Pelajaran</label>
<select name="id_mapel" required>
    <option value="">-- Pilih Mata Pelajaran --</option>
    <?php
    mysqli_data_seek($dataMapel, 0);
    while($mapel = mysqli_fetch_assoc($dataMapel)){
        $selected = ($dataEdit && $dataEdit['id_mapel'] == $mapel['id_mapel']) ? "selected" : "";
        echo "<option value='".$mapel['id_mapel']."' $selected>".$mapel['nama_mapel']." (ID: ".$mapel['id_mapel'].")</option>";
    }
    ?>
</select>

<label>Nilai</label>
<input type="number"
       step="0.01"
       min="0"
       max="100"
       name="nilai"
       required
       value="<?= $dataEdit['nilai'] ?? ''; ?>">

<?php if($dataEdit){ ?>

<button type="submit" name="update">
    Update Nilai
</button>

<?php } else { ?>

<button type="submit" name="tambah">
    Tambah Nilai
</button>

<?php } ?>

</form>

<table>
<tr>
    <th>ID Nilai</th>
    <th>Nama Siswa</th>
    <th>Mata Pelajaran</th>
    <th>Nilai</th>
    <th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
    SELECT 
        nilai.id_nilai,
        nilai.nilai,
        siswa.nama AS nama_siswa,
        mata_pelajaran.nama_mapel
    FROM nilai
    JOIN siswa ON nilai.id_siswa = siswa.id_siswa
    JOIN mata_pelajaran ON nilai.id_mapel = mata_pelajaran.id_mapel
    ORDER BY nilai.id_nilai DESC
");

if(mysqli_num_rows($data) > 0){
    while($row = mysqli_fetch_assoc($data)){
?>

<tr>
    <td><?= $row['id_nilai']; ?></td>
    <td><?= $row['nama_siswa']; ?></td>
    <td><?= $row['nama_mapel']; ?></td>
    <td><?= $row['nilai']; ?></td>
    <td>
        <a class="edit"
           href="nilai.php?edit=<?= $row['id_nilai']; ?>">
           Edit
        </a>

        <a class="hapus"
           href="nilai.php?hapus=<?= $row['id_nilai']; ?>"
           onclick="return confirm('Yakin ingin menghapus data?')">
           Hapus
        </a>
    </td>
</tr>

<?php
    }
} else {
?>
<tr>
    <td colspan="5">Belum ada data nilai</td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>