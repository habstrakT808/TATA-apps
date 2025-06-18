<header>
    <h1>TATA</h1>
    <li>
        @if($nav === 'home')
            <a href="#beranda">Beranda</a>
            <a href="#artikel">Artikel Terbaru</a>
        @elseif($nav === 'detail')
            <a href="/#beranda">Beranda</a>
            <a href="#artikel">Artikel</a>
            <a href="#video">video</a>
            <a href="#rekomendasi">Rekomendasi Artikel</a>
        @else
            <a href="/#beranda">Beranda</a>
            <a href="/#artikel">Artikel Terbaru</a>
        @endif
    </li>
</header>