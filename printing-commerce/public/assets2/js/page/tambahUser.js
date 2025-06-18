const tambahForm = document.getElementById("tambahForm");
const inpNama = document.getElementById("inpNama");
const inpJenisKelamin = document.getElementById("inpJenisKelamin");
const inpNomerTelepon = document.getElementById("inpNomerTelepon");
const inpEmail = document.getElementById("inpEmail");
const iconPass = document.getElementById("iconPass");
const inpPassword = document.getElementById("inpPassword");
const inpFoto = document.getElementById("inpFoto");
const allowedFormats = ["image/jpeg", "image/png"];
let uploadeFile = null;
var isPasswordShow = false;

// Utility Functions
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}

function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}
function showEyePass(){
    if(inpPassword.value == '' || inpPassword.value == null){
        iconPass.style.display = 'none';
    }else{
        iconPass.style.display = 'block';
    }
}
// Password Toggle Functions
function showPass(){
    if(isPasswordShow){
        inpPassword.type = 'password';
        document.getElementById('passClose').style.display = 'block';
        document.getElementById('passShow').style.display = 'none';
        isPasswordShow = false;
    }else{
        inpPassword.type = 'text';
        document.getElementById('passClose').style.display = 'none';
        document.getElementById('passShow').style.display = 'block';
        isPasswordShow = true;
    }
}

// File Handling Functions
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
            document.querySelector('.dropzone-container').style.border = 'none';
            document.querySelector('.dropzone-container img#icon').style.display = 'none';
            document.querySelector('.dropzone-container p').style.display = 'none';
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
            document.querySelector('.dropzone-container').style.border = 'none';
            document.querySelector('.dropzone-container img#icon').style.display = 'none';
            document.querySelector('.dropzone-container p').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Validation Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    if (password === '') {
        showRedPopup('Password harus diisi !');
        return false;
    }
    if (password.length < 8) {
        showRedPopup('Password minimal 8 karakter !');
        return false;
    }
    if (!/[A-Z]/.test(password)) {
        showRedPopup('Password minimal ada 1 huruf kapital !');
        return false;
    }
    if (!/[a-z]/.test(password)) {
        showRedPopup('Password minimal ada 1 huruf kecil !');
        return false;
    }
    if (!/\d/.test(password)) {
        showRedPopup('Password minimal ada 1 angka !');
        return false;
    }
    if (!/[!@#$%^&*]/.test(password)) {
        showRedPopup('Password minimal ada 1 karakter unik !');
        return false;
    }
    return true;
}

// Form Submission
tambahForm.onsubmit = function(event){
    event.preventDefault();
    
    // Get form values
    const nama = inpNama.value.trim();
    const inp_jenis_kelamin = inpJenisKelamin.value.trim();
    const nomer = inpNomerTelepon.value.trim();
    const inpEmails = inpEmail.value.trim();
    const password = inpPassword.value.trim();

    // Validate required fields
    if(nama === "") {
        showRedPopup("Nama Lengkap harus diisi !");
        return;
    }

    if(inp_jenis_kelamin === "") {
        showRedPopup("Jenis Kelamin harus diisi !");
        return;
    }

    // Validate phone number
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

    // Validate email
    if(inpEmails === "") {
        showRedPopup("Email harus diisi !");
        return;
    }
    if(!isValidEmail(inpEmails)) {
        showRedPopup('Format Email salah !');
        return;
    }

    // Validate password
    if (!validatePassword(password)) {
        return;
    }

    // Validate file if uploaded
    if (uploadeFile && !allowedFormats.includes(uploadeFile.type)) {
        showRedPopup("Format Foto harus png, jpeg, jpg !");
        return;
    }

    // Prepare form data
    showLoading();
    const formData = new FormData();
    formData.append("nama_lengkap", nama);
    formData.append("jenis_kelamin", inp_jenis_kelamin);
    formData.append("no_telpon", nomer);
    formData.append("email", inpEmails);
    formData.append("password", password);
    
    if (uploadeFile) {
        formData.append("foto", uploadeFile);
    }

    // Send request
    fetch('/user/create', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        closeLoading();
        if (data.status === 'success'){
            showGreenPopup(data.message || "User berhasil ditambahkan!");
            // setTimeout(() => {
            //     window.location.href = '/user';
            // }, 2000);
        } else {
            showRedPopup(data.message || "Gagal menambahkan user!");
        }
    })
    .catch(error => {
        closeLoading();
        console.error('Error:', error);
        showRedPopup("Terjadi kesalahan saat mengirim data.");
    });

    return false;
}; 