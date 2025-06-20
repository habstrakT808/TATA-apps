<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Metode Pembayaran | TATA</title>
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
            background-color: #fff;
            color: #333;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76,175,80,0.1);
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
        .image-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview .placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #666;
        }
        .image-preview .placeholder i {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .dropdown-container {
            position: relative;
        }
        .dropdown-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
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
                <h1 class="page-title">Tambah Metode Pembayaran</h1>
                
                <div class="card">
                    <form id="tambahForm">
                        <div class="detail-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Metode Pembayaran</label>
                                        <input type="text" class="form-control" name="nama_metode_pembayaran" maxlength="12" placeholder="Masukkan nama metode pembayaran">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nomor Rekening</label>
                                        <input type="text" class="form-control" name="no_metode_pembayaran" maxlength="20" placeholder="Masukkan nomor rekening">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Deskripsi 1</label>
                                        <textarea class="form-control" name="deskripsi_1" maxlength="500" placeholder="Masukkan deskripsi pertama"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Deskripsi 2</label>
                                        <textarea class="form-control" name="deskripsi_2" maxlength="500" placeholder="Masukkan deskripsi kedua"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Harga Jasa</label>
                                        <div class="dropdown-container">
                                            <select class="form-control dropdown-select" name="harga_jasa">
                                                <option value="Regular" selected>Regular</option>
                                                <option value="Premium">Premium</option>
                                                <option value="Exclusive">Exclusive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Desain Logo</label>
                                        <div class="price-input">
                                            <input type="text" class="form-control" name="harga_logo" value="150.000">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Desain Poster</label>
                                        <div class="price-input">
                                            <input type="text" class="form-control" name="harga_poster" value="150.000">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Desain Banner</label>
                                        <div class="price-input">
                                            <input type="text" class="form-control" name="harga_banner" value="150.000">
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
                                        <div class="dropdown-container">
                                            <select class="form-control dropdown-select" name="bahan_poster">
                                                <option value="Art Paper" selected>Art Paper</option>
                                                <option value="Photo Paper">Photo Paper</option>
                                                <option value="Vinyl">Vinyl</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Ukuran</label>
                                        <div class="dropdown-container">
                                            <select class="form-control dropdown-select" name="ukuran_poster">
                                                <option value="A3" selected>A3</option>
                                                <option value="A2">A2</option>
                                                <option value="A1">A1</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Total Harga</label>
                                        <div class="price-input">
                                            <input type="text" class="form-control" name="total_harga_poster" value="150.000">
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
                                        <div class="dropdown-container">
                                            <select class="form-control dropdown-select" name="bahan_banner">
                                                <option value="Flexi China" selected>Flexi China</option>
                                                <option value="Albatros">Albatros</option>
                                                <option value="Vinyl">Vinyl</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Ukuran</label>
                                        <div class="dropdown-container">
                                            <select class="form-control dropdown-select" name="ukuran">
                                                <option value="1 x 2 m" selected>1 x 2 m</option>
                                                <option value="2 x 3 m">2 x 3 m</option>
                                                <option value="3 x 4 m">3 x 4 m</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Total Harga</label>
                                        <div class="price-input">
                                            <input type="text" class="form-control" name="total_harga" value="200.000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn-action btn-secondary" onclick="window.location.href='/metode-pembayaran'">Cancel</button>
                            <button type="submit" class="btn-action btn-primary">Save</button>
                        </div>
                    </form>
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

    <script>
        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            $('#tambahForm').submit(function(e) {
                e.preventDefault();
                $('#preloader').show();

                const formData = new FormData(this);
                
                $.ajax({
                    url: domain + '/metode-pembayaran/create',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#preloader').hide();
                        if (response.status === 'success') {
                            showGreenPopup(response.message);
                            setTimeout(function() {
                                window.location.href = reff;
                            }, 2000);
                        } else {
                            showRedPopup(response.message);
                        }
                    },
                    error: function(xhr) {
                        $('#preloader').hide();
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            showRedPopup(response.message);
                        } else {
                            showRedPopup('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>