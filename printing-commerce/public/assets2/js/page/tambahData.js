const tambahForm = document.getElementById("tambahForm");
const inpJudul = document.getElementById("inpJudul");
const inpKategori = document.getElementById("inpKategori");
const inpRentangUsia = document.getElementById("inpRentangUsia");
const inpLinkVideo = document.getElementById("inpLinkVideo");
const inpDeskripsi = document.getElementById("inpDeskripsi");
const inpFoto = document.getElementById("inpFoto");
const allowedFormats = ["image/jpeg", "image/png"];
let uploadeFile = null;
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}
function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}
function handleFileClick() {
    inpFoto.click();
}
function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        if (!allowedFormats.includes(file.type)) {
            showRedPopup("Format Foto harus png, jpeg, jpg !");
            return;
        }
        uploadeFile = file;
        const fileReader = new FileReader();
        fileReader.onload = function() {
            document.getElementById('file').src = fileReader.result;
            document.getElementById('file').style.display = 'block';
            document.querySelector('div.img').style.border = 'none';
        };
        fileReader.readAsDataURL(uploadeFile);
    }
}
function handleDragOver(event) {
    event.preventDefault();
}
function handleDrop(event) {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    if (file) {
        if (!allowedFormats.includes(file.type)) {
            showRedPopup("Format Foto harus png, jpeg, jpg !");
            return;
        }
        uploadeFile = file;
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('file').src = event.target.result;
            document.getElementById('file').style.display = 'block';
            document.querySelector('div.img').style.border = 'none';
        };
        reader.readAsDataURL(file);
        fileImg = file;
    }
}
tambahForm.onsubmit = function (event) {
    event.preventDefault();
    const judul = inpJudul.value.trim();
    const linkVideo = inpLinkVideo.value.trim();
    const deskripsi = inpDeskripsi.value.trim();
    if (judul === "") {
        showRedPopup("Judul harus diisi !");
        return;
    }
    if (deskripsi === "") {
        showRedPopup("Deskripsi harus diisi !");
        return;
    }
    if (!uploadeFile) {
        showRedPopup("Foto harus dipilih !");
        return;
    }
    if (!allowedFormats.includes(uploadeFile.type)) {
        showRedPopup("Format Foto harus png, jpeg, jpg !");
        return;
    }
    const formData = new FormData();
    formData.append("judul", judul);
    if (reff == "/article") {
        const kategori = inpKategori.value.trim();
        if (kategori === "") {
            showRedPopup("Kategori harus diisi !");
            return;
        }
        formData.append("kategori", kategori);
    } else {
        const rentangUsia = inpRentangUsia.value.trim();
        if (rentangUsia === "") {
            showRedPopup("Rentang Usia harus diisi !");
            return;
        }
        formData.append("rentang_usia", rentangUsia);
    }
    showLoading();
    formData.append("link_video", linkVideo);
    formData.append("deskripsi", deskripsi);
    formData.append("foto", uploadeFile);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", reff + "/tambah");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.onload = function () {
        if (xhr.status === 200) {
            closeLoading();
            var response = JSON.parse(xhr.responseText);
            showGreenPopup(response);
            setTimeout(() => {
                window.location.href = reff;
            }, 2000);
        } else {
            closeLoading();
            var response = JSON.parse(xhr.responseText);
            showRedPopup(response);
        }
    };
    xhr.onerror = function () {
        closeLoading();
        showRedPopup("Error occurred during the request.");
    };
    xhr.send(formData);
    return false;
};
