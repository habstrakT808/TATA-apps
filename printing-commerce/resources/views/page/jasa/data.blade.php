<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jasa | TATA</title>
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
    .btn-delete,
    .btn-detail{
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
    .btn-delete img,
    .btn-detail img{
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
        .btn-edit,
        .btn-delete,
        .btn-detail{
            width: 90px;
            height: 40px;
            font-size: 16px;
        }
        .btn-edit img,
        .btn-delete img,
        .btn-detail img{
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
        .btn-delete,
        .btn-detail{
            width: 90px;
            height: 40px;
            font-size: 15px;
        }
        .btn-edit img,
        .btn-delete img,
        .btn-detail img{
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
        .btn-delete,
        .btn-detail{
            width: 80px;
            height: 37px;
            font-size: 14px;
        }
        .btn-edit img,
        .btn-delete img,
        .btn-detail img{
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
            <div class="container-fluid" style="background-color: #F6F9FF">
                <div class="pagetitle mt-2 mt-sm-3 mt-md-3 mt-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4">
                    <h1>Kelola Jasa</h1>
                </div>
                <div class="d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            <div class="alert alert-info mb-3">
                                <strong>Info!</strong> Aplikasi ini hanya mendukung 3 jasa utama: Desain Logo, Desain Poster, dan Desain Banner.
                            </div>
                            <div class="d-flex justify-content-end mb-3">
                                <a href="/jasa/tambah" class="btn btn-success" id="btnTambah" style="background-color: #00C4FF; border-color: #00C4FF;">
                                    <img src="{{ asset($tPath.'assets2/icon/plus.svg') }}" alt="">
                                    Tambah Jasa
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
                                                <h6 class="fw-semibold mb-0">Nama Jasa</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Aksi</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($jasaData as $data)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">{{ $no++ }}</h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <span class="fw-normal">{{ $data['display_name'] }}
                                                </span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <a href="/jasa/edit/{{ $data['uuid'] }}" class="btn btn-warning btn-edit m-1" style="width: fit-content; height: fit-content; padding: 12px;">
                                                    <img src="{{ asset($tPath.'assets2/icon/edit.svg') }}" alt="">
                                                </a>
                                                <button class="btn btn-danger btn-delete m-1" style="width: fit-content; height: fit-content; padding: 12px;" onclick="showModalDelete('{{ $data['uuid'] }}')">
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
    $modalDelete = 'jasa';
    @endphp
    @include('components.admin.modalDelete')
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
    <script src="{{ asset($tPath.'assets2/js/page/modalDelete.js') }}"></script>
</body>

</html>