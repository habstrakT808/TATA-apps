const editForm = document.getElementById("editForm");
const inpNama = document.getElementById("inpNama");
const inpJenisKelamin = document.getElementById("inpJenisKelamin");
const inpRole = document.getElementById("inpRole");
const inpNomerTelepon = document.getElementById("inpNomerTelepon");
const inpEmail = document.getElementById("inpEmail");
const iconPass = document.getElementById("iconPass");
const inpPassword = document.getElementById("inpPassword");
const inpFoto = document.getElementById("inpFoto");
const allowedFormats = ["image/jpeg", "image/png"];
let uploadeFile = null;
var isPasswordShow = false;
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}
function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}
function showEyePass() {
    if (inpPassword.value == "" || inpPassword.value == null) {
        iconPass.style.display = "none";
    } else {
        iconPass.style.display = "block";
    }
}
function showPass() {
    if (isPasswordShow) {
        inpPassword.type = "password";
        document.getElementById("passClose").style.display = "block";
        document.getElementById("passShow").style.display = "none";
        isPasswordShow = false;
    } else {
        inpPassword.type = "text";
        document.getElementById("passClose").style.display = "none";
        document.getElementById("passShow").style.display = "block";
        isPasswordShow = true;
    }
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
        fileReader.onload = function () {
            document.getElementById("file").src = fileReader.result;
            document.getElementById("file").style.display = "block";
            document.querySelector("div.img").style.border = "none";
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
        reader.onload = function (event) {
            document.getElementById("file").src = event.target.result;
        };
        reader.readAsDataURL(file);
        fileImg = file;
    }
}
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
function validatePassword(password) {
    if (password === "") {
        return true; // Password is optional in edit mode
    }
    if (password.length < 8) {
        showRedPopup("Password minimal 8 karakter !");
        return false;
    }
    if (!/[A-Z]/.test(password)) {
        showRedPopup("Password minimal ada 1 huruf kapital !");
        return false;
    }
    if (!/[a-z]/.test(password)) {
        showRedPopup("Password minimal ada 1 huruf kecil !");
        return false;
    }
    if (!/\d/.test(password)) {
        showRedPopup("Password minimal ada 1 angka !");
        return false;
    }
    if (!/[!@#$%^&*]/.test(password)) {
        showRedPopup("Password minimal ada 1 karakter unik !");
        return false;
    }
    return true;
}
editForm.onsubmit = function (event) {
    event.preventDefault();
    const nama = inpNama.value.trim();
    const role = inpRole.value.trim();
    const email = inpEmail.value.trim();
    const password = inpPassword.value.trim();

    if (
        nama === adminData.nama_admin &&
        role === adminData.role &&
        email === adminData.email &&
        password === ""
    ) {
        showRedPopup("Data belum diubah");
        return;
    }

    if (nama === "") {
        showRedPopup("Nama Admin harus diisi !");
        return;
    }

    if (role === "") {
        showRedPopup("Role Admin harus diisi !");
        return;
    }

    if (email === "") {
        showRedPopup("Email harus diisi !");
        return;
    }

    if (!isValidEmail(email)) {
        showRedPopup("Format Email salah !");
        return;
    }

    if (!validatePassword(password)) {
        return;
    }

    showLoading();
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("uuid", adminData.uuid);
    formData.append("nama_admin", nama);
    formData.append("role", role);
    formData.append("email", email);

    if (password !== "") {
        formData.append("password", password);
    }

    fetch(`/admin/update`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            closeLoading();
            if (data.status === "success") {
                showGreenPopup("Admin berhasil diupdate !");
                setTimeout(() => {
                    window.location.href = "/admin";
                }, 2000);
            } else {
                showRedPopup(data.message);
            }
        })
        .catch((error) => {
            closeLoading();
            showRedPopup("Terjadi kesalahan saat mengupdate admin !");
            console.error("Error:", error);
        });
    return false;
};
