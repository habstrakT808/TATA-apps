<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail User | TATA</title>
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
                    <h1>Detail User</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/user">Kelola User</a></li>
                            <li class="breadcrumb-item">Detail User</li>
                        </ol>
                    </nav>
                </div>
                <div class="container py-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="p-3 bg-white rounded shadow-sm">
                                        <img src="{{ $userData->foto ? asset($userData->foto) : asset($tPath.'assets3/img/user/1.jpg') }}" alt="Foto Profil" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title mb-4">Informasi User</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td width="30%"><strong>Nama Lengkap</strong></td>
                                                    <td width="5%">:</td>
                                                    <td>{{ $userData->nama_lengkap ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $userData->email_user ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jenis Kelamin</strong></td>
                                                    <td>:</td>
                                                    <td>{{ ucfirst($userData->jenis_kelamin) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Nomor Telepon</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $userData->no_telpon ? chunk_split($userData->no_telpon, 4, ' ') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal Dibuat</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $userData->created_at ? $userData->created_at->format('d F Y H:i') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Terakhir Diupdate</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $userData->updated_at ? $userData->updated_at->format('d F Y H:i') : '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if(isset($userData->activities) && count($userData->activities) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="card-title mb-4">Riwayat Aktivitas</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Aktivitas</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($userData->activities as $activity)
                                                <tr>
                                                    <td>{{ $activity->created_at->format('d F Y H:i') }}</td>
                                                    <td>{{ $activity->description }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $activity->status === 'success' ? 'success' : ($activity->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ $activity->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12 d-flex gap-2 justify-content-end">
                                    <a href="/user" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                </div>
                            </div>
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
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>

    <style>
        .badge {
            padding: 8px 12px;
            font-weight: 500;
        }
        .table td {
            padding: 12px 8px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-title {
            color: #333;
            font-weight: 600;
        }
    </style>
</body>
</html>