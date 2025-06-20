const editForm = document.getElementById("editForm");
const inpNama = document.getElementById("inpNama");
const inpEmail = document.getElementById("inpEmail");
const inpJenisKelamin = document.getElementById("inpJenisKelamin");
const inpNoTelpon = document.getElementById("inpNoTelpon");

// Utility Functions
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}

function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Form Submission
editForm.onsubmit = function (event) {
    event.preventDefault();

    // Get form values
    const nama = inpNama.value.trim();
    const email = inpEmail.value.trim();
    const jenisKelamin = inpJenisKelamin.value.trim();
    const noTelpon = inpNoTelpon.value.trim();

    if (
        nama === editorData.nama_editor &&
        email === editorData.email &&
        noTelpon === editorData.no_telpon &&
        jenisKelamin === editorData.jenis_kelamin
    ) {
        showRedPopup("Data belum diubah");
        return;
    }

    // Validate required fields
    if (nama === "") {
        showRedPopup("Nama Editor harus diisi !");
        return;
    }

    // Validate email
    if (email === "") {
        showRedPopup("Email harus diisi !");
        return;
    }
    if (!isValidEmail(email)) {
        showRedPopup("Format Email tidak valid !");
        return;
    }

    // Validate jenis kelamin
    if (jenisKelamin === "") {
        showRedPopup("Jenis Kelamin harus dipilih !");
        return;
    }

    // Validate phone number
    if (noTelpon === "") {
        showRedPopup("Nomor Telepon harus diisi !");
        return;
    }
    if (isNaN(noTelpon)) {
        showRedPopup("Nomor Telepon harus angka !");
        return;
    }
    if (!/^08\d+$/.test(noTelpon)) {
        showRedPopup("Nomor Telepon harus dimulai dengan 08 !");
        return;
    }
    if (!/^\d{11,13}$/.test(noTelpon)) {
        showRedPopup("Nomor Telepon harus terdiri dari 11-13 digit angka !");
        return;
    }

    // Prepare form data
    showLoading();
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("id_editor", editorData.uuid);
    formData.append("nama_editor", nama);
    formData.append("email", email);
    formData.append("jenis_kelamin", jenisKelamin);
    formData.append("no_telpon", noTelpon);

    // Send request
    fetch(`/editor/update`, {
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
                showGreenPopup("Editor berhasil diupdate !");
                setTimeout(() => {
                    window.location.href = "/editor";
                }, 2000);
            } else {
                showRedPopup(data.message);
            }
        })
        .catch((error) => {
            closeLoading();
            showRedPopup("Terjadi kesalahan saat mengupdate editor !");
            console.error("Error:", error);
        });
};
