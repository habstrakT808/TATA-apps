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
            background-color: #38AD5E;
        }
        
        .welcome-section {
            text-align: center;
            padding: 0 20px;
        }
        
        .welcome-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .welcome-section p {
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            width: 20px;
            height: 20px;
        }
        
        .login-form-container {
            width: 70%;
            max-width: 400px;
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
        }
        
        .login-input {
            height: 50px;
            padding-left: 50px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
        
        .login-button {
            background-color: #4D82F3;
            border: none;
            height: 50px;
            font-size: 16px;
            font-weight: 500;
            width: 120px;
            margin: 15px auto;
            display: block;
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
            
            .login-form-container {
                width: 85%;
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
            
            .login-form-container {
                width: 90%;
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
            <main class="d-flex bg-white rounded-4 shadow" style="width: 80%; min-height: 70vh; max-height: 80vh;">
                <!-- Left side - Green section -->
                <div class="position-relative" style="width: 40%; min-height: 100%; overflow: hidden;">
                    <div id="login-content" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
                    
                    <!-- Logo section -->
                    <div id="logo-content" class="position-absolute" style="bottom: 60%; left: 50%; transform: translate(-50%, 0); width: 100%; z-index: 2; text-align: center;">
                        <img src="{{ asset($tPath.'assets2/img/logo.png') }}" alt="Tata Logo" class="img-fluid" style="max-width: 220px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    
                    <!-- Welcome section -->
                    <div class="position-absolute welcome-section" style="bottom: 40%; left: 50%; transform: translate(-50%, 0); z-index: 2;">
                        <h2 class="text-white">Selamat Datang!</h2>
                        <p class="text-white">Masukkan User anda dan Password untuk akses</p>
                        <p class="text-white mt-4">Solusi Cerdas Design Cepat</p>
                    </div>
                </div>
                
                <!-- Right side - Login form -->
                <div class="d-flex align-items-center justify-content-center" style="width: 60%;">
                    <div class="login-form-container">
                        <h3 class="login-title">Login Admin</h3>
                        <form action="" id="loginForm">
                            <!-- Email input -->
                            <div class="position-relative mb-4">
                                <img src="{{ asset($tPath.'assets2/icon/login/inpEmail.png') }}" alt="" class="input-icon">
                                <input type="text" id="inpEmail" class="form-control login-input" required placeholder="Email">
                                </div>
                            
                            <!-- Password input -->
                            <div class="position-relative mb-4">
                                <img src="{{ asset($tPath.'assets2/icon/login/inpPassword.png') }}" alt="" class="input-icon">
                                <input id="inpPassword" type="password" class="form-control login-input" oninput="showEyePass()" required placeholder="Password">
                                <div id="iconPass" onclick="showPass()" style="display: none; position: absolute; top: 50%; transform: translateY(-50%); right: 15px;">
                                        <img src="{{ asset($tPath.'assets2/icon/eye-slash.svg') }}" alt="" id="passClose">
                                        <img src="{{ asset($tPath.'assets2/icon/eye.svg') }}" alt="" id="passShow" style="display: none">
                                </div>
                            </div>
                            
                            <!-- Login button -->
                            <input type="submit" class="btn btn-primary login-button rounded-3" value="Login">
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
        });
    </script>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/popup.js') }}"></script>
    <script src="{{ asset($tPath.'assets2/js/page/login.js') }}"></script>
</body>
</html>