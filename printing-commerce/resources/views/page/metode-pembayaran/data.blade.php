<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/page/modalDelete.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
    }
    .card-body {
        padding: 30px;
    }
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table thead {
        background-color: #4CAF50;
        color: white;
        border-radius: 8px 8px 0 0;
    }
    .table thead th {
        padding: 15px;
        font-weight: 600;
        border: none;
    }
    .table thead th:first-child {
        border-radius: 8px 0 0 0;
    }
    .table thead th:last-child {
        border-radius: 0 8px 0 0;
    }
    .table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }
    .table tbody tr:last-child {
        border-bottom: none;
    }
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }
    #btnTambah {
        background-color: #4CAF50;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        height: 50px;
    }
    #btnTambah img {
        width: 24px;
        height: 24px;
    }
    .btn-edit {
        background-color: #FFC107;
        color: white;
        border-radius: 6px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }
    .btn-delete {
        background-color: #FF5252;
        color: white;
        border-radius: 6px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }
    .btn-edit img, .btn-delete img {
        width: 16px;
        height: 16px;
    }
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }
    .pagetitle h1 {
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }
    /* Add styling for the Aksi column to make it wider */
    .table th:last-child {
        width: 220px;
        text-align: right;
    }
    .table td:last-child {
        text-align: right;
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
                <div class="pagetitle mt-4 mb-4">
                    <h1>Metode Pembayaran</h1>
                </div>
                <div class="d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Rekening</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($metodePembayaranData as $data)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>
                                                <a href="/payment-methods/detail/{{ $data['uuid'] }}" class="text-decoration-none text-dark">
                                                    {{ $data['nama_metode_pembayaran'] }}
                                                </a>
                                            </td>
                                            <td>Rekening ini digunakan untuk pembayaran</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/payment-methods/edit/{{ $data['uuid'] }}" class="btn btn-edit">
                                                        <img src="{{ asset($tPath.'assets2/icon/edit.svg') }}" alt="Edit">
                                                        <span class="ms-1">Edit</span>
                                                    </a>
                                                    <button type="button" class="btn btn-delete" onclick="showModalDelete('{{ $data['uuid'] }}')">
                                                        <img src="{{ asset($tPath.'assets2/icon/delete.svg') }}" alt="Delete">
                                                        <span class="ms-1">Delete</span>
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
    $modalDelete = 'payment-methods';
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
            xhr.open("DELETE", "/payment-methods/delete");
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