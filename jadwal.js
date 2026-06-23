/**
 * jadwal.js - Mengelola operasi CRUD Jadwal melalui REST API
 */

const API_JADWAL = 'api_buat_jadwal.php';
const HEADERS = { 
    'X-API-KEY': 'eduflex2026_secret', 
    'Content-Type': 'application/json' 
};

// 1. TAMBAH JADWAL (POST)
const formTambah = document.getElementById('formTambahJadwal');
if (formTambah) {
    formTambah.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log("Tombol ditekan, data sedang dikirim...");

        const payload = {
            id_kelas: document.getElementById('in_kelas').value,
            id_mapel: document.getElementById('in_mapel').value,
            hari: document.getElementById('in_hari').value,
            jam_mulai: document.getElementById('in_jam_mulai').value,
            jam_selesai: document.getElementById('in_jam_selesai').value,
            ruang: document.getElementById('in_ruang').value
        };

        fetch(API_JADWAL, {
            method: 'POST',
            headers: HEADERS,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') location.reload();
        })
        .catch(err => console.error('Error:', err));
    });
}

// 2. EDIT JADWAL (PUT)
const formEdit = document.getElementById('formEditJadwal');
if (formEdit) {
    formEdit.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const payload = {
            id_jadwal: document.getElementById('in_id_jadwal').value,
            id_kelas: document.getElementById('in_kelas').value,
            id_mapel: document.getElementById('in_mapel').value,
            hari: document.getElementById('in_hari').value,
            jam_mulai: document.getElementById('in_jam_mulai').value,
            jam_selesai: document.getElementById('in_jam_selesai').value,
            ruang: document.getElementById('in_ruang').value
        };

        fetch(API_JADWAL, {
            method: 'PUT',
            headers: HEADERS,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') window.location.href = 'buat_jadwal.php';
        })
        .catch(err => console.error('Error:', err));
    });
}

// 3. HAPUS JADWAL (DELETE)
function hapusJadwal(id) {
    if (confirm('Yakin ingin menghapus jadwal ini?')) {
        fetch(API_JADWAL, {
            method: 'DELETE',
            headers: HEADERS,
            body: JSON.stringify({ id_jadwal: id })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') location.reload();
        })
        .catch(err => console.error('Error:', err));
    }
}