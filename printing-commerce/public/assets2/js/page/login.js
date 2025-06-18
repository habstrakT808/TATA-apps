const domain = window.location.protocol + "//" + window.location.hostname + ":" + window.location.port;
const inpEmail = document.getElementById("inpEmail");
const inpPassword = document.getElementById("inpPassword");
const iconPass = document.getElementById("iconPass");
const loginForm = document.getElementById("loginForm");
var isPasswordShow = false;
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
loginForm.onsubmit = function (event) {
    event.preventDefault();
    const email = inpEmail.value;
    const password = inpPassword.value;
    if (email.trim() === "") {
        showRedPopup("Email harus diisi !");
        return;
    }
    if (password.trim() === "") {
        showRedPopup("Password harus diisi !");
        return;
    }
    showLoading();
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: inpEmail.value,
        password: inpPassword.value,
    };
    xhr.open("POST", "/admin/login");
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showGreenPopup(response.message, "dashboard");
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response.message);
            }
        }
    };
    return false;
};