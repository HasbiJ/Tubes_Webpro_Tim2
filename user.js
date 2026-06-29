/**
 * user.js - Mengelola komunikasi frontend ke backend via API
 * API Endpoint: api_buat_user.php
 */

const API_URL = 'api_buat_user.php';
const HEADERS = { 
    'X-API-KEY': 'eduflex2026_secret', 
    'Content-Type': 'application/json' 
};

// 1. TAMBAH USER (POST)
const formTambah = document.getElementById('formTambahUser');
if (formTambah) {
    formTambah.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const payload = {
            username: document.getElementById('in_username').value,
            password: document.getElementById('in_password').value,
            role: document.getElementById('in_role').value
        };

        fetch(API_URL, {
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

// 2. EDIT USER (PUT)
const formEdit = document.getElementById('formEditUser');
if (formEdit) {
    formEdit.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const payload = {
            id_user: document.getElementById('in_id_user').value,
            role: document.getElementById('in_role_edit').value
        };

        fetch(API_URL, {
            method: 'PUT',
            headers: HEADERS,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') window.location.href = 'buat_user.php';
        })
        .catch(err => console.error('Error:', err));
    });
}

// 3. HAPUS USER (DELETE)
function hapusUser(id) {
    if (confirm('Yakin ingin menghapus user ini?')) {
        fetch(API_URL, {
            method: 'DELETE',
            headers: HEADERS,
            body: JSON.stringify({ id_user: id })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') location.reload();
        })
        .catch(err => console.error('Error:', err));
    }
}