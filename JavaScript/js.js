function pwdmeter(passwordID,meterID,textID) {
    var strength = {
        0: "Ļoti vāja",
        1: "Slikta",
        2: "Vāja",
        3: "Apmierinoša",
        4: "Stipra"
    }
    
    var password = document.getElementById(passwordID);
    var meter = document.getElementById(meterID);
    var text = document.getElementById(textID);
    
    password.addEventListener('input', function() {
    var val = password.value;
    var result = zxcvbn(val);
    
    // Update the password strength meter
    meter.value = result.score;
    
    // Update the text indicator
    if (val !== "") {
    text.innerHTML = "Paroles stiprums: " + strength[result.score]; 
    } else {
    text.innerHTML = "";
    }
    });
}

function togglewiev(field) {
    var view = document.getElementById(field);
    if(view.style.display == 'flex'){
        view.style.display = 'none';
    } else{
        view.style.display = 'flex';
    }
}

function showPwd(field,icon) {
    var pswrdField = document.getElementById(field);
    var toggleIcon = document.getElementById(icon);

    if(pswrdField.type === "password"){
        pswrdField.type = "text";
        toggleIcon.classList.add("active");
      }else{
        pswrdField.type = "password";
        toggleIcon.classList.remove("active");
      }
}


function checkPassword(pwd,pwdr,errorfield) {

    var password1 = document.getElementById(pwd).value;
    var password2 = document.getElementById(pwdr).value;
    var error = document.getElementById(errorfield);
    
    if (password1 != password2) {
        error.classList.remove('hidden');
    } else{
        error.classList.add('hidden');
    }
}
function lenghtCheck(field,errorF){
    var password = document.getElementById(field);
    var error = document.getElementById(errorF);
    if (password.value.length < 8) {
        error.classList.remove('hidden');
    } else{
        error.classList.add('hidden');
    }
}