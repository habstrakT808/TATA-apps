const redPopup = document.querySelector("div#redPopup");
const greenPopup = document.querySelector("div#greenPopup");
var isPopupVisible = false;
function showGreenPopup(message, div = null) {
    if (typeof div === "string") {
        console.log(
            "Using deprecated showGreenPopup with hardcoded redirect. Use showSuccessAndRedirect instead."
        );
    }

    greenPopup.innerHTML = `
        <div class="bg" onclick="closePopup('green',true)"></div>
        <img class="kotak" src="${
            window.location.origin + tPath
        }/assets2/icon/popup/check.svg" alt="">
        <img class="closePopup" onclick="closePopup('green',true)" src="${
            window.location.origin + tPath
        }/assets2/icon/popup/close.svg" alt="">
        <label>${message}</label>
    `;
    greenPopup.style.display = "block";
    setTimeout(() => {
        // closePopup("green");
    }, 1000);
}
function showRedPopup(message, div) {
    if (div == "otp" && !isPopupVisible) {
        redPopup.innerHTML = `
            <div class="bg" onclick="closePopup('red',true)"></div>
            <img class="kotak" onclick="closePopup('red', true)" src="${
                window.location.origin + tPath
            }/assets2/icon/popup/error.svg" alt="">
            <img class="closePopup" onclick="closePopup('red',true)" src="${
                window.location.origin + tPath
            }/assets2/icon/popup/close.svg" alt="">
            <label>${message}</label>
        `;
        redPopup.style.display = "block";
        showDiv(div);
        isPopupVisible = true;
        setTimeout(() => {
            closePopup("red");
            isPopupVisible = false;
        }, 1000);
    } else if (!isPopupVisible) {
        if (message) {
            redPopup.innerHTML = `
                <div class="bg" onclick="closePopup('red',true)"></div>
                <img class="kotak" onclick="closePopup('red', true)" src="${
                    window.location.origin + tPath
                }/assets2/icon/popup/error.svg" alt="">
                <img class="closePopup" onclick="closePopup('red',true)" src="${
                    window.location.origin + tPath
                }/assets2/icon/popup/close.svg" alt="">
                <label>${message}</label>
            `;
            redPopup.style.display = "block";
            isPopupVisible = true;
            setTimeout(() => {
                closePopup("red");
                isPopupVisible = false;
            }, 1000);
        } else {
            redPopup.innerHTML = `
                <div class="bg" onclick="closePopup('red',true)"></div>
                <img class="kotak" onclick="closePopup('red', true)" src="${
                    window.location.origin + tPath
                }/assets2/icon/popup/error.svg" alt="">
                <img class="closePopup" onclick="closePopup('red', true)" src="${
                    window.location.origin + tPath
                }/assets2/icon/popup/close.svg" alt="">
                <label>${data}</label>
            `;
            redPopup.style.display = "block";
            isPopupVisible = true;
            setTimeout(() => {
                closePopup("red");
                isPopupVisible = false;
            }, 1000);
        }
    }
}
function closePopup(opt, click = false, div = null) {
    if (click) {
        if (opt == "green") {
            greenPopup.style.display = "none";
            greenPopup.innerHTML = "";
        } else if (opt == "red") {
            redPopup.style.display = "none";
            redPopup.innerHTML = "";
        }
    } else {
        if (opt == "green") {
            greenPopup.classList.add("fade-out");
            setTimeout(() => {
                greenPopup.style.display = "none";
                greenPopup.classList.remove("fade-out");
                greenPopup.innerHTML = "";
            }, 750);
        } else if (opt == "red") {
            redPopup.classList.add("fade-out");
            setTimeout(() => {
                redPopup.style.display = "none";
                redPopup.classList.remove("fade-out");
                redPopup.innerHTML = "";
            }, 750);
        }
    }
    if (div) {
        if (div == "login") {
            loginPage();
        } else {
            showDiv(div);
        }
    }
}
function showDiv(div) {
    if (div == "otp") {
        document.querySelector("div#registerDiv").style.display = "none";
        document.querySelector("div#otp").style.display = "block";
    }
}
