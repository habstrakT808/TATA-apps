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
    <script>
        // Variabel global
        const tPath = '{{ $tPath }}';
        const csrfToken = '{{ csrf_token() }}';
        let uploadedFile = null;
        const allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
        
        // Fungsi untuk membuka modal gambar
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
        
        // Fungsi untuk download gambar
        function downloadImage(imageSrc, fileName) {
            const link = document.createElement('a');
            link.href = imageSrc;
            link.download = fileName || 'gambar-referensi';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Fungsi untuk menangani klik pada tombol upload
        function handleFileClick() {
            document.getElementById('input_hasil').click();
        }
        
        // Fungsi untuk menangani perubahan file
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
        
        // Fungsi untuk memperbarui UI preview hasil desain
        function updateHasilDesainUIPreview(filename) {
            // Get the preview button element
            const previewButton = document.querySelector('.btn-outline-primary[onclick*="revisi_editor"]') || 
                                  document.querySelector('.btn-outline-secondary');
            
            if (previewButton) {
                // Update preview button text to show selected file
                const fileExtension = filename.split('.').pop();
                previewButton.innerHTML = `Gambar.${fileExtension}`;
                
                // Disable the onclick since file is not uploaded yet
                previewButton.setAttribute('onclick', '');
                previewButton.style.opacity = '0.7';
                previewButton.title = 'File dipilih, belum diupload';
            }
        }
        
        // Fungsi untuk memperbarui UI hasil desain
        function updateHasilDesainUI(filename, filePath) {
            // Get the button element
            const previewButton = document.querySelector('.btn-outline-primary[onclick*="revisi_editor"]') || 
                                  document.querySelector('.btn-outline-secondary');
            const uploadButton = document.querySelector('.btn-success[onclick="handleFileClick()"]');
            
            if (previewButton) {
                // Update preview button after successful upload
                const fileExtension = filename.split('.').pop();
                previewButton.innerHTML = `Gambar.${fileExtension}`;
                previewButton.setAttribute('onclick', `openImageModal('${filePath}')`);
                previewButton.style.opacity = '1';
                previewButton.title = 'Klik untuk melihat gambar';
                previewButton.classList.remove('btn-outline-secondary');
                previewButton.classList.add('btn-outline-primary');
                
                // Change upload button text to indicate file was uploaded
                if (uploadButton) {
                    uploadButton.innerHTML = 'Berhasil Diupload';
                    uploadButton.classList.remove('btn-success');
                    uploadButton.classList.add('btn-outline-success');
                    
                    // Reset after 3 seconds
                    setTimeout(() => {
                        uploadButton.innerHTML = 'Upload';
                        uploadButton.classList.remove('btn-outline-success');
                        uploadButton.classList.add('btn-success');
                    }, 3000);
                }
            }
        }
        
        // Fungsi untuk upload file dan menyimpan perubahan
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
        
        // Fungsi untuk menyimpan perubahan status
        function saveStatusChanges() {
            const xhr = new XMLHttpRequest();
            xhr.open('PUT', '/pesanan/update', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            
            // Prepare the data to send
            const dataToSend = {
                id_pesanan: '{{ $pesananData['uuid'] }}',
                status_pengerjaan: document.getElementById('statusSelect').value,
                editor_id: document.querySelector('select[name="editor_id"]').value,
                estimasi_mulai: document.querySelector('input[name="estimasi_mulai"]').value,
                estimasi_selesai: document.querySelector('input[name="estimasi_selesai"]').value,
                maksimal_revisi: document.querySelector('input[name="maksimal_revisi"]').value
            };
            
            console.log('Sending data:', dataToSend); // Debug log
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('preloader').style.display = 'none';
                    
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            showGreenPopup(response.message);
                            setTimeout(function() {
                                window.location.reload(); // Reload the page to see changes
                            }, 2000);
                        } else {
                            showRedPopup(response.message);
                        }
                    } else {
                        showRedPopup('Terjadi kesalahan saat menyimpan');
                        console.error('Error:', xhr.status, xhr.responseText); // Debug log
                    }
                }
            };
            
            xhr.send(JSON.stringify(dataToSend));
        }
        
        // Fungsi untuk menyimpan perubahan
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
        
        // Fungsi untuk membatalkan perubahan
        function cancelEdit() {
            if (confirm('Apakah Anda yakin ingin membatalkan perubahan?')) {
                window.location.reload(); // Reload the page to reset form
            }
        }
        
        // Fungsi popup
        function showGreenPopup(message) {
            const greenPopup = document.querySelector('#greenPopup');
            if (greenPopup) {
                greenPopup.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1050;"></div>
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; z-index: 1051; text-align: center;">
                        <div style="color: green; font-size: 24px; margin-bottom: 10px;">✓</div>
                        <div>${message}</div>
                    </div>
                `;
                greenPopup.style.display = 'block';
                setTimeout(() => {
                    greenPopup.style.display = 'none';
                    greenPopup.innerHTML = '';
                }, 2000);
            }
        }
        
        function showRedPopup(message) {
            const redPopup = document.querySelector('#redPopup');
            if (redPopup) {
                redPopup.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1050;"></div>
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; z-index: 1051; text-align: center;">
                        <div style="color: red; font-size: 24px; margin-bottom: 10px;">✗</div>
                        <div>${message}</div>
                    </div>
                `;
                redPopup.style.display = 'block';
                setTimeout(() => {
                    redPopup.style.display = 'none';
                    redPopup.innerHTML = '';
                }, 2000);
            }
        }
        
        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            // Enable form fields that should be editable
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // List of fields that should remain readonly
            const readOnlyFields = ['id_pesanan', 'nama_pelanggan', 'jenis_jasa', 'kelas_jasa', 'deskripsi'];
            
            inputs.forEach(input => {
                // Only enable fields that should be editable
                if (!readOnlyFields.includes(input.name)) {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                }
            });
            
            // Handle image load errors on thumbnail
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
    </script>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Jenis Jasa</label>
                                            <input type="text" name="jenis_jasa" class="form-control" value="{{ $pesananData['jenis_jasa'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Kelas Jasa</label>
                                            <input type="text" name="kelas_jasa" class="form-control" value="{{ $pesananData['kelas_jasa'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Sisa Revisi</label>
                                            <input type="number" name="maksimal_revisi" class="form-control" value="{{ $pesananData['maksimal_revisi'] }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Status Pengerjaan</label>
                                            <div>
                                                @php
                                                    $statusClass = 'status-menunggu';
                                                    $statusText = 'Menunggu';
                                                    
                                                    if(isset($pesananData['status_pengerjaan'])) {
                                                        switch($pesananData['status_pengerjaan']) {
                                                            case 'diproses':
                                                                $statusClass = 'status-proses';
                                                                $statusText = 'Diproses';
                                                                break;
                                                            case 'dikerjakan':
                                                                $statusClass = 'status-dikerjakan';
                                                                $statusText = 'Dikerjakan';
                                                                break;
                                                            case 'selesai':
                                                                $statusClass = 'status-selesai';
                                                                $statusText = 'Selesai';
                                                                break;
                                                            default:
                                                                $statusClass = 'status-menunggu';
                                                                $statusText = 'Menunggu';
                                                        }
                                                    }
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </div>
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
                                                    Gambar.{{ pathinfo($pesananData['gambar_referensi'])['extension'] }}
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" onclick="downloadImage('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/catatan_pesanan/' . $pesananData['gambar_referensi']) }}', '{{ basename($pesananData['gambar_referensi']) }}')">
                                                    Download
                                                </button>
                                            @else
                                                <div class="text-muted">Tidak ada gambar referensi</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Estimasi Pengerjaan -->
                                <div class="row mb-3">
                                    <div class="col-12 mb-2">
                                        <label class="form-label">Estimasi Pengerjaan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Dari</label>
                                            <input type="date" name="estimasi_mulai" class="form-control" value="{{ isset($pesananData['estimasi_mulai']) ? $pesananData['estimasi_mulai'] : $pesananData['estimasi_waktu']['dari'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Sampai</label>
                                            <input type="date" name="estimasi_selesai" class="form-control" value="{{ isset($pesananData['estimasi_selesai']) ? $pesananData['estimasi_selesai'] : $pesananData['estimasi_waktu']['sampai'] }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Hasil Desain -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Hasil Desain</label>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(isset($pesananData['file_hasil_desain']) && !empty($pesananData['file_hasil_desain']))
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageModal('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/hasil_desain/' . $pesananData['file_hasil_desain']) }}')">
                                                    Gambar.{{ pathinfo($pesananData['file_hasil_desain'])['extension'] }}
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" onclick="downloadImage('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/hasil_desain/' . $pesananData['file_hasil_desain']) }}', '{{ $pesananData['file_hasil_desain'] }}')">
                                                    Download
                                                </button>
                                            @else
                                                <!-- Upload button tetap ditampilkan untuk admin -->
                                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                    Belum ada hasil desain
                                                </button>
                                            @endif
                                            <input type="file" id="input_hasil" name="gambar_hasil_desain" hidden onchange="handleFileChange(event)">
                                            <button type="button" class="btn btn-success btn-sm" onclick="handleFileClick()">
                                                Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Gambar Revisi Pelanggan (jika ada) -->
                                @if(isset($pesananData['revisi_user']) && !empty($pesananData['revisi_user']))
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Gambar Revisi Pelanggan</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageModal('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/revisi_user/' . $pesananData['revisi_user']) }}')">
                                                Gambar.{{ pathinfo($pesananData['revisi_user'])['extension'] }}
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" onclick="downloadImage('{{ asset('assets3/img/pesanan/'. $pesananData['uuid'] . '/revisi_user/' . $pesananData['revisi_user']) }}', '{{ basename($pesananData['revisi_user']) }}')">
                                                Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Nama Editor -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Nama Editor</label>
                                            <select class="form-select" name="editor_id">
                                                @if(!isset($pesananData['editor_assigned']) || !$pesananData['editor_assigned'])
                                                    <option value="">Pilih Editor</option>
                                                @endif
                                                @foreach($editorList as $editor)
                                                    <option value="{{ $editor->id_editor }}" {{ isset($pesananData['id_editor']) && $pesananData['id_editor'] == $editor->id_editor ? 'selected' : '' }}>{{ $editor->nama_editor }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">
                                            Kembali
                                        </button>
                                        <div class="d-flex gap-2 align-items-center">
                                            <select name="status_pengerjaan" id="statusSelect" class="form-select form-select-sm">
                                                <option value="menunggu" {{ isset($pesananData['status_pengerjaan']) && $pesananData['status_pengerjaan'] == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                <option value="diproses" {{ isset($pesananData['status_pengerjaan']) && $pesananData['status_pengerjaan'] == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                                <option value="dikerjakan" {{ isset($pesananData['status_pengerjaan']) && $pesananData['status_pengerjaan'] == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                                <option value="selesai" {{ isset($pesananData['status_pengerjaan']) && $pesananData['status_pengerjaan'] == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success btn-sm" onclick="saveChanges()">
                                                    Save
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="cancelEdit()">
                                                    Cancel
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
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Tutup
                            </button>
                            <a id="downloadBtn" href="" download="" class="btn btn-success btn-sm">
                                Download
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
</body>

</html>