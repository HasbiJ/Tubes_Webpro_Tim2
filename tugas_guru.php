<?php

$conn = mysqli_connect("localhost","root","","eduflex");

if(!$conn){
    die("Koneksi Gagal : ".mysqli_connect_error());
}

/* CREATE */
if(isset($_POST['tambah'])){

    $id_mapel = $_POST['id_mapel'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $deadline = $_POST['deadline'];

    mysqli_query($conn,"
        INSERT INTO tugas
        (id_mapel, judul, deskripsi, deadline)
        VALUES
        ('$id_mapel','$judul','$deskripsi','$deadline')
    ");

    header("Location: tugas_guru.php");
    exit;
}

/* DELETE */
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query($conn,"
        DELETE FROM tugas
        WHERE id_tugas='$id'
    ");

    header("Location: tugas_guru.php");
    exit;
}

/* UPDATE */
if(isset($_POST['update'])){

    $id = $_POST['id_tugas'];

    $id_mapel = $_POST['id_mapel'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $deadline = $_POST['deadline'];

    mysqli_query($conn,"
        UPDATE tugas SET
        id_mapel='$id_mapel',
        judul='$judul',
        deskripsi='$deskripsi',
        deadline='$deadline'
        WHERE id_tugas='$id'
    ");

    header("Location: tugas_guru.php");
    exit;
}

/* AMBIL DATA EDIT */
$dataEdit = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $query = mysqli_query($conn,"
        SELECT * FROM tugas
        WHERE id_tugas='$id'
    ");

    $dataEdit = mysqli_fetch_assoc($query);
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD Tugas Guru</title>

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
textarea{
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

</style>
</head>

<body>

<div class="container">

<h2>Segmentasi Guru - Kelola Tugas</h2>

<form method="POST">

<?php if($dataEdit){ ?>
    <input
        type="hidden"
        name="id_tugas"
        value="<?= $dataEdit['id_tugas']; ?>">
<?php } ?>

<label>ID Mapel</label>
<input
    type="number"
    name="id_mapel"
    required
    value="<?= $dataEdit['id_mapel'] ?? ''; ?>">

<label>Judul Tugas</label>
<input
    type="text"
    name="judul"
    required
    value="<?= $dataEdit['judul'] ?? ''; ?>">

<label>Deskripsi</label>
<textarea
    name="deskripsi"
    rows="4"
    required><?= $dataEdit['deskripsi'] ?? ''; ?></textarea>

<label>Deadline</label>
<input
    type="date"
    name="deadline"
    required
    value="<?= $dataEdit['deadline'] ?? ''; ?>">

<?php if($dataEdit){ ?>

    <button type="submit" name="update">
        Update Tugas
    </button>

<?php } else { ?>

    <button type="submit" name="tambah">
        Tambah Tugas
    </button>

<?php } ?>

</form>

<table>

<tr>
    <th>ID Tugas</th>
    <th>ID Mapel</th>
    <th>Judul</th>
    <th>Deskripsi</th>
    <th>Deadline</th>
    <th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
    SELECT * FROM tugas
    ORDER BY id_tugas DESC
");

while($row = mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $row['id_tugas']; ?></td>
<td><?= $row['id_mapel']; ?></td>
<td><?= $row['judul']; ?></td>
<td><?= $row['deskripsi']; ?></td>
<td><?= $row['deadline']; ?></td>

<td>

<a
class="edit"
href="tugas_guru.php?edit=<?= $row['id_tugas']; ?>">
Edit
</a>

<a
class="hapus"
href="tugas_guru.php?hapus=<?= $row['id_tugas']; ?>"
onclick="return confirm('Yakin ingin menghapus tugas?')">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>