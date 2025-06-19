// Fungsi untuk menangani perubahan pada input file
function handleImagesChange(event) {
    const files = event.target.files;
    const gallery = document.getElementById("imageGallery");

    // Validasi jumlah gambar
    if (files.length > 5) {
        alert("Maksimal 5 gambar yang dapat diunggah!");
        return;
    }

    // Hapus semua gambar yang ada di gallery kecuali tombol tambah
    const existingImages = gallery.querySelectorAll(".image-item");
    existingImages.forEach((item) => gallery.removeChild(item));

    // Tambahkan gambar baru
    for (let i = 0; i < files.length; i++) {
        const file = files[i];

        // Validasi tipe file
        if (
            !file.type.match("image/jpeg") &&
            !file.type.match("image/png") &&
            !file.type.match("image/jpg")
        ) {
            alert("Hanya file gambar (JPEG, PNG, JPG) yang diperbolehkan!");
            continue;
        }

        // Validasi ukuran file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert("Ukuran file tidak boleh lebih dari 5MB!");
            continue;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const imageItem = document.createElement("div");
            imageItem.className = "image-item";

            const img = document.createElement("img");
            img.src = e.target.result;
            img.alt = "Gallery Image";

            const removeBtn = document.createElement("button");
            removeBtn.className = "remove-btn";
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function () {
                gallery.removeChild(imageItem);

                // Reset file input jika semua gambar dihapus
                const remainingImages = gallery.querySelectorAll(".image-item");
                if (remainingImages.length === 0) {
                    document.getElementById("inpImages").value = "";
                }
            };

            imageItem.appendChild(img);
            imageItem.appendChild(removeBtn);

            // Tambahkan sebelum tombol tambah
            const addBtn = gallery.querySelector(".add-image-btn");
            gallery.insertBefore(imageItem, addBtn);
        };

        reader.readAsDataURL(file);
    }
}

// Inisialisasi form tambah jasa
document.addEventListener("DOMContentLoaded", function () {
    const createForm = document.getElementById("createForm");

    if (createForm) {
        createForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Validasi form
            const kategori = document.querySelector(
                'select[name="kategori"]'
            ).value;
            const kelasJasa = document.querySelector(
                'select[name="kelas_jasa"]'
            ).value;
            const deskripsiJasa = document.querySelector(
                'textarea[name="deskripsi_jasa"]'
            ).value;
            const hargaPaketJasa = document.querySelector(
                'input[name="harga_paket_jasa"]'
            ).value;
            const waktuPengerjaan = document.querySelector(
                'input[name="waktu_pengerjaan"]'
            ).value;
            const maksimalRevisi = document.querySelector(
                'input[name="maksimal_revisi"]'
            ).value;
            const deskripsiSingkat = document.querySelector(
                'textarea[name="deskripsi_singkat"]'
            ).value;
            const images = document.getElementById("inpImages").files;

            if (!kategori) {
                alert("Kategori wajib diisi!");
                return;
            }

            if (!kelasJasa) {
                alert("Kelas Jasa wajib diisi!");
                return;
            }

            if (!deskripsiJasa) {
                alert("Deskripsi Jasa wajib diisi!");
                return;
            }

            if (!hargaPaketJasa) {
                alert("Harga Paket Jasa wajib diisi!");
                return;
            }

            if (!waktuPengerjaan) {
                alert("Waktu Pengerjaan wajib diisi!");
                return;
            }

            if (!maksimalRevisi) {
                alert("Maksimal Revisi wajib diisi!");
                return;
            }

            if (!deskripsiSingkat) {
                alert("Deskripsi Singkat wajib diisi!");
                return;
            }

            if (images.length === 0) {
                alert("Gambar Jasa wajib diisi!");
                return;
            }

            // Buat FormData untuk mengirim data termasuk file
            const formData = new FormData();
            formData.append("kategori", kategori);
            formData.append("kelas_jasa", kelasJasa);
            formData.append("deskripsi_jasa", deskripsiJasa);
            formData.append("harga_paket_jasa", hargaPaketJasa);
            formData.append("waktu_pengerjaan", waktuPengerjaan);
            formData.append("maksimal_revisi", maksimalRevisi);
            formData.append("deskripsi_singkat", deskripsiSingkat);

            // Tambahkan semua file gambar
            for (let i = 0; i < images.length; i++) {
                formData.append("images[]", images[i]);
            }

            // Kirim data ke server
            fetch("/services/jasa/create", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        alert("Jasa berhasil ditambahkan!");
                        window.location.href = "/jasa";
                    } else {
                        alert("Gagal menambahkan jasa: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat menambahkan jasa.");
                });
        });
    }
});
