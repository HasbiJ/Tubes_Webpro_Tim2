const API_URL = '../admin/admin_jadwal_api.php';
const KEY = 'EduFlexKey2026'; // Harus sama dengan di file PHP

document.getElementById("formJadwal").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const data = {
        id_jadwal: document.querySelector('input[name="id_jadwal"]')?.value || '',
        id_kelas: document.querySelector('select[name="id_kelas"]').value,
        id_mapel: document.querySelector('select[name="id_mapel"]').value,
        hari: document.querySelector('select[name="hari"]').value,
        jam_mulai: document.querySelector('input[name="jam_mulai"]').value,
        jam_selesai: document.querySelector('input[name="jam_selesai"]').value,
        ruang: document.querySelector('input[name="ruang"]').value
    };

    fetch(API_URL + '?method=POST', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'API-KEY': KEY 
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if(d.status === "sukses") { 
            alert("Berhasil!"); 
            window.location.href="../admin/crud_jadwal_full.php"; 
        } else {
            alert("Gagal: " + d.message);
        }
    });
});

function deleteUser(id) {
    if(confirm('Yakin?')) {
        fetch(API_URL, {
            method: 'DELETE',
            headers: { 
                'API-KEY': API_KEY, // <--- INI HARUS SAMA DENGAN PHP
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({ id_user: id })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'sukses') {
                loadUsers();
            } else {
                alert("Gagal: " + JSON.stringify(data));
            }
        });
    }
}