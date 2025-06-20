<?php
?>
<aside class="left-sidebar sidebar-dark-primary col-auto" style="background-color: #38AD5E; height: 100vh;">
    <!-- Sidebar scroll-->
    <div class="brand-logo d-flex align-items-center justify-content-center py-3">
        <a href="/dashboard" class="text-nowrap logo-img w-100 text-center position-relative">
            <img src="{{ asset($tPath.'assets2/img/logo.png') }}" alt="" class="img-fluid" style="max-width: 100%; width: 180px; height: 100px; left: 50%; margin-right: auto">
            <div class="logo-tagline-container" style="position: absolute; bottom: -30px; left: 0; width: 100%; padding-bottom: 15px;">
                <div class="logo-tagline text-white text-decoration-none text-center mx-auto" style="font-size: 14px; font-weight:600; width: 90%; word-wrap: break-word; word-break: break-word; line-height: 1.3; min-height: fit-content;">Solusi Cerdas Design Cepat</div>
            </div>
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>
    <!-- End Sidebar scroll-->
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar mt-4" data-simplebar="" style="height: calc(100% - 180px);">
        <ul id="sidebarnav" class="nav flex-column gap-1 p-0">
            <li class="nav-item sidebar-item {{ $nav == 'dashboard' ? 'selected' : ''}}">
                <a class="nav-link sidebar-link {{ $nav == 'dashboard' ? 'active' : ''}} d-flex align-items-center py-2" href="/dashboard"
                    aria-expanded="false">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/dashboard.svg') }}" alt="Dashboard" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item sidebar-item {{ $nav == 'jasa' ? 'selected' : ''}}">
                <a class="nav-link sidebar-link {{ $nav == 'jasa' ? 'active' : ''}} d-flex align-items-center py-2" href="/jasa" aria-expanded="false">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/jasa.svg') }}" alt="Jasa" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Kelola Jasa</span>
                </a>
            </li>
            
            <li class="nav-item sidebar-item {{ $nav == 'pesanan' ? 'selected' : ''}}">
                <a class="nav-link sidebar-link {{ $nav == 'pesanan' ? 'active' : ''}} d-flex align-items-center py-2" href="/pesanan"
                    aria-expanded="false">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/pesanan.svg') }}" alt="Pesanan" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Kelola Pesanan</span>
                </a>
            </li>

            <li class="nav-item sidebar-item {{ $nav == 'metode-pembayaran' ? 'selected' : ''}}">
                <a class="nav-link sidebar-link {{ $nav == 'metode-pembayaran' ? 'active' : ''}} d-flex align-items-center py-2" href="/payment-methods"
                    aria-expanded="false">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/metode-pembayaran.svg') }}" alt="Metode Pembayaran" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Metode Pembayaran</span>
                </a>
            </li>

            <li class="nav-item sidebar-item {{ $nav == 'user-management' ? 'selected' : ''}}">
                <a class="nav-link sidebar-link {{ $nav == 'user-management' ? 'active' : ''}} d-flex align-items-center py-2" href="/user-management"
                    aria-expanded="false">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/user.svg') }}" alt="User" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Kelola User</span>
                </a>
            </li>
            
            <!-- Logout button moved here, inside the navigation menu -->
            <li class="nav-item sidebar-item mt-3">
                <button class="nav-link sidebar-link d-flex align-items-center py-2 w-100" onclick="logout()" 
                    style="outline: none; border: none; background-color: transparent; text-align: left;">
                    <span class="icon-holder me-2">
                        <img src="{{ asset($tPath.'assets2/icon/sidebar/logout.svg') }}" alt="Logout" class="white img-fluid" style="width: 26px; height: 26px;">
                    </span>
                    <span class="hide-menu text-white">Logout</span>
                </button>
            </li>
        </ul>
        <!-- End Sidebar navigation -->
    </nav>
</aside>

<style>
/* Responsive sidebar with consistent text size */
.hide-menu {
    font-size: 14px;
}

.logo-tagline {
    font-size: 14px;
    height: auto;
}

.logo-tagline-container {
    height: auto;
}

@media (max-width: 1199.98px) {
    .left-sidebar {
        width: 250px;
    }
    .hide-menu {
        font-size: 14px;
    }
    .icon-holder img {
        width: 22px !important;
        height: 22px !important;
    }
    .logo-tagline {
        font-size: 13px;
    }
}

@media (max-width: 767.98px) {
    .left-sidebar {
        width: 220px;
    }
    .hide-menu {
        font-size: 13px;
    }
    .icon-holder img {
        width: 20px !important;
        height: 20px !important;
    }
    .logo-tagline {
        font-size: 12px;
    }
}

@media (max-width: 575.98px) {
    .left-sidebar {
        width: 190px;
    }
    .hide-menu {
        font-size: 12px;
    }
    .icon-holder img {
        width: 18px !important;
        height: 18px !important;
    }
    .logo-tagline {
        font-size: 11px;
    }
}
</style>

<script>
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(function(item){
        if(!item.classList.contains('selected')){
            item.addEventListener('click', function(){
                item.querySelector('.dark').style.display = 'none';
                item.querySelector('.white').style.display = 'block';
                sidebarItems.forEach(function(itemActive){
                    if(itemActive.classList.contains('selected')){
                        itemActive.querySelector('.dark').style.display = 'block';
                        itemActive.querySelector('.white').style.display = 'none';
                        itemActive.classList.remove('selected');
                    }
                });
                item.classList.add('selected');
            });
        }
    });
    function logout(){
        var xhr = new XMLHttpRequest();
        //open the request
        xhr.open('POST', "/admin/logout");
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Content-Type', 'application/json');
        //send the form data
        xhr.send(JSON.stringify({}));
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    // Redirect to login page after successful logout
                    window.location.href = '/login';
                } else {
                    console.error('Logout failed');
                }
            }
        }
    }
</script>