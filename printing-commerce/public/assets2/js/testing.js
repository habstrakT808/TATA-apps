const modalDelete = document.querySelector('#modalDelete');
const deleteForm = document.getElementById('deleteForm');
const inpID = document.getElementById('inpID');
let isAnimating = false;
function showModalDelete(id){
    inpID.value = id;
    modalDelete.style.display = 'block';
    animateModalDelete('50%');
}

function closeModalDelete(){
    animateModalDelete('-50%');
}

function animateModalDelete(finalTop) {
    let currentTop = parseInt(deleteForm.style.top) || 0;
    let increment = currentTop < parseInt(finalTop) ? 1 : -1;
    function frame() {
        currentTop += increment;
        deleteForm.style.top = currentTop + '%';
        if ((increment === 1 && currentTop >= parseInt(finalTop)) || (increment === -1 && currentTop <= parseInt(finalTop))) {
            clearInterval(animationInterval);
            if (finalTop === '50%') {
                isAnimating = false;
            } else {
                modalDelete.style.display = 'none';
            }
        }
    }
    let animationInterval = setInterval(frame, 10);
}
deleteForm.onsubmit = function (event) {
    event.preventDefault();
    showLoading();
    var xhr = new XMLHttpRequest();
    var requestBody = {
        uuid: uuid,
        email: email,
    };
    xhr.open("POST", "/admin" + reff + "/delete");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showGreenPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    };
    return false;
};