const tambahForm = document.getElementById("tambahForm");
const inpNama = document.getElementById("inpNama");
const inpJenisKelamin = document.getElementById("inpJenisKelamin");
const inpNoTelpon = document.getElementById("inpNoTelpon");

// Utility Functions
function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}

function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}

// Form Submission
tambahForm.onsubmit = function(event) {
    event.preventDefault();
    
    // Get form values
    const nama = inpNama.value.trim();
    const jenisKelamin = inpJenisKelamin.value.trim();
    const noTelpon = inpNoTelpon.value.trim();

    // Validate required fields
    if(nama === "") {
        showRedPopup("Nama Editor harus diisi !");
        return;
    }

    // Validate jenis kelamin
    if(jenisKelamin === "") {
        showRedPopup("Jenis Kelamin harus dipilih !");
        return;
    }

    // Validate phone number
    if(noTelpon === "") {
        showRedPopup("Nomor Telepon harus diisi !");
        return;
    }
    if(isNaN(noTelpon)) {
        showRedPopup("Nomor Telepon harus angka !");
        return;
    }
    if(!/^08\d+$/.test(noTelpon)) {
        showRedPopup("Nomor Telepon harus dimulai dengan 08 !");
        return;
    }
    if(!/^\d{11,13}$/.test(noTelpon)) {
        showRedPopup("Nomor Telepon harus terdiri dari 11-13 digit angka !");
        return;
    }

    // Prepare form data
    showLoading();
    const formData = new FormData();
    formData.append("nama_editor", nama);
    formData.append("jenis_kelamin", jenisKelamin);
    formData.append("no_telpon", noTelpon);

    // Send request
    fetch('/editor/create', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        closeLoading();
        if(data.status === 'success') {
            showGreenPopup('Editor berhasil ditambahkan !');
            setTimeout(() => {
                window.location.href = '/editor';
            }, 2000);
        } else {
            showRedPopup(data.message);
        }
    })
    .catch(error => {
        closeLoading();
        showRedPopup('Terjadi kesalahan saat menambahkan editor !');
        console.error('Error:', error);
    });
}; 