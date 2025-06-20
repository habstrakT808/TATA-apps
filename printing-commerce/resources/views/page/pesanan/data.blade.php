<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/page/modalDelete.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <style>
    #btnTambah{
        padding: 0px;
        display: flex;
        width: 145px;
        height: 45px;
        align-items: center;
        justify-content: space-evenly;
        border: none;
        font-size: 15px;
    }
    #btnTambah img{
        width: 30px;
        height: 30px;
    }
    /* Status filter styles */
    .status-filters .btn {
        font-size: 14px;
        padding: 8px 20px;
        transition: all 0.3s ease;
        border-radius: 4px;
        margin-right: 5px;
    }
    .status-filters .btn-outline-primary {
        background-color: white;
        color: #3E97FF;
        border-color: #3E97FF;
    }
    .status-filters .btn-primary {
        background-color: #3E97FF;
        border-color: #3E97FF;
        font-weight: 600;
    }
    
    /* Table styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table th {
        background-color: #F9FAFB;
        color: #252F40;
        font-weight: 600;
        padding: 12px 16px;
    }
    .table td {
        padding: 12px 16px;
        vertical-align: middle;
        color: #252F40;
    }
    .table tr:hover {
        background-color: #F6F9FF;
    }
    
    th {
        white-space: nowrap;
    }
    th:nth-child(5) {
        width: 30%;
    }
    td:last-child {
        position: relative;
        display: flex;
        flex-direction: row;
    }
    .btn-edit,
    .btn-delete{
        padding: 0px;
        display: flex;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 17px;
    }
    .btn-edit img,
    .btn-delete img{
        width: 20px;
        height: 20px;
    }
    .btn-delete,
    .btn-delete:hover{
        background-color: #FA64B5;
    }
    
    /* Menunggu tab header */
    .tab-header {
        background-color: #3E97FF;
        color: white;
        padding: 12px 20px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-weight: 600;
        font-size: 18px;
    }
    
    @media screen and (min-width: 700px) and (max-width: 1100px) {
        #btnTambah{
            width: 135px;
            height: 43px;
            font-size: 14px;
        }
        #btnTambah img{
            width: 26px;
            height: 26px;
        }
        .status-filters .btn {
            font-size: 13px;
            padding: 5px 12px;
        }
        .btn-edit,
        .btn-delete{
            width: 35px;
            height: 35px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 18px;
            height: 18px;
        }
    }
    @media screen and (min-width: 500px) and (max-width: 700px) {
        #btnTambah{
            width: 125px;
            height: 40px;
            font-size: 13px;
        }
        #btnTambah img{
            width: 23px;
            height: 23px;
        }
        .status-filters .btn {
            font-size: 12px;
            padding: 4px 10px;
        }
        .table{
            margin-top: 7px;
        }
        .table>:not(caption)>*>*{
            padding: 7px 7px;
        }
        th h6{
            font-size: 14px;
        }
        td{
            font-size: 13px;
        }
        td:last-child {
            flex-direction: column;
        }
        .btn-edit,
        .btn-delete{
            width: 30px;
            height: 30px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 16px;
            height: 16px;
        }
    }
    @media screen and (max-width: 500px) {
        #btnTambah{
            width: 115px;
            height: 37px;
            font-size: 13px;
        }
        #btnTambah img{
            width: 20px;
            height: 20px;
        }
        .status-filters .btn {
            font-size: 11px;
            padding: 3px 8px;
            margin-bottom: 5px;
        }
        .table{
            margin-top: 7px;
        }
        .table>:not(caption)>*>*{
            padding: 5px 5px;
        }
        th h6{
            font-size: 12px;
        }
        td{
            font-size: 11px;
        }
        td:last-child {
            flex-direction: column;
        }
        .btn-edit,
        .btn-delete{
            width: 28px;
            height: 28px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 14px;
            height: 14px;
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
    @if(isset($default_url) && $default_url)
    history.replaceState(null, document.title, "{{ $default_url }}");
    @endif
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
                    <h1>Kelola Pesanan</h1>
                </div>
                
                <!-- Status filter buttons -->
                <div class="status-filters mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/pesanan?status=menunggu') }}" class="btn {{ $currentStatus == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">Menunggu</a>
                        <a href="{{ url('/pesanan?status=proses') }}" class="btn {{ $currentStatus == 'diproses' ? 'btn-primary' : 'btn-outline-primary' }}">Diproses</a>
                        <a href="{{ url('/pesanan?status=dikerjakan') }}" class="btn {{ $currentStatus == 'dikerjakan' ? 'btn-primary' : 'btn-outline-primary' }}">Dikerjakan</a>
                        <a href="{{ url('/pesanan?status=selesai') }}" class="btn {{ $currentStatus == 'selesai' ? 'btn-primary' : 'btn-outline-primary' }}">Selesai</a>
                    </div>
                </div>
                
                <div class="d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="tab-header">
                            @if($currentStatus == 'pending')
                                Menunggu
                            @elseif($currentStatus == 'diproses')
                                Diproses
                            @elseif($currentStatus == 'dikerjakan')
                                Dikerjakan
                            @elseif($currentStatus == 'selesai')
                                Selesai
                            @else
                                {{ ucfirst($currentStatus) }}
                            @endif
                        </div>
                        <div class="card-body p-4" style="box-shadow: rgba(145,158,171,0.2) 0px 0px 2px 0px, rgba(145,158,171,0.12) 0px 12px 24px -4px;">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">No</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Pelanggan</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Kelas</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Kategori</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Deskripsi Pesanan</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Deadline</h6>
                                            </th>
                                            @if(in_array($currentStatus, ['dikerjakan', 'selesai']))
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Nama Editor</h6>
                                            </th>
                                            @endif
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Aksi</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($dataPesanan as $data)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">{{ $no++ }}</h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ $data['nama_user'] }}</span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ ucfirst($data['kelas_jasa'] ?? 'Standar') }}</span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ ucfirst($data['kategori'] ?? 'Logo') }}</span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ \Illuminate\Support\Str::limit($data['deskripsi'] ?? 'Logo brand makanan, simpel dan modern. Warna hangat, elemen makanan atau alat makan. Harus menarik dan mudah dikenali.', 100, '...') }}</span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">{{ $data['estimasi_waktu']}}</p>
                                            </td>
                                            @if(in_array($currentStatus, ['dikerjakan', 'selesai']))
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">{{ $data['nama_editor'] ?? 'Burhan Harahab'}}</p>
                                            </td>
                                            @endif
                                            <td class="border-bottom-0">
                                                <div class="d-flex">
                                                    <a href="/pesanan/detail/{{ $data['uuid'] }}" class="btn btn-warning m-1" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                        <img src="{{ asset($tPath.'assets2/icon/edit.svg') }}" alt="Edit" style="width: 20px; height: 20px;">
                                                    </a>
                                                    <button type="button" class="btn btn-danger m-1" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="showModalDelete('{{ $data['uuid'] }}')">
                                                        <img src="{{ asset($tPath.'assets2/icon/delete.svg') }}" alt="Delete" style="width: 20px; height: 20px;">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @include('components.admin.footer')
            </div>
        </div>
    </div>
    @php
    $modalDelete = 'pesanan';
    $modalTitle = 'Konfirmasi Hapus Pesanan';
    $modalMessage = 'Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.';
    $confirmText = 'Hapus Pesanan';
    @endphp
    @include('components.admin.modalDelete')
    @include('components.preloader')
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script>
        const modalDelete = document.querySelector('#modalDelete');
        const deleteForm = document.getElementById('deleteForm');
        const inpID = document.getElementById('inpID');
        let isAnimating = false;
        deleteForm.addEventListener('click',function(event){
            event.stopPropagation();
        });
        function showModalDelete(id){
            inpID.value = id;
            modalDelete.style.display = 'block';
            animateModalDelete('20%');
        }
        function closeModalDelete(){
            animateModalDelete('-20%');
        }
        function animateModalDelete(finalTop) {
            let currentTop = parseInt(deleteForm.style.top) || 0;
            let increment = currentTop < parseInt(finalTop) ? 1 : -1;
            function frame() {
                currentTop += increment;
                deleteForm.style.top = currentTop + '%';
                if ((increment === 1 && currentTop >= parseInt(finalTop)) || (increment === -1 && currentTop <= parseInt(finalTop))) {
                    clearInterval(animationInterval);
                    if (finalTop === '20%') {
                        isAnimating = false;
                    } else {
                        modalDelete.style.display = 'none';
                    }
                }
            }
            let animationInterval = setInterval(frame, 5);
        }
        function showLoading() {
            document.querySelector("div#preloader").style.display = "block";
        }
        function closeLoading() {
            document.querySelector("div#preloader").style.display = "none";
        }
        deleteForm.onsubmit = function (event) {
            event.preventDefault();
            showLoading();
            var xhr = new XMLHttpRequest();
            var requestBody = {
                id_pesanan: inpID.value.trim(),
            };
            xhr.open("DELETE", "/pesanan/delete");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(JSON.stringify(requestBody));
            xhr.onreadystatechange = function () {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        closeLoading();
                        var response = JSON.parse(xhr.responseText);
                        showGreenPopup(response.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        closeLoading();
                        var response = JSON.parse(xhr.responseText);
                        showRedPopup(response.message);
                    }
                }
            };
            return false;
        };
    </script>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/app.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showRedPopup("{{ session('error') }}");
        });
    </script>
    @endif
    
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showGreenPopup("{{ session('success') }}");
        });
    </script>
    @endif
</body>
</html>