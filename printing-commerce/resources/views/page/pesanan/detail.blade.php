<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .detail-title {
            font-size: 1.5rem;
            margin: 0;
            color: #333;
        }
        .detail-subtitle {
            color: #666;
            margin: 0.5rem 0;
        }
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        .dropzone-container {
            border: 2px dashed #ddd;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dropzone-container:hover {
            border-color: #4CAF50;
        }
        .img-preview {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-success {
            background-color: #4CAF50;
            border: none;
        }
        .btn-success:hover {
            background-color: #43A047;
        }
        .btn-danger {
            background-color: #f44336;
            border: none;
        }
        .btn-danger:hover {
            background-color: #e53935;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-menunggu { background-color: #FFF3CD; color: #856404; }
        .status-proses { background-color: #CCE5FF; color: #004085; }
        .status-dikerjakan { background-color: #D4EDDA; color: #155724; }
        .status-revisi { background-color: #F8D7DA; color: #721C24; }
        .status-selesai { background-color: #D1E7DD; color: #0F5132; }
        .status-dibatalkan { background-color: #E2E3E5; color: #383D41; }
        
        .revision-item {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6 !important;
        }
        .file-item {
            background-color: white;
            transition: all 0.3s ease;
        }
        .file-item:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge {
            font-size: 0.75rem;
        }
        
        /* Image Preview Styles */
        .image-preview-container {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: #f8f9fa;
        }
        
        .image-thumbnail img {
            transition: all 0.3s ease;
            border-radius: 0.375rem;
        }
        
        .image-thumbnail img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .image-info h6 {
            font-weight: 600;
            color: #495057;
        }
        
        .btn-group .btn {
            border-radius: 0.25rem;
        }
        
        .btn-group .btn:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .btn-group .btn:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        /* Modal Enhancements */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
        
        #modalImage {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Error State */
        .image-error {
            border: 2px dashed #dc3545 !important;
            background-color: #f8d7da;
        }
        
        /* Loading State */
        .image-loading {
            position: relative;
            opacity: 0.7;
        }
        
        .image-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #007bff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Action Buttons Responsive Styles */
        @media screen and (max-width: 500px) {
            .row .col-12.d-flex.justify-content-between {
                flex-direction: column;
                gap: 10px;
            }
            
            .row .col-12.d-flex.justify-content-between button {
                width: 100%;
            }
            
            .row .col-12.d-flex.justify-content-between .d-flex {
                width: 100%;
                flex-direction: column;
            }
            
            #editModeControls {
                width: 100%;
                flex-direction: column;
                gap: 10px;
            }
            
            #editModeControls .d-flex {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            
            #editModeControls select,
            #editModeControls button {
                width: 100%;
            }
            
            #readModeControls {
                width: 100%;
            }
            
            #readModeControls button {
                width: 100%;
            }
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
    const reff = '/pesanan';
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
            $nav = 'pesanan';
        @endphp
        @include('components.admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('components.admin.header')
            <!--  Header End -->
            <div class="container-fluid" style="background-color: #F6F9FF">
                <div class="pagetitle mt-2 mt-sm-3 mt-md-3 mt-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4">
                    <h1>Detail Pesanan</h1>
                </div>

                <div class="container py-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Detail Pesanan</h2>
                            {{-- @php
                                $currentStatus = $pesananData['status_raw'];
                                $config = $statusConfig[$currentStatus] ?? [];
                            @endphp --}}
                            <form id="editForm" class="needs-validation" novalidate>
                                <!-- Basic Info Section -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">ID Pesanan</label>
                                            <input type="text" name="id_pesanan" class="form-control" value="{{ $pesananData['uuid'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nama Pelanggan</label>
                                            <input type="text" name="nama_pelanggan" class="form-control" value="{{ $pesananData['nama_pelanggan'] }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Jenis Jasa</label>
                                            <input type="text" name="jenis_jasa" class="form-control" value="{{ $pesananData['jenis_jasa'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Kelas Jasa</label>
                                            <input type="text" name="kelas_jasa" class="form-control" value="{{ $pesananData['kelas_jasa'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Sisa Revisi</label>
                                            <input type="text" name="sisa_revisi" class="form-control" value="{{ $pesananData['sisa_revisi'] }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Deskripsi Pesanan</label>
                                            <textarea name="deskripsi" class="form-control" rows="4" readonly>{{ $pesananData['deskripsi'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gambar Referensi Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Gambar Referensi</label>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(isset($pesananData['gambar_referensi']) && !empty($pesananData['gambar_referensi']) && !is_null($pesananData['gambar_referensi']))
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageModal('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/catatan_pesanan/' . $pesananData['gambar_referensi']) }}')">
                                                    <i class="fas fa-image"></i> {{ isset($pesananData['gambar_referensi']) && !empty($pesananData['gambar_referensi']) && !is_null($pesananData['gambar_referensi']) ? 'Gambar.' . pathinfo($pesananData['gambar_referensi'])['extension'] : 'Tidak ada gambar' }}
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" onclick="downloadImage('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/catatan_pesanan/' . $pesananData['gambar_referensi']) }}', '{{ basename($pesananData['gambar_referensi']) }}')">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            @else
                                                <div class="text-muted">Tidak ada gambar referensi</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Estimasi Pengerjaan -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Dari</label>
                                            <input type="date" name="tanggal_awal" class="form-control" value="{{ $pesananData['estimasi_waktu']['dari'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Sampai</label>
                                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $pesananData['estimasi_waktu']['sampai'] }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                @if(in_array($pesananData['status_pesanan'], ['dikerjakan', 'revisi', 'selesai']))
                                <!-- Hasil Desain -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Hasil Desain</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageModal('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/revisi_editor/' . $pesananData['revisi_editor_terbaru']) }}')">
                                                <i class="fas fa-image"></i> {{ isset($pesananData['revisi_editor_terbaru']) && !empty($pesananData['revisi_editor_terbaru']) && !is_null($pesananData['revisi_editor_terbaru']) ? 'Gambar.' . pathinfo($pesananData['revisi_editor_terbaru'])['extension'] : 'Tidak ada gambar' }}
                                            </button>
                                            <input type="file" id="input_hasil" name="gambar_hasil_desain" hidden onchange="handleFileChange(event)">
                                            <button type="button" class="btn btn-success btn-sm" onclick="handleFileClick()">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <!-- Gambar Referensi Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Gambar Revisi Pelanggan</label>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(isset($pesananData['revisi_user']) && !empty($pesananData['revisi_user']) && !is_null($pesananData['revisi_user']))
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageModal('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/revisi_user/' . $pesananData['revisi_user']) }}')">
                                                    <i class="fas fa-image"></i> {{ isset($pesananData['revisi_user']) && !empty($pesananData['revisi_user']) && !is_null($pesananData['revisi_user']) ? 'Gambar.' . pathinfo($pesananData['revisi_user'])['extension'] : 'Tidak ada gambar' }}
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" onclick="downloadImage('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/revisi_user/' . $pesananData['revisi_user']) }}', '{{ basename($pesananData['revisi_user']) }}')">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            @else
                                                <div class="text-muted">Tidak ada gambar referensi</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if(in_array($pesananData['status_pesanan'], ['diproses', 'menunggu_editor', 'dikerjakan', 'revisi']))
                                <!-- Editor Selection -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Nama Editor</label>
                                            <select class="form-select" name="editor_id">
                                                @if(!$pesananData['id_editor'])
                                                    <option value="">Pilih Editor</option>
                                                @endif
                                                @foreach($editorList as $editor)
                                                    <option value="{{ $editor->id_editor }}" {{ $pesananData['id_editor'] == $editor->id_editor ? 'selected' : '' }}>{{ $editor->nama_editor }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <!-- Action Buttons -->
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="history.back()">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </button>
                                        <div class="d-flex gap-2">
                                            <!-- Edit Mode Controls - Initially Hidden -->
                                            <div id="editModeControls" style="display: none;">
                                                <select name="status" id="statusSelect" class="form-select">
                                                    @foreach($pesananData['status_pesanan_list'] as $key => $status)
                                                        <option value="{{ $key }}" {{ $pesananData['status_pesanan'] == $key ? 'selected' : '' }}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-success" onclick="saveChanges()">
                                                        <i class="fas fa-save"></i> Simpan
                                                    </button>
                                                    <button type="button" class="btn btn-danger" onclick="cancelEdit()">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Read Mode Controls - Initially Visible -->
                                            <div id="readModeControls">
                                                <button type="button" class="btn btn-warning" onclick="enableEditMode()">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                        </div>
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
    
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">
                        <i class="fas fa-image me-2"></i>Gambar Referensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="Gambar Referensi" class="img-fluid" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <span id="imageFileName" class="text-muted"></span>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Tutup
                            </button>
                            <a id="downloadBtn" href="" download="" class="btn btn-success">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/app.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    
    <script>
        // Image Modal Functions
        function openImageModal(imageSrc) {
            if(imageSrc == '') {
                return;
            }
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            const imageFileName = document.getElementById('imageFileName');
            const downloadBtn = document.getElementById('downloadBtn');
            
            // Set image source
            modalImage.src = imageSrc;
            modalImage.onerror = function() {
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0yMDAgMTAwTDI4MCAyMDBIMTIwTDIwMCAxMDBaIiBmaWxsPSIjRENEQ0RDIi8+CjxjaXJjbGUgY3g9IjE2MCIgY3k9IjE0MCIgcj0iMTUiIGZpbGw9IiNEQ0RDREMiLz4KPHRleHQgeD0iMjAwIiB5PSIyNDAiIGZpbGw9IiM5OTk5OTkiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNiIgdGV4dC1hbmNob3I9Im1pZGRsZSI+R2FtYmFyIHRpZGFrIGRhcGF0IGRpdG11a2FuPC90ZXh0Pgo8L3N2Zz4K';
                imageFileName.textContent = 'Gambar tidak dapat dimuat';
                downloadBtn.style.display = 'none';
            };
            
            // Set file name
            const fileName = imageSrc.split('/').pop();
            imageFileName.textContent = fileName;
            
            // Set download link
            downloadBtn.href = imageSrc;
            downloadBtn.download = fileName;
            downloadBtn.style.display = 'inline-block';
            
            // Show modal
            modal.show();
        }
        // Handle image load errors on thumbnail
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.image-thumbnail img');
            thumbnails.forEach(img => {
                img.addEventListener('error', function() {
                    this.style.border = '2px dashed #dc3545';
                    this.title = 'Gambar tidak dapat dimuat';
                });
                
                img.addEventListener('load', function() {
                    this.style.border = '1px solid #dee2e6';
                    this.title = 'Klik untuk melihat gambar ukuran penuh';
                });
            });
        });

        /////
        function enableEditMode() {
            document.getElementById('editModeControls').style.display = 'flex';
            document.getElementById('editModeControls').style.gap = '10px';
            // document.getElementById('editModeControls').style.justifyContent = 'flex-end';
            document.getElementById('readModeControls').style.display = 'none';
            document.getElementById('readModeControls').style.gap = '0';
            // document.getElementById('readModeControls').style.justifyContent = 'flex-start';
            
            // Enable all form inputs
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // List of fields that should remain readonly
            const readOnlyFields = ['id_pesanan', 'nama_pelanggan', 'jenis_jasa', 'kelas_jasa', 'sisa_revisi', 'deskripsi', 'tanggal_awal', 'tanggal_selesai'];
            
            inputs.forEach(input => {
                // Only enable fields that should be editable
                if (!readOnlyFields.includes(input.name)) {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                }
            });
        }

        // Function to save changes
        function saveChanges() {
            document.getElementById('preloader').style.display = 'block';
            
            // Check if there's a file selected to upload
            if (uploadedFile) {
                // Upload file first, then save other changes
                uploadFileAndSaveChanges();
            } else {
                // No file selected, just save status changes
                saveStatusChanges();
            }
        }

        function uploadFileAndSaveChanges() {
            // Create FormData object for file upload
            const formData = new FormData();
            formData.append('gambar_hasil_desain', uploadedFile);
            formData.append('id_pesanan', '{{ $pesananData['uuid'] }}');
            formData.append('_token', csrfToken);
            
            // Upload file first
            fetch('/pesanan/upload-hasil-desain', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // File uploaded successfully, now save status changes
                    updateHasilDesainUI(data.filename, data.file_path);
                    uploadedFile = null; // Clear the file
                    saveStatusChanges();
                } else {
                    document.getElementById('preloader').style.display = 'none';
                    showRedPopup(data.message || 'Gagal mengupload file');
                }
            })
            .catch(error => {
                document.getElementById('preloader').style.display = 'none';
                console.error('Error:', error);
                showRedPopup('Terjadi kesalahan saat mengupload file');
            });
        }

        function saveStatusChanges() {
            const xhr = new XMLHttpRequest();
            xhr.open('PUT', '/pesanan/update', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('preloader').style.display = 'none';
                    
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            showGreenPopup(response.message);
                            setTimeout(function() {
                                window.location.href = '/pesanan';
                            }, 2000);
                        } else {
                            showRedPopup(response.message);
                        }
                    } else {
                        showRedPopup('Terjadi kesalahan saat menyimpan');
                    }
                }
            };
            
            xhr.send(JSON.stringify({
                id_pesanan: '{{ $pesananData['uuid'] }}',
                status: document.getElementById('statusSelect').value,
                editor_id: document.querySelector('select[name="editor_id"]').value
            }));
        }

        // Function to cancel edit mode
        function cancelEdit() {
            if (confirm('Apakah Anda yakin ingin membatalkan perubahan?')) {
                disableEditMode();
                // Here you would typically reset the form to its original values
                document.querySelector('form').reset();
            }
        }

        // Function to disable edit mode
        function disableEditMode() {
            document.getElementById('editModeControls').style.display = 'none';
            document.getElementById('readModeControls').style.display = 'block';
            
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // Disable all inputs
            inputs.forEach(input => {
                input.setAttribute('readonly', true);
                input.setAttribute('disabled', true);
            });
            form.querySelector('input[name="gambar_hasil_desain"]').removeAttribute('readonly');
            form.querySelector('input[name="gambar_hasil_desain"]').removeAttribute('disabled');
        }
        // Enhance download functionality
        function downloadImage(imageSrc, fileName) {
            const link = document.createElement('a');
            link.href = imageSrc;
            link.download = fileName || 'gambar-referensi';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Initialize the page in read-only mode
        document.addEventListener('DOMContentLoaded', function() {
            disableEditMode();
        });

        const allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
        let uploadedFile = null;

        function handleFileClick() {
            document.getElementById('input_hasil').click();
        }

        function handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (!allowedFormats.includes(file.type)) {
                    showRedPopup("Format file harus png, jpeg, atau jpg!");
                    return;
                }
                uploadedFile = file;
                
                // Update UI immediately without uploading
                updateHasilDesainUIPreview(file.name);
            }
        }

        function updateHasilDesainUIPreview(filename) {
            // Get the preview button element
            const previewButton = document.querySelector('.btn-outline-primary[onclick*="revisi_editor"]');
            
            if (previewButton) {
                // Update preview button text to show selected file
                const fileExtension = filename.split('.').pop();
                previewButton.innerHTML = `<i class="fas fa-image"></i> Gambar.${fileExtension}`;
                
                // Disable the onclick since file is not uploaded yet
                previewButton.setAttribute('onclick', '');
                previewButton.style.opacity = '0.7';
                previewButton.title = 'File dipilih, belum diupload';
            }
        }

        // Keep the upload function for when it's actually needed (e.g., during save)
        function uploadFile(file) {
            // Show preloader
            document.getElementById('preloader').style.display = 'block';
            
            // Create FormData object
            const formData = new FormData();
            formData.append('gambar_hasil_desain', file);
            formData.append('id_pesanan', '{{ $pesananData['uuid'] }}');
            formData.append('_token', csrfToken);
            
            // Send AJAX request
            fetch('/pesanan/upload-hasil-desain', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide preloader
                document.getElementById('preloader').style.display = 'none';
                
                if (data.status === 'success') {
                    // Show success message
                    showGreenPopup(data.message);
                    
                    // Update the UI after successful upload
                    updateHasilDesainUI(data.filename, data.file_path);
                    
                } else {
                    showRedPopup(data.message || 'Gagal mengupload file');
                }
            })
            .catch(error => {
                // Hide preloader
                document.getElementById('preloader').style.display = 'none';
                console.error('Error:', error);
                showRedPopup('Terjadi kesalahan saat mengupload file');
            });
        }

        function updateHasilDesainUI(filename, filePath) {
            // Get the button element
            const previewButton = document.querySelector('.btn-outline-primary[onclick*="revisi_editor"]');
            const uploadButton = document.querySelector('.btn-success[onclick="handleFileClick()"]');
            
            if (previewButton) {
                // Update preview button after successful upload
                const fileExtension = filename.split('.').pop();
                previewButton.innerHTML = `<i class="fas fa-image"></i> Gambar.${fileExtension}`;
                previewButton.setAttribute('onclick', `openImageModal('${filePath}')`);
                previewButton.style.opacity = '1';
                previewButton.title = 'Klik untuk melihat gambar';
                
                // Change upload button text to indicate file was uploaded
                if (uploadButton) {
                    uploadButton.innerHTML = '<i class="fas fa-check"></i> Berhasil Diupload';
                    uploadButton.classList.remove('btn-success');
                    uploadButton.classList.add('btn-outline-success');
                    
                    // Reset after 3 seconds
                    setTimeout(() => {
                        uploadButton.innerHTML = '<i class="fas fa-upload"></i> Upload';
                        uploadButton.classList.remove('btn-outline-success');
                        uploadButton.classList.add('btn-success');
                    }, 3000);
                }
            }
        }
    </script>
</body>

</html>