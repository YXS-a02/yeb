function checkInput(elementId,name) {
    var input = document.getElementById(elementId).value;

    if (input === name) {
        return true;
    } else {
        return false;
    }
}

function sarah() {
    if (checkInput("sarah","yeb")) {
        alert("Is there!");
    } else {
        handleOtherCase();
    }
}

function handleOtherCase() {
    if (checkInput("sarah","/")) {
        alert("......")
    } else {
        alert("sorry, I don't know!");
    }
}
