<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <style>
        .detail-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .detail-title {
            font-size: 24px;
            margin: 0;
        }
        .detail-subtitle {
            color: #666;
            margin: 5px 0 20px 0;
        }
        .detail-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-save {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    @if(app()->environment('local'))
    <script>
    var tPath = '';
    </script>
    @else
    <script>
    var tPath = '';
    </script>
    @endif
    <script>
    const domain = window.location.protocol + '//' + window.location.hostname + ":" + window.location.port;
    const reff = '/admin';
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
            $nav = 'admin';
        @endphp
        @include('components.admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper" style="background-color: #efefef;">
            <!--  Header Start -->
            @include('components.admin.header')
            <!--  Header End -->
            <div class="container-fluid">
                <div class="detail-container">
                    <div class="detail-header">
                        <div>
                            <h1 class="detail-title">Tambah Admin</h1>
                        </div>
                    </div>

                    <div class="detail-form">
                        <form id="tambahForm" class="needs-validation" novalidate>
                            <div class="form-group">
                                <label for="inpNama">Nama Admin</label>
                                <input type="text" class="form-control" id="inpNama" required maxlength="50">
                                <div class="invalid-feedback">
                                    Nama admin harus diisi
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inpEmail">Email</label>
                                <input type="email" class="form-control" id="inpEmail" required maxlength="45">
                                <div class="invalid-feedback">
                                    Email harus diisi dengan format yang benar
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inpPassword">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="inpPassword" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye-slash" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Password harus diisi minimal 8 karakter
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inpRole">Role</label>
                                <select class="form-control" id="inpRole" required>
                                    <option value="" selected disabled>Pilih Role</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="admin_chat">Admin Chat</option>
                                    <option value="admin_pemesanan">Admin Pemesanan</option>
                                </select>
                                <div class="invalid-feedback">
                                    Role harus dipilih
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="/admin" class="btn btn-cancel">Cancel</a>
                                <button type="submit" class="btn btn-save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                @include('components.admin.footer')
            </div>
        </div>
    </div>
    @include('components.preloader')
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/app.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/page/tambahAdmin.js') }}"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('inpPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>