<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <style>
        body {
            background-color: #F6F9FF;
        }
        .container-fluid {
            padding: 20px;
        }
        .card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
            display: block;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            width: 100%;
            background-color: #f9f9f9;
            color: #333;
            font-size: 14px;
        }
        .form-control:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        .image-container {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            margin-right: 10px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }
        .price-input {
            position: relative;
        }
        .price-input::before {
            content: "Rp";
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .price-input input {
            padding-left: 40px;
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
        const reff = '/payment-methods';
        var csrfToken = "{{ csrf_token() }}";
        var userAuth = @json($userAuth);
    </script>

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        
        @php
            $nav = 'metode-pembayaran';
        @endphp
        @include('components.admin.sidebar')

        <div class="body-wrapper">
            @include('components.admin.header')

            <div class="container-fluid">
                <h1 class="page-title">Detail Pembayaran</h1>
                <h2 class="section-title">{{ $metodePembayaranData['nama_metode_pembayaran'] }}</h2>
                
                <div class="card">
                    <div class="detail-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" class="form-control" value="{{ $metodePembayaranData['no_metode_pembayaran'] }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Harga Jasa</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="{{ $metodePembayaranData['harga_jasa'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Desain Logo</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="{{ $metodePembayaranData['harga_logo'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Desain Poster</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="{{ $metodePembayaranData['harga_poster'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Desain Banner</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="{{ $metodePembayaranData['harga_banner'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h3 class="section-title">Cetak Poster</h3>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Bahan Poster</label>
                                    <input type="text" class="form-control" value="Art Paper" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" class="form-control" value="A3" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Total Harga</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="150.000" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h3 class="section-title">Cetak Banner</h3>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Bahan Banner</label>
                                    <input type="text" class="form-control" value="{{ $metodePembayaranData['bahan_banner'] }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" class="form-control" value="{{ $metodePembayaranData['ukuran'] }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Total Harga</label>
                                    <div class="price-input">
                                        <input type="text" class="form-control" value="{{ $metodePembayaranData['total_harga'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn-action btn-secondary" onclick="window.location.href='/payment-methods'">Cancel</button>
                        <button type="button" class="btn-action btn-primary" onclick="window.location.href='/payment-methods/edit/{{ $metodePembayaranData['uuid'] }}'">Edit</button>
                    </div>
                </div>
            </div>
            @include('components.admin.footer')
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
</body>
</html> 