@extends('layouts.admin')

@section('title', 'Tambah User')

@section('page-title', 'Tambah User')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="userForm">
                    <div class="mb-3">
                        <label for="roles" class="form-label fw-bold">Roles</label>
                        <select class="form-control" id="roles" name="roles" required>
                            <option value="" disabled selected>Pilih Tipe Admin</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="editor">Editor</option>
                            <option value="admin_chat">Admin Chat</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-bold">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Isikan Nama Admin" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" required>
                        <div class="valid-feedback">
                            Email valid
                        </div>
                        <div class="invalid-feedback">
                            Email tidak valid
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Kata Sandi</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Kata Sandi</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required>
                                <div class="invalid-feedback">
                                    Kata sandi tidak cocok
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="no_telpon" class="form-label fw-bold">No Telpon</label>
                        <input type="text" class="form-control" id="no_telpon" name="no_telpon" placeholder="08xxxxxxxxxx" required>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <a href="/user-management" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Email validation
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailRegex.test(email)) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                this.nextElementSibling.style.display = 'block';
                this.nextElementSibling.nextElementSibling.style.display = 'none';
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                this.nextElementSibling.style.display = 'none';
                this.nextElementSibling.nextElementSibling.style.display = 'block';
            }
        });
        
        // Password confirmation validation
        document.getElementById('password_confirmation').addEventListener('keyup', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password === confirmPassword) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                this.nextElementSibling.style.display = 'none';
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                this.nextElementSibling.style.display = 'block';
            }
        });
        
        // Form submission
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const role = document.getElementById('roles').value;
            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            const no_telpon = document.getElementById('no_telpon').value;
            
            if (password !== password_confirmation) {
                document.getElementById('password_confirmation').classList.add('is-invalid');
                document.getElementById('password_confirmation').nextElementSibling.style.display = 'block';
                return;
            }
            
            // Prepare data based on role
            let endpoint = '';
            let data = {
                email: email,
                no_telpon: no_telpon
            };
            
            if (role === 'editor') {
                endpoint = '/editor/create';
                data.nama_editor = nama;
            } else {
                // Include password for all roles
                data.password = password;
                
                if (role === 'admin' || role === 'admin_chat') {
                    endpoint = '/admin/create';
                    data.nama_admin = nama;
                    data.role = role;
                } else {
                    endpoint = '/user/create';
                    data.nama = nama;
                }
            }
            
            // Show loading indicator
            const saveButton = document.querySelector('button[type="submit"]');
            const originalText = saveButton.innerHTML;
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            // Send request
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    window.location.href = '/user-management';
                } else {
                    alert(result.message || 'Terjadi kesalahan saat menyimpan data');
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
                saveButton.disabled = false;
                saveButton.innerHTML = originalText;
            });
        });
    });
</script>
@endsection 