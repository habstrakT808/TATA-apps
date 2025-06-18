<?php 
if(app()->environment('local')){
    $tPath = '';
}else{
    $tPath = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicons -->
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="apple-touch-icon">
    <title>Login | TATA</title>
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}">
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset($tPath.'assets2/css/preloader.css') }}" />
    <link href="{{ asset($tPath.'assets2/css/page/login.css') }}" rel="stylesheet">
    <style>
        html{
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Poppins', sans-serif;
            user-select: none;
            background-color: #CCCCCC;
        }
        body img{
            pointer-events: none;
        }
        
        /* Enhanced SVG positioning */
        #login-content {
            border-radius: 20px 0 0 20px;
            overflow: hidden !important;
        }
        
        /* Responsive fixes - maintain horizontal layout */
        @media (max-width: 768px) {
            main {
                width: 95% !important;
                /* Keep horizontal layout - NO flex-direction: column */
            }
            
            main > div:first-child {
                width: 35% !important; /* Slightly smaller green section */
            }
            
            main > div:last-child {
                width: 65% !important; /* More space for form */
            }
            
            main > div:last-child > div {
                width: 80% !important; /* Wider form container */
                left: 50% !important;
            }
        }
        
        @media (max-width: 576px) {
            main {
                width: 98% !important;
                border-radius: 15px !important;
            }
            
            main > div:first-child {
                width: 30% !important; /* Even smaller green section */
            }
            
            main > div:last-child {
                width: 70% !important;
            }
            
            main > div:last-child > div {
                width: 90% !important;
                left: 55% !important;
            }
            
            #login-content {
                border-radius: 15px 0 0 15px;
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
    var csrfToken = "{{ csrf_token() }}";
    @if(isset($logout))
    var logoutt = "{{$logout}}";    
    showPopup(logoutt);
    @endif
    </script>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <main class="d-flex bg-white rounded-5" style="width: 80%; min-height: 70vh; max-height: 80vh;">
                <div class="position-relative" style="width: 40%; min-height: 100%; overflow: hidden;">
                    <div id="login-content" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
                    <div id="logo-content" class="position-absolute z-10 d-flex justify-content-center align-items-center flex-column-reverse" style="top: 20%; left: 50%; transform: translate(-50%, -50%);">
                        <p class="text-white">Solusi Cerdas Design Cepat</p>
                    </div>
                    <div class="position-absolute z-10" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <h2 class="text-white fs-5">Selamat Datang!</h2>
                        <p class="text-white">Masukkan User anda dan Password untuk akses</p>
                    </div>
                </div>
                <div class="" style="width: 60%;">
                    <div class="position-relative" style="width: 50%; top: 50%; left: 60%; transform: translate(-50%, -50%);">
                        <h3>Login Admin</h3>
                        <form action="" id="loginForm">
                            <div class="row">
                                <div class="position-relative col-12 mb-3">
                                    <input type="text" id="inpEmail" class="form-control rounded-3" style="padding-left: 45px;" required placeholder="Email">
                                    <img src="{{ asset($tPath.'assets2/icon/login/inpEmail.png') }}" alt="" style="position: absolute; top: 50%; transform: translateY(-50%); left: 10px;">
                                </div>
                                <div class="position-relative col-12 mb-4">
                                    <input id="inpPassword" type="password" class="form-control rounded-3" style="padding-left: 45px; padding-right: 45px;" oninput="showEyePass()" required placeholder="Password">
                                    <img src="{{ asset($tPath.'assets2/icon/login/inpPassword.png') }}" alt="" style="position: absolute; top: 50%; transform: translateY(-50%); left: 10px;">
                                    <div id="iconPass" onclick="showPass()" style="display: none;">
                                        <img src="{{ asset($tPath.'assets2/icon/eye-slash.svg') }}" alt="" id="passClose">
                                        <img src="{{ asset($tPath.'assets2/icon/eye.svg') }}" alt="" id="passShow" style="display: none">
                                    </div>
                                </div>
                                <input type="submit" class="position-relative btn btn-primary py-8 fs-4 mb-4 rounded-3" style="width: 40%; left: 50%; transform: translate(-50%, 0);" value="Login">
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('components.preloader')
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script>
        function loadSVG(svgFile, targetElement){
            const xhr = new XMLHttpRequest();
            xhr.open('GET', svgFile, true);
            xhr.onload = function(){
                if (xhr.status === 200) {
                    // Keep original SVG shape with perfect scaling
                    let svgContent = xhr.responseText
                        .replace(/width="[^"]*"/, 'width="100%"')
                        .replace(/height="[^"]*"/, 'height="100%"')
                        .replace(/viewBox="[^"]*"/, 'viewBox="0 0 616 840" preserveAspectRatio="none"');
                    targetElement.innerHTML += svgContent;
                }
            };
            xhr.onerror = function(){
                console.error('Error loading SVG');
            };
            xhr.send();
        }
        document.addEventListener('DOMContentLoaded', function(){
            const loginContent = document.querySelector('#login-content');
            const loginPath = "{{ asset($tPath.'assets2/icon/login/login.svg') }}";
            if(loginContent){
                loadSVG(loginPath, loginContent);
            }
            const logoContent = document.querySelector('#logo-content');
            const logoPath = "{{ asset($tPath.'assets2/icon/logo.svg') }}";
            if(logoContent){
                loadSVG(logoPath, logoContent);
            }
        });
    </script>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/page/login.js') }}"></script>
</body>
</html>