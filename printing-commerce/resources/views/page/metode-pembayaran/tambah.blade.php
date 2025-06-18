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
        .container-fluid {
            background-color: #F6F9FF;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 24px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            width: 100%;
            margin-bottom: 16px;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76,175,80,0.1);
        }
        .image-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 4px;
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
            display: none;
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
        .btn-primary {
            background: #4CAF50;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-secondary {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px 20px;
            color: #333;
            border-radius: 4px;
            cursor: pointer;
        }
        .section-title {
            margin-bottom: 24px;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        const reff = '/metode-pembayaran';
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
                <div class="card">
                    <h5 class="section-title">Tambah Metode Pembayaran</h5>
                    
                    <form id="tambahForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Metode Pembayaran</label>
                                    <input type="text" class="form-control" name="nama_metode_pembayaran" maxlength="12" placeholder="Masukkan nama metode pembayaran">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nomor Rekening/VA</label>
                                    <input type="text" class="form-control" name="no_metode_pembayaran" maxlength="20" placeholder="Masukkan nomor rekening atau virtual account">
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
                                    <label class="form-label">Thumbnail</label>
                                    <div class="image-preview" onclick="document.getElementById('inpThumbnail').click()">
                                        <img src="" alt="Thumbnail Preview" id="thumbnailPreview">
                                        <div class="placeholder" id="thumbnailPlaceholder">
                                            <i class="fas fa-image"></i>
                                            <span>Pilih Gambar Thumbnail</span>
                                        </div>
                                    </div>
                                    <input type="file" id="inpThumbnail" name="thumbnail" hidden accept="image/*" onchange="previewImage(this, 'thumbnailPreview', 'thumbnailPlaceholder')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Icon</label>
                                    <div class="image-preview" onclick="document.getElementById('inpIcon').click()">
                                        <img src="" alt="Icon Preview" id="iconPreview">
                                        <div class="placeholder" id="iconPlaceholder">
                                            <i class="fas fa-image"></i>
                                            <span>Pilih Icon</span>
                                        </div>
                                    </div>
                                    <input type="file" id="inpIcon" name="icon" hidden accept="image/*" onchange="previewImage(this, 'iconPreview', 'iconPlaceholder')">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn-secondary" onclick="window.location.href='/metode-pembayaran'">Cancel</button>
                            <button type="submit" class="btn-primary">Save</button>
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