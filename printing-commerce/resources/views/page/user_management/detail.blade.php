@extends('layouts.admin')

@section('title', 'Detail User')

@section('page-title', 'Detail User')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="userForm">
                    <div class="mb-3">
                        <label for="roles" class="form-label fw-bold">Roles</label>
                        <select class="form-control" id="roles" name="roles" required>
                            <option value="" disabled>Pilih Tipe Admin</option>
                            <option value="admin" {{ $userData->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ $userData->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="editor" {{ $userData->role == 'editor' ? 'selected' : '' }}>Editor</option>
                            <option value="admin_chat" {{ $userData->role == 'admin_chat' ? 'selected' : '' }}>Admin Chat</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-bold">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $userData->nama }}" placeholder="Isikan Nama Admin" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="{{ $userData->email ?? '' }}" 
                            placeholder="example@gmail.com" 
                            {{ $userType == 'editor' ? 'disabled' : 'required' }}>
                        <div class="valid-feedback">
                            Email valid
                        </div>
                        <div class="invalid-feedback">
                            Email tidak valid
                        </div>
                        @if($userType == 'editor')
                        <small class="text-muted">Editor tidak memiliki email terkait di sistem</small>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Kata Sandi</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Kata Sandi">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah kata sandi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Kata Sandi</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Kata Sandi">
                                <div class="invalid-feedback">
                                    Kata sandi tidak cocok
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="no_telpon" class="form-label fw-bold">No Telpon</label>
                        <input type="text" class="form-control" id="no_telpon" name="no_telpon" value="{{ $userData->no_telpon }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-danger me-2" onclick="deleteUser('{{ $userData->uuid }}')">Hapus</button>
                        <a href="/user-management" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus user ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            
            if (password && password !== password_confirmation) {
                document.getElementById('password_confirmation').classList.add('is-invalid');
                document.getElementById('password_confirmation').nextElementSibling.style.display = 'block';
                return;
            }
            
            // Prepare data based on user type
            let endpoint = '';
            let data = {
                uuid: '{{ $userData->uuid }}',
                email: email,
                no_telpon: no_telpon
            };
            
            if (password) {
                data.password = password;
            }
            
            if ('{{ $userType }}' === 'admin') {
                endpoint = '/admin/update';
                data.nama_admin = nama;
                data.role = role;
            } else if ('{{ $userType }}' === 'editor') {
                endpoint = '/editor/update';
                data.nama_editor = nama;
            } else {
                endpoint = '/user/update';
                data.nama_user = nama;
            }
            
            // Send request
            console.log('Sending data:', data);
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', endpoint);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify(data));
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    console.log('Response status:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    if (xhr.status === 200) {
                        window.location.href = '/user-management';
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            alert(response.message || 'Terjadi kesalahan saat menyimpan data');
                        } catch (e) {
                            alert('Terjadi kesalahan saat menyimpan data');
                        }
                    }
                }
            }
        });
    });
    
    let userIdToDelete = null;
    
    function deleteUser(uuid) {
        userIdToDelete = uuid;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (userIdToDelete) {
            // Determine endpoint based on user type
            let deleteEndpoint = '';
            if ('{{ $userType }}' === 'admin') {
                deleteEndpoint = '/admin/delete';
            } else if ('{{ $userType }}' === 'editor') {
                deleteEndpoint = '/editor/delete';
            } else {
                deleteEndpoint = '/user/delete';
            }
            
            // Send delete request
            console.log('Deleting user from endpoint:', deleteEndpoint);
            var xhr = new XMLHttpRequest();
            xhr.open('DELETE', deleteEndpoint);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify({ uuid: userIdToDelete }));
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    console.log('Delete response status:', xhr.status);
                    console.log('Delete response text:', xhr.responseText);
                    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();
                    if (xhr.status === 200) {
                        // Redirect to user management page on successful deletion
                        window.location.href = '/user-management';
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            alert(response.message || 'Gagal menghapus user');
                        } catch (e) {
                            alert('Gagal menghapus user');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection 