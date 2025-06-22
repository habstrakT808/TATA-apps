{{-- <script>
    var errFotos = [];
    function imgError(err) {
        errFotos.push(err);
        var image = document.getElementById(err);
        if (image && image.src !== "{{ route('download.foto.default') }}") {
            image.src = "{{ route('download.foto.default') }}";
        }
    }
</script> --}}
<header class="app-header" style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5);">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <!-- Notifikasi pesanan masuk - hanya untuk Admin Pesanan -->
                @if($userAuth['role'] == 'admin_pesanan')
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover rounded-pill" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false" style="padding-top: 4px; padding-bottom: 4px; padding-left: 12px; padding-right: 12px;">
                        <img src="{{ asset($tPath.'assets2/icon/header/notification.png') }}" alt="" style="width: 42px; height: 42px;">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2" style="width: 70vw;">
                        <div class="message-body">
                            <h5 class="py-2 px-3 border-bottom">Notifikasi Pesanan Masuk</h5>
                            @if(count($headerData) > 0)
                                <ul class="row gap-3 px-3" style="height: 300px; overflow-y: auto;">
                                    @foreach ($headerData as $pesanan)
                                        <li class="col-12 d-flex align-items-center gap-3 p-3 border-bottom">
                                            {{-- <img src="{{ asset($tPath.'assets3/img/pesanan'.$pesanan->toJasa->foto_jasa) }}" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;"> --}}
                                            <img src="{{ asset($tPath.'assets3/img/pesanan/1.jpg') }}" alt="Profile" class="rounded-circle" style="width: 35px; height: 35px;">
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">{{ $pesanan->toUser->nama_user ?? 'User' }}</h5>
                                                    <small class="text-muted">{{ $pesanan->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-1">{{ $pesanan->toJasa->nama_jasa ?? 'Jasa Design' }} | {{ $pesanan->toJasa->kategori ?? 'Basic' }}</p>
                                                <div style="max-width: 90%;">
                                                    <p class="mb-0">{{ $pesanan->deskripsi }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-4">
                                    <p>Tidak ada pesanan masuk baru</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
                @endif
                
                <!-- Tombol chat - hanya untuk Admin Chat -->
                @if($userAuth['role'] == 'admin_chat')
                <a class="nav-link" href="/chat" class="btn btn-outline-primary mx-3 mt-2 d-block">
                    <div class="rounded-pill">
                        <img src="{{ asset($tPath.'assets2/icon/header/chat.png') }}" alt="" style="width: 42px; height: 42px;">
                    </div>
                </a>
                @endif
            </ul>
        </div>
    </nav>
</header>
{{-- <script>
    window.addEventListener('load', function() {
        var imgs = document.querySelectorAll('.foto_admin');
        imgs.forEach(function(image) {
            if (errFotos.includes(image.id)) {
                image.src = "{{ route('download.foto.default') }}";
            }
            if (image.complete && image.naturalWidth === 0) {
                image.src = "{{ route('download.foto.default') }}";
            }
        });
    });
</script> --}}