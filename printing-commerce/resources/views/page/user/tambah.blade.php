<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
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
    const reff = '/user';
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
            $nav = 'user';
        @endphp
        @include('components.admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper" style="background-color: #efefef;">
            <!--  Header Start -->
            @include('components.admin.header')
            <!--  Header End -->
            <div class="container-fluid" style="background-color: #F6F9FF">
                <div class="pagetitle mt-2 mt-sm-3 mt-md-3 mt-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4">
                    <h1>Tambah User</h1>
                </div>
                <div class="container py-4">
                    <div class="card">
                        <div class="card-body">
                            <form id="tambahForm" class="needs-validation" novalidate>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inpNama" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="inpNama" required>
                                            <div class="invalid-feedback">
                                                Nama lengkap harus diisi
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inpEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="inpEmail" required>
                                            <div class="invalid-feedback">
                                                Email harus diisi dengan format yang benar
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inpJenisKelamin" class="form-label">Jenis Kelamin</label>
                                            <select class="form-select" id="inpJenisKelamin" required>
                                                <option value="" selected disabled>Pilih Kelamin</option>
                                                <option value="laki-laki">Laki-Laki</option>
                                                <option value="perempuan">Perempuan</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Jenis kelamin harus dipilih
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inpNomerTelepon" class="form-label">Nomor Telepon</label>
                                            <input type="tel" class="form-control" id="inpNomerTelepon" required pattern="08[0-9]{9,11}">
                                            <div class="invalid-feedback">
                                                Nomor telepon harus diisi dengan format yang benar (dimulai dengan 08)
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inpPassword" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="inpPassword" required oninput="showEyePass()">
                                                <button class="btn btn-outline-secondary" type="button" id="iconPass" onclick="showPass()" style="display: none">
                                                    <i class="fas fa-eye-slash" id="passClose" ></i>
                                                    <i class="fas fa-eye" id="passShow" style="display: none"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">
                                                Password harus diisi (min. 8 karakter, huruf besar, huruf kecil, angka, dan karakter khusus)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Foto</label>
                                            <div class="dropzone-container border rounded p-3 text-center" onclick="handleFileClick()" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" style="cursor: pointer;">
                                                <img src="{{ asset($tPath.'assets2/icon/upload.svg') }}" alt="" id="icon" class="mb-2" style="max-width: 50px;">
                                                <p class="mb-0">Pilih File atau Jatuhkan File</p>
                                                <input type="file" id="inpFoto" hidden onchange="handleFileChange(event)" accept="image/jpeg,image/png">
                                                <img src="" alt="" id="file" class="img-preview mt-2" style="display:none; max-width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex gap-2 justify-content-end">
                                        <a href="/admin" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus me-2"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset($tPath.'assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/page/tambahUser.js') }}"></script>
</body>
</html>