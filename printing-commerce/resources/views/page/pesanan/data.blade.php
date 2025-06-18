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
        padding: 6px 15px;
        transition: all 0.3s ease;
    }
    .status-filters .btn-outline-primary {
        background-color: white;
    }
    .status-filters .btn-primary {
        font-weight: 600;
    }
    
    th {
        white-space: nowrap;
    }
    th:nth-child(2) {
        width: 100%;
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
        width: 100px;
        height: 40px;
        align-items: center;
        justify-content: space-evenly;
        border: none;
        font-size: 17px;
    }
    .btn-edit img,
    .btn-delete img{
        width: 24px;
        height: 24px;
    }
    .btn-delete,
    .btn-delete:hover{
        background-color: #FA64B5;
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
            width: 90px;
            height: 40px;
            font-size: 16px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 22px;
            height: 22px;
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
            width: 90px;
            height: 40px;
            font-size: 15px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 21px;
            height: 21px;
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
            width: 80px;
            height: 37px;
            font-size: 14px;
        }
        .btn-edit img,
        .btn-delete img{
            width: 19px;
            height: 19px;
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
                        <a href="{{ url('/pesanan?status=menunggu') }}" class="btn {{ $currentStatus == 'pending' ? 'btn-secondary' : 'btn-outline-secondary' }}">Menunggu</a>
                        <a href="{{ url('/pesanan?status=proses') }}" class="btn {{ $currentStatus == 'diproses' ? 'btn-secondary' : 'btn-outline-secondary' }}">Proses</a>
                        <a href="{{ url('/pesanan?status=menunggu_editor') }}" class="btn {{ $currentStatus == 'menunggu_editor' ? 'btn-secondary' : 'btn-outline-secondary' }}">Menunggu Editor</a>
                        <a href="{{ url('/pesanan?status=dikerjakan') }}" class="btn {{ $currentStatus == 'dikerjakan' ? 'btn-secondary' : 'btn-outline-secondary' }}">Dikerjakan</a>
                        <a href="{{ url('/pesanan?status=revisi') }}" class="btn {{ $currentStatus == 'revisi' ? 'btn-secondary' : 'btn-outline-secondary' }}">Revisi</a>
                        <a href="{{ url('/pesanan?status=selesai') }}" class="btn {{ $currentStatus == 'selesai' ? 'btn-secondary' : 'btn-outline-secondary' }}">Selesai</a>
                        <a href="{{ url('/pesanan?status=dibatalkan') }}" class="btn {{ $currentStatus == 'dibatalkan' ? 'btn-secondary' : 'btn-outline-secondary' }}">Dibatalkan</a>
                    </div>
                </div>
                
                <div class="d-flex align-items-stretch">
                    <div class="card w-100">
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
                                                <h6 class="fw-semibold mb-0">Deadline</h6>
                                            </th>
                                            @if(in_array($currentStatus, ['menunggu_editor', 'dikerjakan', 'revisi']))
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
                                                <span class="fw-normal">{{ $data['nama_user'] }}
                                                </span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">{{ $data['estimasi_waktu']}}</p>
                                            </td>
                                            @if(in_array($currentStatus, ['menunggu_editor', 'dikerjakan', 'revisi']))
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">{{ $data['nama_editor']}}</p>
                                            </td>
                                            @endif
                                            <td class="border-bottom-0">
                                                <a href="/pesanan/detail/{{ $data['uuid'] }}" class="btn btn-warning btn-edit m-1" style="width: fit-content; height: fit-content; padding: 12px;">
                                                    <img src="{{ asset($tPath.'assets2/icon/detail.svg') }}" alt="">
                                                </a>
                                                <button type="button" class="btn btn-danger btn-delete m-1" style="width: fit-content; height: fit-content; padding: 12px;" onclick="showModalDelete('{{ $data['uuid'] }}')">
                                                    <img src="{{ asset($tPath.'assets2/icon/delete.svg') }}" alt="">
                                                </button>
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