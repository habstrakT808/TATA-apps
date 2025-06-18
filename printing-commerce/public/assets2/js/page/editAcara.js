const editForm = document.getElementById("editForm");
const inpNamaEvent = document.getElementById("inpNamaEvent");
const inpTanggalAwal = document.getElementById("tanggal_awal");
const inpTanggalAkhir = document.getElementById("tanggal_akhir");
const inpTempat = document.getElementById("inpTempat");
const inpDeskripsi = document.getElementById("inpDeskripsi");
var bTanggalAwal = currentDate;
var bTanggalAkhir = currentDate;
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}
function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}
inpTanggalAwal.onchange = function(){
    const tanggal_awal = new Date(inpTanggalAwal.value.trim());
    const tanggal_akhir = new Date(inpTanggalAkhir.value.trim());
    if (tanggal_awal < currentDate) {
        showRedPopup("Tanggal awal tidak boleh kurang dari tanggal sekarang !");
        inpTanggalAwal.value = bTanggalAwal.toISOString().split('T')[0];
        return;
    }
    bTanggalAwal = tanggal_awal;
    if (tanggal_awal > tanggal_akhir) {
        inpTanggalAkhir.value = tanggal_awal.toISOString().split('T')[0];
        return;
    }
}
inpTanggalAkhir.onchange = function () {
    const tanggal_awal = new Date(inpTanggalAwal.value.trim());
    const tanggal_akhir = new Date(inpTanggalAkhir.value.trim());
    if (tanggal_akhir < currentDate) {
        showRedPopup("Tanggal akhir tidak boleh kurang dari tanggal sekarang !");
        inpTanggalAkhir.value = bTanggalAkhir.toISOString().split('T')[0];
        return;
    }
    bTanggalAkhir = tanggal_akhir;
    if (tanggal_akhir < tanggal_awal) {
        inpTanggalAwal.value = tanggal_akhir.toISOString().split('T')[0];
        return;
    }
}
editForm.onsubmit = function (event) {
    event.preventDefault();
    const nama_event = inpNamaEvent.value.trim();
    const tanggal_awal = inpTanggalAwal.value.trim();
    const tanggal_akhir = inpTanggalAkhir.value.trim();
    const tempat = inpTempat.value.trim();
    const deskripsi = inpDeskripsi.value.trim();
    if (nama_event === data.nama_event && tanggal_awal === data.tanggal_awal && tanggal_akhir === data.tanggal_akhir && tempat === data.tempat && deskripsi === data.deskripsi) {
        showRedPopup('Data belum diubah');
        return;
    }
    // if (judul === data.judul && linkVideo === data.link_video && deskripsi === data.deskripsi && uploadeFile === null) {
    //     showRedPopup('Data belum diubah');
    //     return;
    // }
    if (nama_event === "") {
        showRedPopup("Nama Acara harus diisi !");
        return;
    }
    if (tanggal_awal === "") {
        showRedPopup("Tanggal awal harus diisi !");
        return;
    }
    if (tanggal_akhir === "") {
        showRedPopup("Tanggal akhir harus diisi !");
        return;
    }
    if (new Date(tanggal_awal) > new Date(tanggal_akhir)) {
        showRedPopup("Tanggal awal tidak boleh lebih besar dari tanggal akhir !");
        return;
    }
    if (new Date(tanggal_akhir) < new Date(tanggal_awal)) {
        showRedPopup("Tanggal akhir tidak boleh kurang dari tanggal awal !");
        return;
    }
    if (tempat === "") {
        showRedPopup("Tempat Acara harus diisi !");
        return;
    }
    if (deskripsi === "") {
        showRedPopup("Deskripsi Acara harus diisi !");
        return;
    }
    showLoading();
    var xhr = new XMLHttpRequest();
    var requestBody = {
        uuid: uuid,
        nama_event: nama_event,
        tanggal_awal: tanggal_awal,
        tanggal_akhir: tanggal_akhir,
        tempat: tempat,
        deskripsi: deskripsi,
    };
    xhr.open("PUT", "/acara/update");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showGreenPopup(response);
                setTimeout(() => {
                    window.location.href = '/acara';
                }, 2000);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    };
    return false;
};