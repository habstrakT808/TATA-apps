const editForm = document.getElementById("editForm");
const inpNama = document.getElementById("inpNama");
const inpJenisKelamin = document.getElementById("inpJenisKelamin");
const inpNomerTelepon = document.getElementById("inpNomerTelepon");
const inpEmail = document.getElementById("inpEmail");
const inpStatus = document.getElementById("inpStatus");
const inpTanggal = document.getElementById("inpTanggal");
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
            document.getElementById('icon').style.display = 'none';
            document.querySelector('span').style.display = 'none';
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
            document.getElementById('icon').style.display = 'none';
            document.querySelector('span').style.display = 'none';
            document.querySelector('div.img').style.border = 'none';
        };
        reader.readAsDataURL(file);
    }
}
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function imgError(id) {
    document.getElementById(id).style.display = 'none';
    document.getElementById('icon').style.display = 'block';
    document.querySelector('span').style.display = 'block';
    document.querySelector('div.img').style.border = '4px dashed #b1b1b1';
}

editForm.onsubmit = function(event){
    event.preventDefault();
    const nama = inpNama.value.trim();
    const nomer = inpNomerTelepon.value.trim();
    const inp_jenis_kelamin = inpJenisKelamin.value.trim();
    const inpEmails = inpEmail.value.trim();
    const status = inpStatus.value.trim();
    const tanggal = inpTanggal.value.trim();
    
    if (nama === users.nama_lengkap && 
        nomer === users.no_telpon && 
        inp_jenis_kelamin === users.jenis_kelamin && 
        inpEmails === users.email && 
        status === users.status && 
        tanggal === users.tanggal && 
        uploadeFile === null) {
        showRedPopup('Data belum diubah');
        return;
    }
    
    if(nama === "") {  
        showRedPopup("Nama Lengkap harus diisi !");
        return;
    }
    if(inp_jenis_kelamin === "") {
        showRedPopup("Jenis Kelamin harus diisi !");
        return;
    }
    if(nomer === "") {
        showRedPopup("Nomer Telepon harus diisi !");
        return;
    }else if(isNaN(nomer)) {
        showRedPopup("Nomer Telepon harus angka !");
        return;
    }else if(!/^08\d+$/.test(nomer)) {
        showRedPopup("Nomer Telepon harus dimulai dengan 08 !");
        return;
    }else if(!/^\d{11,13}$/.test(nomer)) {
        showRedPopup("Nomer Telepon harus terdiri dari 11-13 digit angka !");
        return;
    }
    if(inpEmails === "") {
        showRedPopup("Email harus diisi !");
        return;
    }
    if(!isValidEmail(inpEmails)) {
        showRedPopup('Format Email salah !');
        return;
    }
    if(status === "") {
        showRedPopup("Status Transaksi harus diisi !");
        return;
    }
    if(tanggal === "") {
        showRedPopup("Tanggal Transaksi harus diisi !");
        return;
    }
    if (uploadeFile) {
        if (!allowedFormats.includes(uploadeFile.type)) {
            showRedPopup("Format Foto harus png, jpeg, jpg !");
            return;
        }
    }
    
    showLoading();
    const formData = new FormData();
    formData.append("_method", 'PUT');
    formData.append("nama_lengkap", nama);
    formData.append("jenis_kelamin", inp_jenis_kelamin);
    formData.append("no_telpon", nomer);
    formData.append("email", inpEmails);
    formData.append("status", status);
    formData.append("tanggal", tanggal);
    formData.append("id_transaksi", users.id);
    
    if (uploadeFile) {
        formData.append("bukti_pembayaran", uploadeFile);
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/transaksi/update");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.onload = function () {
        if (xhr.status === 200) {
            closeLoading();
            var response = JSON.parse(xhr.responseText);
            showGreenPopup(response);
            setTimeout(() => {
                window.location.href = '/transaksi';
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