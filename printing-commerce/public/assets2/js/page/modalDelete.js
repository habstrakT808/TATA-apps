var uuid = null;
var modalDelete = null;

function showLoading() {
    document.querySelector("div#preloader").style.display = "block";
}

function closeLoading() {
    document.querySelector("div#preloader").style.display = "none";
}

function showModalDelete(id) {
    uuid = id;
    document.getElementById("inpID").value = id;
    document.getElementById("modalDelete").style.display = "flex";
}

function closeModalDelete() {
    document.getElementById("modalDelete").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    modalDelete =
        document.getElementById("modalDelete").getAttribute("data-type") ||
        "jasa";

    document
        .getElementById("deleteForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();

            if (uuid === null) {
                showRedPopup("ID tidak valid");
                return;
            }

            document.getElementById("preloader").style.display = "block";

            const xhr = new XMLHttpRequest();
            xhr.open("DELETE", domain + "/" + modalDelete + "/delete", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);

            xhr.onload = function () {
                document.getElementById("preloader").style.display = "none";
                closeModalDelete();

                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        showGreenPopup(response.message);
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showRedPopup(response.message);
                    }
                } else {
                    let errorMessage = "Terjadi kesalahan. Silakan coba lagi.";
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {}
                    showRedPopup(errorMessage);
                }
            };

            xhr.onerror = function () {
                document.getElementById("preloader").style.display = "none";
                closeModalDelete();
                showRedPopup("Terjadi kesalahan jaringan. Silakan coba lagi.");
            };

            // Kirim data sesuai dengan jenis modal
            let requestData = {};
            if (modalDelete === "jasa") {
                requestData = { id_jasa: uuid };
            } else {
                requestData = { uuid: uuid };
            }

            xhr.send(JSON.stringify(requestData));
        });
});
