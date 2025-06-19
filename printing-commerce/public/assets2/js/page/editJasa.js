// Fungsi untuk menangani perubahan pada input file
function handleImagesChange(event) {
    const files = event.target.files;
    const gallery = document.getElementById("imageGallery");

    // Hitung jumlah gambar yang sudah ada
    const existingImages = gallery.querySelectorAll(".image-item");
    const totalImages = existingImages.length + files.length;

    // Validasi jumlah gambar
    if (totalImages > 5) {
        alert("Maksimal 5 gambar yang dapat diunggah!");
        return;
    }

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

                // Tampilkan tombol tambah jika jumlah gambar kurang dari 5
                const remainingImages = gallery.querySelectorAll(".image-item");
                const addBtn = document.querySelector(".btn-add-new");
                if (remainingImages.length < 5 && addBtn) {
                    addBtn.style.display = "block";
                }
            };

            imageItem.appendChild(img);
            imageItem.appendChild(removeBtn);

            // Tambahkan sebelum tombol tambah
            const addBtn = gallery.querySelector(".add-image-btn");
            if (addBtn) {
                gallery.insertBefore(imageItem, addBtn);
            } else {
                gallery.appendChild(imageItem);
            }

            // Sembunyikan tombol tambah jika jumlah gambar mencapai 5
            const updatedImages = gallery.querySelectorAll(".image-item");
            const addBtnHeader = document.querySelector(".btn-add-new");
            if (updatedImages.length >= 5 && addBtnHeader) {
                addBtnHeader.style.display = "none";
            }
        };

        reader.readAsDataURL(file);
    }
}

// Fungsi untuk menghapus gambar yang sudah ada
function deleteImage(element, imageId) {
    if (confirm("Apakah Anda yakin ingin menghapus gambar ini?")) {
        const imageItem = element.closest(".image-item");
        const gallery = document.getElementById("imageGallery");

        // Tambahkan ID gambar ke array gambar yang akan dihapus
        const deletedImagesInput = document.getElementById("deletedImages");
        let deletedImages = deletedImagesInput.value
            ? JSON.parse(deletedImagesInput.value)
            : [];
        deletedImages.push(imageId);
        deletedImagesInput.value = JSON.stringify(deletedImages);

        // Hapus elemen gambar dari DOM
        gallery.removeChild(imageItem);

        // Tampilkan tombol tambah jika jumlah gambar kurang dari 5
        const remainingImages = gallery.querySelectorAll(".image-item");
        const addBtn = document.querySelector(".btn-add-new");
        if (remainingImages.length < 5 && addBtn) {
            addBtn.style.display = "block";
        }

        // Tampilkan tombol tambah di gallery jika belum ada
        const addBtnGallery = gallery.querySelector(".add-image-btn");
        if (!addBtnGallery && remainingImages.length < 5) {
            const newAddBtn = document.createElement("div");
            newAddBtn.className = "add-image-btn";
            newAddBtn.innerHTML =
                '<i class="fas fa-plus"></i><span>Tambah Gambar</span>';
            newAddBtn.onclick = function () {
                document.getElementById("inpImages").click();
            };
            gallery.appendChild(newAddBtn);
        }
    }
}

// Inisialisasi form edit jasa
document.addEventListener("DOMContentLoaded", function () {
    const editForm = document.getElementById("editForm");

    // Sembunyikan tombol tambah jika jumlah gambar mencapai 5
    const gallery = document.getElementById("imageGallery");
    if (gallery) {
        const existingImages = gallery.querySelectorAll(".image-item");
        const addBtn = document.querySelector(".btn-add-new");
        if (existingImages.length >= 5 && addBtn) {
            addBtn.style.display = "none";
        }

        // Tambahkan tombol tambah di gallery jika belum ada dan jumlah gambar kurang dari 5
        const addBtnGallery = gallery.querySelector(".add-image-btn");
        if (!addBtnGallery && existingImages.length < 5) {
            const newAddBtn = document.createElement("div");
            newAddBtn.className = "add-image-btn";
            newAddBtn.innerHTML =
                '<i class="fas fa-plus"></i><span>Tambah Gambar</span>';
            newAddBtn.onclick = function () {
                document.getElementById("inpImages").click();
            };
            gallery.appendChild(newAddBtn);
        }
    }

    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Validasi form
            const deskripsiJasa = document.querySelector(
                'textarea[name="deskripsi_jasa"]'
            ).value;

            if (!deskripsiJasa) {
                alert("Deskripsi Jasa wajib diisi!");
                return;
            }

            // Buat FormData untuk mengirim data termasuk file
            const formData = new FormData(editForm);

            // Tambahkan semua file gambar baru
            const images = document.getElementById("inpImages").files;
            for (let i = 0; i < images.length; i++) {
                formData.append("new_images[]", images[i]);
            }

            // Kirim data ke server
            fetch("/services/jasa/update", {
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
                        alert("Jasa berhasil diperbarui!");
                        window.location.href = "/jasa";
                    } else {
                        alert("Gagal memperbarui jasa: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat memperbarui jasa.");
                });
        });
    }
});
