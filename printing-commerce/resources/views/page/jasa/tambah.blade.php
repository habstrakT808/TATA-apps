<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jasa | TATA</title>
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
                    <h1>Tambah Jasa</h1>
                </div>
                <div class="card">
                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Hanya boleh ada 3 jasa utama: Desain Logo, Desain Poster, dan Desain Banner.
                    </div>
                    <form id="createForm">
                        <div class="section-title">
                            <h5>Gambar Produk</h5>
                            <button type="button" class="btn-add-new" onclick="document.getElementById('inpImages').click()">
                                <i class="fas fa-plus"></i> Tambah Gambar
                            </button>
                        </div>

                        <div class="image-gallery" id="imageGallery">
                            <div class="add-image-btn" onclick="document.getElementById('inpImages').click()">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Gambar</span>
                            </div>
                        </div>
                        <input type="file" id="inpImages" name="images[]" hidden multiple accept="image/jpeg,image/png,image/jpg" onchange="handleImagesChange(event)">

                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" name="kategori" id="kategoriSelect">
                                <option value="">Pilih Kategori</option>
                                <option value="logo">Desain Logo</option>
                                <option value="poster">Desain Poster</option>
                                <option value="banner">Desain Banner</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi Jasa</label>
                            <textarea class="form-control" name="deskripsi_jasa" rows="4" placeholder="Masukkan deskripsi produk"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kelas Jasa</label>
                            <select class="form-control" id="kelasJasaSelect" name="kelas_jasa">
                                <option value="">Pilih Kelas Jasa</option>
                                <option value="basic">Basic</option>
                                <option value="standard">Standard</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>

                        <div id="additionalFields">
                            <div class="form-group">
                                <label class="form-label">Harga Jasa</label>
                                <input type="number" class="form-control" name="harga_paket_jasa" placeholder="Masukkan harga jasa">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Deskripsi Singkat</label>
                                <textarea class="form-control" name="deskripsi_singkat" rows="3" placeholder="Masukkan deskripsi singkat"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Waktu Pengerjaan</label>
                                <input type="text" class="form-control" name="waktu_pengerjaan" placeholder="Contoh: 3 hari, 1 minggu, dst">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Total Revisi</label>
                                <input type="number" class="form-control" name="maksimal_revisi" placeholder="Masukkan jumlah revisi">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn-secondary" onclick="window.location.href='/jasa'">Cancel</button>
                            <button type="submit" class="btn-primary">Save</button>
                        </div>
                    </form>
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
    <script>
        let selectedFiles = []; // Array to store selected files
        
        function handleImagesChange(event) {
            const files = event.target.files;
            const gallery = document.getElementById('imageGallery');
            const addButton = gallery.querySelector('.add-image-btn');
            
            // Check if adding these files would exceed the 5 image limit
            const currentImageCount = gallery.querySelectorAll('.gallery-item').length;
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
                    };
                    
                    galleryItem.appendChild(img);
                    galleryItem.appendChild(removeBtn);
                    gallery.insertBefore(galleryItem, addButton);
                };
                reader.readAsDataURL(file);
            }
            // Clear the input value but keep the files in our selectedFiles array
            event.target.value = '';
            
            // Hide add button if we've reached 5 images
            if (gallery.querySelectorAll('.gallery-item').length >= 5) {
                addButton.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('createForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                const kategori = document.querySelector('select[name="kategori"]').value;
                const deskripsiJasa = document.querySelector('textarea[name="deskripsi_jasa"]').value;
                const kelasJasa = document.querySelector('select[name="kelas_jasa"]').value;
                const hargaPaketJasa = document.querySelector('input[name="harga_paket_jasa"]').value;
                const deskripsiSingkat = document.querySelector('textarea[name="deskripsi_singkat"]').value;
                const waktuPengerjaan = document.querySelector('input[name="waktu_pengerjaan"]').value;
                const maksimalRevisi = document.querySelector('input[name="maksimal_revisi"]').value;
                
                if (!kategori) {
                    showRedPopup('Kategori wajib di isi');
                    return;
                }
                
                if (!deskripsiJasa) {
                    showRedPopup('Deskripsi Jasa wajib di isi');
                    return;
                }
                
                if (!kelasJasa) {
                    showRedPopup('Kelas Jasa wajib di isi');
                    return;
                }
                
                if (!hargaPaketJasa) {
                    showRedPopup('Harga Paket Jasa wajib di isi');
                    return;
                }
                
                if (!deskripsiSingkat) {
                    showRedPopup('Deskripsi Singkat wajib di isi');
                    return;
                }
                
                if (!waktuPengerjaan) {
                    showRedPopup('Waktu Pengerjaan wajib di isi');
                    return;
                }
                
                if (!maksimalRevisi) {
                    showRedPopup('Maksimal Revisi wajib di isi');
                    return;
                }
                
                if (selectedFiles.length === 0) {
                    showRedPopup('Gambar Jasa wajib di isi');
                    return;
                }
                
                document.getElementById('preloader').style.display = 'block';
                const formData = new FormData(this);
                
                // Add selected files to the form data
                selectedFiles.forEach((file, index) => {
                    formData.append('images[]', file);
                });
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', domain + '/jasa/create', true);
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