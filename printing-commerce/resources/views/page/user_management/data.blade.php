@extends('layouts.admin')

@section('title', 'Kelola User')

@section('page-title', 'Kelola User')

@section('styles')
<style>
    .user-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-start mb-3">
            <a href="/user-management/tambah" class="btn btn-success me-2">+ Tambah Admin</a>
            <a href="/user-management/tambah" class="btn btn-success me-2">+ Tambah User</a>
            <a href="/user-management/tambah" class="btn btn-success me-2">+ Tambah Editor</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Roles</th>
                        <th>Email</th>
                        <th>No Telepon</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userData as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}.</td>
                        <td>
                            @if($user->foto)
                                <img src="{{ asset('assets3/img/user/' . $user->foto) }}" class="user-photo" alt="{{ $user->nama }}">
                            @else
                                <img src="{{ asset('assets2/icon/user.svg') }}" class="user-photo" alt="{{ $user->nama }}">
                            @endif
                        </td>
                        <td>{{ $user->nama }}</td>
                        <td>{{ $user->roles }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->no_telpon ?? '-' }}</td>
                        <td>
                            <a href="/user-management/detail/{{ $user->uuid }}" class="btn btn-sm btn-success">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser('{{ $user->uuid }}')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    let userIdToDelete = null;
    
    function deleteUser(uuid) {
        userIdToDelete = uuid;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (userIdToDelete) {
            // Get user role from the row data
            const userRow = document.querySelector(`button[onclick="deleteUser('${userIdToDelete}')"]`).closest('tr');
            const userRole = userRow.querySelector('td:nth-child(4)').textContent.trim().toLowerCase();
            
            // Determine endpoint based on user role
            let deleteEndpoint = '';
            if (userRole.includes('admin')) {
                deleteEndpoint = '/admin/delete';
            } else if (userRole.includes('editor')) {
                deleteEndpoint = '/editor/delete';
            } else {
                deleteEndpoint = '/user/delete';
            }
            
            console.log('Deleting user with role:', userRole, 'using endpoint:', deleteEndpoint);
            
            // Send delete request
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
                        // Reload page on successful deletion
                        window.location.reload();
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