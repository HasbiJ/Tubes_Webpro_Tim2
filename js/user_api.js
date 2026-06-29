const API_URL = '../admin/admin_user_api.php';
const API_KEY = 'EduFlexKey2026';

function loadUsers() {
    fetch(API_URL, {
        method: 'GET',
        headers: { 'API-KEY': API_KEY }
    })
    .then(res => res.json())
    .then(data => {
        let html = '';
        data.forEach(u => {
            html += `<tr>
                <td>${u.username}</td>
                <td>${u.role}</td>
                <td><button onclick="deleteUser(${u.id_user})">Hapus</button></td>
            </tr>`;
        });
        document.querySelector('#userTableBody').innerHTML = html;
    });
}

function deleteUser(id) {
    if(confirm('Yakin ingin menghapus?')) {
        fetch(API_URL, {
            method: 'DELETE',
            headers: { 
                'API-KEY': API_KEY,
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({ id_user: id })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'sukses') loadUsers();
            else alert("Gagal menghapus");
        });
    }
}
// Fungsi untuk memicu update (panggil saat tombol edit diklik)
function updateUser(id, username, role) {
    fetch(API_URL, {
        method: 'PUT',
        headers: { 
            'API-KEY': API_KEY,
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ 
            id_user: id, 
            username: username, 
            role: role 
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'sukses') {
            alert('Update Berhasil!');
            loadUsers(); // Refresh tabel
        } else {
            alert('Update Gagal!');
        }
    });
}
loadUsers();