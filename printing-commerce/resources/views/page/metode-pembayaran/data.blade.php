<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Metode Pembayaran | TATA</title>
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
        width: 160px;
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
            width: 145px;
            height: 40px;
            font-size: 14px;
        }
        #btnTambah img{
            width: 25px;
            height: 25px;
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
            width: 135px;
            height: 35px;
            font-size: 13px;
        }
        #btnTambah img{
            width: 23px;
            height: 23px;
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
            width: 125px;
            height: 32px;
            font-size: 12px;
        }
        #btnTambah img{
            width: 20px;
            height: 20px;
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
    const reff = '/metode-pembayaran';
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
            $nav = 'metode-pembayaran';
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
                    <h1>Kelola Metode Pembayaran</h1>
                </div>
                <div class="d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-start mb-3">
                                <a href="/metode-pembayaran/tambah" class="btn btn-success d-flex align-items-center justify-content-center gap-2" id="btnTambah" style="width: 200px; height: 60px; padding-left: 16px; padding-right: 6px;">
                                    <img src="{{ asset($tPath.'assets2/icon/tambah.svg') }}" alt="Tambah" class="img-fluid" style="max-width: 50px;">
                                    <span class="d-none d-sm-inline">Tambah Metode Pembayaran</span>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">No</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Nama Rekening</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($metodePembayaranData as $data)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">{{ $no++ }}</h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ $data['nama_metode_pembayaran'] }}
                                                </span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <a href="/metode-pembayaran/edit/{{ $data['uuid'] }}" class="btn btn-warning btn-edit m-1" style="width: fit-content; height: fit-content; padding: 12px;">
                                                    <img src="{{ asset($tPath.'assets2/icon/edit.svg') }}" alt="">
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
    $modalDelete = 'metode-pembayaran';
    $modalTitle = 'Konfirmasi Hapus Metode Pembayaran';
    $modalMessage = 'Apakah Anda yakin ingin menghapus metode pembayaran ini? Tindakan ini tidak dapat dibatalkan.';
    $confirmText = 'Hapus Metode Pembayaran';
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
                id_metode_pembayaran: inpID.value.trim(),
            };
            xhr.open("DELETE", "/metode-pembayaran/delete");
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
    <script src="{{ asset($tPath.'assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
</body>
</html>