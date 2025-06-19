<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jasa | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <!-- Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
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
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .image-item {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .add-image-btn {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: #777;
        }
        .add-image-btn i {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .form-group {
            margin-bottom: 20px;
            padding: 0 15px;
        }
        .paket-jasa-container {
            padding: 0 15px;
            margin-bottom: 20px;
        }
        .paket-card {
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .paket-card .card-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
        }
        .paket-card .card-body {
            padding: 15px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .btn-add-new {
            background-color: #00C4FF;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .image-preview {
            position: relative;
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thumbnail-container {
            margin-bottom: 20px;
        }
        .carousel-container {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .carousel-slide {
            position: relative;
            height: 250px;
            overflow: hidden;
            border-radius: 8px;
        }
        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .slick-prev:before, .slick-next:before {
            color: #007bff;
        }
        .upload-placeholder {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #777;
        }
        .upload-placeholder i {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .existing-image {
            position: relative;
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .thumbnail-gallery {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 0;
            margin-top: 15px;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .thumbnail.active {
            border-color: #4CAF50;
        }
        
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-add {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-return {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-edit-mode {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: auto;
        }
        
        .mode-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
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
    const reff = '/jasa';
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    var uuid = "{{ $jasa['uuid'] }}";
    var dataFetch = {!! json_encode($jasa) !!};
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
            $nav = 'jasa';
        @endphp
        @include('components.admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('components.admin.header')
            <!--  Header End -->
            <div class="container-fluid">
                <div class="pagetitle mt-2 mt-sm-3 mt-md-3 mt-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4">
                    <h1>Edit {{ $jasa['display_name'] }}</h1>
                </div>
                <div class="card">
                    <form id="editForm">
                        <input type="hidden" name="id_jasa" value="{{ $jasa['uuid'] }}">
                        <input type="hidden" name="deletedImages" id="deletedImages" value="[]">
                        
                        <div class="section-title">
                            <h5>Gambar Produk</h5>
                            <button type="button" class="btn-add-new" onclick="document.getElementById('inpImages').click()" style="{{ count($jasa['images']) >= 5 ? 'display: none;' : '' }}">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                        </div>

                        <div class="image-gallery" id="imageGallery">
                            @foreach($jasa['images'] as $image)
                                <div class="image-item">
                                    <img src="{{ asset($tPath.'assets3/img/jasa/'.$jasa['kategori'].'/'.$image->image_path) }}" alt="Gallery Image">
                                    <button type="button" class="remove-btn" onclick="deleteImage(this, {{ $image->id_jasa_image }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                            
                            @if(count($jasa['images']) < 5)
                                <div class="add-image-btn" onclick="document.getElementById('inpImages').click()">
                                    <i class="fas fa-plus"></i>
                                    <span>Tambah Gambar</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" id="inpImages" name="images[]" hidden multiple accept="image/jpeg,image/png,image/jpg" onchange="handleImagesChange(event)">

                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori" value="{{ $jasa['display_name'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi Jasa</label>
                            <textarea class="form-control" name="deskripsi_jasa" rows="3" placeholder="Masukkan deskripsi jasa">{{ $jasa['deskripsi_jasa'] }}</textarea>
                        </div>

                        <div class="paket-jasa-container">
                            <h5 class="mb-3">Paket Jasa</h5>
                            
                            @foreach($jasa['paket_jasa'] as $paket)
                                <div class="card mb-3 paket-card">
                                    <div class="card-header">
                                        <h6>{{ ucfirst($paket->kelas_jasa) }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="paket_id[]" value="{{ $paket->id_paket_jasa }}">
                                        
                                        <div class="form-group">
                                            <label class="form-label">Deskripsi Singkat</label>
                                            <textarea class="form-control" name="deskripsi_singkat[]" rows="2" placeholder="Masukkan deskripsi singkat">{{ $paket->deskripsi_singkat }}</textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Harga Paket</label>
                                            <input type="number" class="form-control" name="harga_paket_jasa[]" placeholder="Masukkan harga paket" value="{{ $paket->harga_paket_jasa }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Waktu Pengerjaan</label>
                                            <input type="text" class="form-control" name="waktu_pengerjaan[]" placeholder="Contoh: 3 hari" value="{{ $paket->waktu_pengerjaan }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Maksimal Revisi</label>
                                            <input type="number" class="form-control" name="maksimal_revisi[]" placeholder="Masukkan maksimal revisi" value="{{ $paket->maksimal_revisi }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group text-end">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='/jasa'">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
    <script src="{{ asset($tPath.'assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    <script>
        let deletedImageIds = [];
        let originalData = {};
        let currentData = {};
        let dataChanged = false;
        let selectedFiles = []; // Array to store selected files
        
        // Initialize data from the fetched JSON
        function initializeData() {
            originalData = JSON.parse(JSON.stringify(dataFetch));
            currentData = JSON.parse(JSON.stringify(dataFetch));
        }

        function handleImagesChange(event) {
            const files = event.target.files;
            const gallery = document.getElementById('imageGallery');
            const addButton = gallery.querySelector('.add-image-btn');
            
            // Check if adding these files would exceed the 5 image limit
            const currentImageCount = gallery.querySelectorAll('.gallery-item').length - 1; // -1 to exclude the add button
            const newImageCount = files.length;
            
            if (currentImageCount + newImageCount > 5) {
                showRedPopup("Maksimal 5 gambar untuk setiap jasa");
                event.target.value = '';
                return;
            }

            // Store the selected files for later submission
            for (let i = 0; i < files.length; i++) {
                selectedFiles.push(files[i]);
            }

            for (let file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const galleryItem = document.createElement('div');
                    galleryItem.className = 'gallery-item';
                    galleryItem.dataset.new = 'true';
                    galleryItem.dataset.filename = file.name; // Store filename for reference
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Gallery Image';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function() {
                        // Remove file from selectedFiles array
                        const filename = galleryItem.dataset.filename;
                        selectedFiles = selectedFiles.filter(f => f.name !== filename);
                        gallery.removeChild(galleryItem);
                        dataChanged = true;
                    };
                    
                    galleryItem.appendChild(img);
                    galleryItem.appendChild(removeBtn);
                    gallery.insertBefore(galleryItem, addButton);
                    
                    // Mark data as changed
                    dataChanged = true;
                };
                reader.readAsDataURL(file);
            }
            // Clear the input value but keep the files in our selectedFiles array
            event.target.value = '';
            
            // Hide add button if we've reached 5 images
            if (gallery.querySelectorAll('.gallery-item').length - 1 >= 5) {
                addButton.style.display = 'none';
            }
        }

        function removeImage(button) {
            const item = button.parentElement;
            const imageId = item.dataset.id;
            const gallery = document.getElementById('imageGallery');
            const addButton = gallery.querySelector('.add-image-btn');
            
            if (imageId) {
                // This is an existing image, add to deletedImageIds
                deletedImageIds.push(imageId);
                document.getElementById('deletedImages').value = JSON.stringify(deletedImageIds);
            } else if (item.dataset.new === 'true') {
                // This is a new image, remove from selectedFiles
                const filename = item.dataset.filename;
                if (filename) {
                    selectedFiles = selectedFiles.filter(f => f.name !== filename);
                }
            }
            
            // Mark data as changed
            dataChanged = true;
            
            // Remove the item from DOM
            item.remove();
            
            // Show add button if we now have fewer than 5 images
            if (gallery.querySelectorAll('.gallery-item').length - 1 < 5) {
                addButton.style.display = 'block';
            }
        }

        // Populate form fields based on selected kelas_jasa
        function populateFields(kelasJasa) {
            const paketJasaData = dataFetch.paket_jasa.find(paket => paket.kelas_jasa === kelasJasa);
            if (paketJasaData) {
                document.querySelector('input[name="harga_paket_jasa"]').value = paketJasaData.harga_paket_jasa;
                document.querySelector('textarea[name="deskripsi_singkat"]').value = paketJasaData.deskripsi_singkat;
                document.querySelector('input[name="waktu_pengerjaan"]').value = paketJasaData.waktu_pengerjaan;
                document.querySelector('input[name="maksimal_revisi"]').value = paketJasaData.maksimal_revisi;
                currentData.selectedPaket = paketJasaData;
            } else {
                document.querySelector('input[name="harga_paket_jasa"]').value = '';
                document.querySelector('textarea[name="deskripsi_singkat"]').value = '';
                document.querySelector('input[name="waktu_pengerjaan"]').value = '';
                document.querySelector('input[name="maksimal_revisi"]').value = '';
                
                currentData.selectedPaket = null;
            }
            
            dataChanged = false;
        }
        
        // Format date for display
        function formatDate(dateString, format) {
            // Handle null or undefined dates
            if (!dateString) {
                return '';
            }
            // Parse the date string
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return '';
            }
            
            if (format === 'dd-mm-yyyy') {
                // Ensure day and month are padded with leading zeros if needed
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}-${month}-${year}`;
            } else if (format === 'yyyy-mm-dd') {
                // Format for input[type="date"]
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${year}-${month}-${day}`;
            } else {
                // Default format
                return date.toISOString().slice(0, 16).replace('T', ' ');
            }
        }
        
        function hasDataChanged() {
            if (dataChanged || deletedImageIds.length > 0) {
                return true;
            }
            
            const selectedKelasJasa = document.getElementById('kelasJasaSelect').value;
            if (!selectedKelasJasa || !currentData.selectedPaket) {
                return false;
            }
            
            const hargaInput = document.querySelector('input[name="harga_paket_jasa"]').value;
            const waktuInput = document.querySelector('input[name="waktu_pengerjaan"]').value;
            const revisiInput = document.querySelector('input[name="maksimal_revisi"]').value;
            const deskripsiSingkatInput = document.querySelector('textarea[name="deskripsi_singkat"]').value;
            const deskripsiInput = document.querySelector('textarea[name="deskripsi_jasa"]').value;
            
            if (
                parseInt(hargaInput) !== currentData.selectedPaket.harga_paket_jasa ||
                waktuInput !== currentData.selectedPaket.waktu_pengerjaan ||
                parseInt(revisiInput) !== currentData.selectedPaket.maksimal_revisi ||
                deskripsiSingkatInput !== currentData.selectedPaket.deskripsi_singkat ||
                deskripsiInput !== dataFetch.deskripsi_jasa
            ) {
                return true;
            }
            
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize data
            initializeData();
            
            const kelasJasaSelect = document.getElementById('kelasJasaSelect');
            const additionalFields = document.getElementById('additionalFields');
            
            // Set up change event listeners for all form fields
            const formInputs = document.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                if (input.id !== 'kelasJasaSelect') {
                    input.addEventListener('change', function() {
                        dataChanged = true;
                    });
                    
                    if (input.tagName === 'TEXTAREA' || input.type === 'text' || input.type === 'number') {
                        input.addEventListener('input', function() {
                            dataChanged = true;
                        });
                    }
                }
            });
            
            // Set the initial selected value from dataFetch if available
            if (dataFetch && dataFetch.paket_jasa && dataFetch.paket_jasa.length > 0) {
                // Default to the first package
                const defaultPaket = dataFetch.paket_jasa[0];
                kelasJasaSelect.value = defaultPaket.kelas_jasa;
                
                // Show the fields
                additionalFields.style.display = 'block';
                
                // Populate fields with the default package data
                populateFields(defaultPaket.kelas_jasa);
            }
            
            kelasJasaSelect.addEventListener('change', function() {
                if (this.value) {
                    additionalFields.style.display = 'block';
                    populateFields(this.value);
                } else {
                    additionalFields.style.display = 'none';
                }
            });
            
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if data has been changed
                if (!hasDataChanged()) {
                    showRedPopup('Data belum diubah. Tidak ada perubahan untuk disimpan.');
                    return;
                }
                
                document.getElementById('preloader').style.display = 'block';
                const formData = new FormData(this);
                
                // Add selected files to the form data
                selectedFiles.forEach((file, index) => {
                    formData.append('images[]', file);
                });
                
                const selectedKelasJasa = kelasJasaSelect.value;
                if (selectedKelasJasa) {
                    formData.set('kelas_jasa', selectedKelasJasa);
                }
                formData.set('_method', 'PUT');
                formData.set('id_jasa', uuid);
                formData.set('deleted_images', JSON.stringify(deletedImageIds));
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', domain + '/jasa/update', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onload = function() {
                    document.getElementById('preloader').style.display = 'none';
                    if (xhr.status >= 200 && xhr.status < 300) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            showGreenPopup(response.message);
                            setTimeout(function() {
                                window.location.href = reff;
                            }, 2000);
                        } else {
                            showRedPopup(response.message);
                        }
                    } else {
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                        if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                        }
                        showRedPopup(errorMessage);
                    }
                };
                
                xhr.onerror = function() {
                    document.getElementById('preloader').style.display = 'none';
                    showRedPopup('Terjadi kesalahan jaringan. Silakan coba lagi.');
                };
                
                xhr.send(formData);
            });
        });
    </script>
</body>

</html>