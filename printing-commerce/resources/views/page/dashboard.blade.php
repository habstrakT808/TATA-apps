<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <!-- CSS for full calender -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <style>
    body img{
        pointer-events: none;
    }
    a:hover{
        text-decoration: none;
    }
    </style>
</head>

<body style="user-select: none;">
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
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth);
    </script>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @php
        $nav = 'dashboard';
        @endphp
        @include('components.admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('components.admin.header')
            <!--  Header End -->
            <div class="container-fluid" style="background-color: #F6F9FF">
                <div class="pagetitle mt-2 mt-sm-3 mt-md-3 mt-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4 mb-2 mb-sm-3 mb-md-3 mb-lg-4">
                    <h1>Beranda</h1>
                </div>
                <main>
                    <div class="container d-flex justify-content-between gap-4">
                        <div class="card" style="width: 40%; flex-shrink: 0;">
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 100%;">
                                <div class="d-flex flex-column justify-content-center align-items-center gap-4">
                                    <div class="rounded-circle" style="background-color: #E7B500; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ asset($tPath.'assets2/icon/dashboard.png') }}" alt="">
                                    </div>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <p class="card-text">Total Projek Selesai</p>
                                        <h4 class="card-text">{{ $total_pesanan }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="flex: 1;">
                            <div class="card-body">
                                <h5 class="card-title">Ringkasan Penjualan</h5>
                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead class="text-dark fs-4">
                                            <tr>
                                                <th class="border-bottom-0">
                                                    <h6 class="fw-semibold mb-0">Pelanggan</h6>
                                                </th>
                                                <th class="border-bottom-0">
                                                    <h6 class="fw-semibold mb-0">Selesai Pada</h6>
                                                </th>
                                                <th class="border-bottom-0">
                                                    <h6 class="fw-semibold mb-0">Jenis Jasa</h6>
                                                </th>
                                                <th class="border-bottom-0">
                                                    <h6 class="fw-semibold mb-0">Pendapatan</h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($list_pesanan as $data)
                                            <tr>
                                                <td class="border-bottom-0">
                                                    <h6 class="fw-semibold mb-0">{{ $data['pelanggan'] }}</h6>
                                                </td>
                                                <td class="border-bottom-0">
                                                    <span class="fw-normal">{{ $data['selesai_pada'] }}
                                                    </span>
                                                </td>
                                                <td class="border-bottom-0">
                                                    <p class="mb-0 fw-normal">{{ $data['jenis_jasa']}}</p>
                                                </td>
                                                <td class="border-bottom-0">
                                                    <p class="mb-0 fw-normal">{{ $data['pendapatan']}}</p>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Ringkasan Penjualan</h5>
                                <div id="salesChart"></div>
                            </div>
                        </div>
                    </div>
                </main>
                @include('components.admin.footer')
            </div>
        </div>
    </div>
    @include('components.preloader')
    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- JS for full calender -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- bootstrap css and js -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
    </script>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/app.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Sales Chart
            var options = {
                series: [{
                    name: 'Pendapatan',
                    data: @json($monthly_sales)
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#FFB100'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    labels: {
                        style: {
                            colors: '#787878'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return 'Rp ' + (value/1000) + 'k';
                        },
                        style: {
                            colors: '#787878'
                        }
                    },
                },
                markers: {
                    size: 4,
                    colors: ["#FFB100"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                },
            };

            var chart = new ApexCharts(document.querySelector("#salesChart"), options);
            chart.render();
        });
    </script>
</body>

</html>