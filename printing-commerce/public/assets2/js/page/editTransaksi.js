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

function clearErrorMessage(elementId) {
    const errorElement = document.getElementById(elementId + '_error');
    if (errorElement) {
        errorElement.innerHTML = '';
        errorElement.style.display = 'none';
    }
}

function showErrorMessage(elementId, message) {
    let errorElement = document.getElementById(elementId + '_error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.id = elementId + '_error';
        errorElement.className = 'error-message';
        errorElement.style.color = 'red';
        errorElement.style.fontSize = '0.8rem';
        errorElement.style.marginTop = '5px';
        
        const inputElement = document.getElementById(elementId);
        if (inputElement && inputElement.parentNode) {
            inputElement.parentNode.appendChild(errorElement);
        }
    }
    
    errorElement.innerHTML = message;
    errorElement.style.display = 'block';
}

function validateForm() {
    let isValid = true;
    
    // Clear all error messages first
    clearErrorMessage('inpNama');
    clearErrorMessage('inpJenisKelamin');
    clearErrorMessage('inpNomerTelepon');
    clearErrorMessage('inpEmail');
    clearErrorMessage('inpStatus');
    clearErrorMessage('inpTanggal');
    
    const nama = inpNama.value.trim();
    const nomer = inpNomerTelepon.value.trim();
    const inp_jenis_kelamin = inpJenisKelamin.value.trim();
    const inpEmails = inpEmail.value.trim();
    const status = inpStatus.value.trim();
    const tanggal = inpTanggal.value.trim();
    
    // Check if any data has changed
    if (nama === users.nama_lengkap && 
        nomer === users.no_telpon && 
        inp_jenis_kelamin === users.jenis_kelamin && 
        inpEmails === users.email && 
        status === users.status && 
        tanggal === users.tanggal && 
        uploadeFile === null) {
        showRedPopup('Data belum diubah');
        return false;
    }
    
    // Validate name
    if(nama === "") {
        showErrorMessage('inpNama', "Nama Lengkap harus diisi!");
        isValid = false;
    }
    
    // Validate gender
    if(inp_jenis_kelamin === "") {
        showErrorMessage('inpJenisKelamin', "Jenis Kelamin harus diisi!");
        isValid = false;
    }
    
    // Validate phone number
    if(nomer === "") {
        showErrorMessage('inpNomerTelepon', "Nomor Telepon harus diisi!");
        isValid = false;
    } else if(isNaN(nomer)) {
        showErrorMessage('inpNomerTelepon', "Nomor Telepon harus angka!");
        isValid = false;
    } else if(!/^08\d+$/.test(nomer)) {
        showErrorMessage('inpNomerTelepon', "Nomor Telepon harus dimulai dengan 08!");
        isValid = false;
    } else if(!/^\d{11,13}$/.test(nomer)) {
        showErrorMessage('inpNomerTelepon', "Nomor Telepon harus terdiri dari 11-13 digit angka!");
        isValid = false;
    }
    
    // Validate email
    if(inpEmails === "") {
        showErrorMessage('inpEmail', "Email harus diisi!");
        isValid = false;
    } else if(!isValidEmail(inpEmails)) {
        showErrorMessage('inpEmail', "Format Email salah!");
        isValid = false;
    }
    
    // Validate status
    if(status === "") {
        showErrorMessage('inpStatus', "Status Transaksi harus diisi!");
        isValid = false;
    }
    
    // Validate date
    if(tanggal === "") {
        showErrorMessage('inpTanggal', "Tanggal Transaksi harus diisi!");
        isValid = false;
    }
    
    // Validate uploaded file if any
    if (uploadeFile && !allowedFormats.includes(uploadeFile.type)) {
        showRedPopup("Format Foto harus png, jpeg, jpg!");
        isValid = false;
    }
    
    return isValid;
}

editForm.onsubmit = function(event){
    event.preventDefault();
    
    if (!validateForm()) {
        return false;
    }
    
    const nama = inpNama.value.trim();
    const nomer = inpNomerTelepon.value.trim();
    const inp_jenis_kelamin = inpJenisKelamin.value.trim();
    const inpEmails = inpEmail.value.trim();
    const status = inpStatus.value.trim();
    const tanggal = inpTanggal.value.trim();
    
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
        closeLoading();
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showGreenPopup(response.message || "Transaksi berhasil diupdate");
                    setTimeout(() => {
                        window.location.href = '/transaksi';
                    }, 2000);
                } else {
                    if (response.errors) {
                        // Handle validation errors from backend
                        for (const [key, messages] of Object.entries(response.errors)) {
                            if (messages && messages.length > 0) {
                                let fieldId;
                                switch(key) {
                                    case 'nama_lengkap': fieldId = 'inpNama'; break;
                                    case 'jenis_kelamin': fieldId = 'inpJenisKelamin'; break;
                                    case 'no_telpon': fieldId = 'inpNomerTelepon'; break;
                                    case 'email': fieldId = 'inpEmail'; break;
                                    case 'status': fieldId = 'inpStatus'; break;
                                    case 'tanggal': fieldId = 'inpTanggal'; break;
                                    case 'bukti_pembayaran': 
                                        showRedPopup(messages[0]);
                                        continue;
                                    default: continue;
                                }
                                showErrorMessage(fieldId, messages[0]);
                            }
                        }
                    } else {
                        showRedPopup(response.message || "Terjadi kesalahan");
                    }
                }
            } catch (e) {
                showRedPopup("Error parsing response from server");
            }
        } else {
            try {
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response.message || "Terjadi kesalahan pada server");
            } catch (e) {
                showRedPopup("Error occurred during the request");
            }
        }
    };
    xhr.onerror = function () {
        closeLoading();
        showRedPopup("Error connecting to server");
    };
    xhr.send(formData);
    return false;
}; 