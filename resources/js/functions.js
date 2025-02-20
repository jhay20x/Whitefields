$("#userPassword, #togglePassword").on("focusin", function(e) {
    if ($("#loginLabel").text() == "Sign Up") {
        $("#passwordValidation").slideToggle("fast", function() {});
    }
});

$("#userPassword, #togglePassword").on("focusout", function(e) {
    if ($("#loginLabel").text() == "Sign Up") {
        $("#passwordValidation").slideToggle("fast", function() {});
    }
});


$("#userPassword, #togglePassword").on("focusin", function(e) {
    if ($("#loginLabel").text() == "Forgot Password") {
        $("#passwordValidation").slideToggle("fast", function() {});
    }
});


$("#userPassword, #togglePassword").on("focusout", function(e) {
    if ($("#loginLabel").text() == "Forgot Password") {
        $("#passwordValidation").slideToggle("fast", function() {});
    }
});

function CheckPassword(inputtxt) {
    var lowerCaseLetters = /[a-z]/g;
    var upperCaseLetters = /[A-Z]/g;
    var numbers = /[0-9]/g;
    var symbols = /\W/g;

    if(inputtxt.match(lowerCaseLetters)) {
        $("#passLower").removeClass("invalidPassword");			
        $("#passLower").addClass("validPassword");
    } else {
        $("#passLower").removeClass("validPassword");			
        $("#passLower").addClass("invalidPassword");
    }

    if(inputtxt.match(upperCaseLetters)) {
        $("#passUpper").removeClass("invalidPassword");			
        $("#passUpper").addClass("validPassword");
    } else {
        $("#passUpper").removeClass("validPassword");			
        $("#passUpper").addClass("invalidPassword");
    }

    if(inputtxt.match(numbers)) {
        $("#passNumber").removeClass("invalidPassword");			
        $("#passNumber").addClass("validPassword");
    } else {
        $("#passNumber").removeClass("validPassword");			
        $("#passNumber").addClass("invalidPassword");
    }

    if(inputtxt.match(symbols)) {
        $("#passSymbol").removeClass("validPassword");			
        $("#passSymbol").addClass("invalidPassword");
    } else {
        $("#passSymbol").removeClass("invalidPassword");			
        $("#passSymbol").addClass("validPassword");
    }

    if(inputtxt.length >= 6) {
        $("#passLength").removeClass("invalidPassword");			
        $("#passLength").addClass("validPassword");
    } else {
        $("#passLength").removeClass("validPassword");			
        $("#passLength").addClass("invalidPassword");
    }		
}

$("#userPassword").keyup(function(e){
    let userPassword = document.getElementById("userPassword");		
    CheckPassword(userPassword.value);

    if ($(".passwordValidate").hasClass("invalidPassword")) {
        $("#signUpBtn").prop('disabled', true)
        $("#verifyBtn").prop('disabled', true)
    } else {			
        $("#signUpBtn").prop('disabled', false)
        $("#verifyBtn").prop('disabled', false)
    }
});

function redirectDashboard() {
    window.location.replace('./users/secretary/dashboard.php');
}

function redirectVerifyEmail() {
    window.location.replace('verify-email.php');
}

function redirectForgotPassword() {
    window.location.replace('forgot-password.php');
}

$("#signUpLink").click(function(e){
    $("#errorMessage").empty();
    clearInputs();
    showSignUp();    
    grecaptcha.reset();
});

$("#loginLink").click(function(e){
    $("#errorMessage").empty();
    clearInputs();
    showLogin();
    grecaptcha.reset();
});

$("#forgotPassword").click(function(e){
    redirectForgotPassword();
});

$("#backToLogin").click(function(e){
    window.location.replace('login.php');
});

function clearInputs() {
    $(".clearInputs").val("");
}

function disableInputs() {
    $(".disableInputs").prop('disabled', true);
}

function enableInputs() {
    $(".disableInputs").prop('disabled', false);
}

function showLoader() {
    $("#overlay").show();
    $('html, body').css({
        overflow: 'hidden',
        height: '100%'
    });
}

function hideLoader() {
    $("#overlay").hide();
    $('html, body').css({
        overflow: 'auto',
        height: 'auto'
    });
}

function showSignUp() {
    $(".userSignUp").show();
    $(".userLogin").hide();
    $("#loginLabel").text("Sign Up");
    $("#loginUserEmail, #signUpBtn").prop('disabled', true);
    $("#signUpUsername, #signUpEmail").prop('disabled', false);
    $('#myForm').attr('action', 'auth/auth-signup.php');
}

function showLogin () {
    $(".userSignUp").hide();
    $(".userLogin").show();
    $("#loginLabel").text("Login");
    $("#loginUserEmail").prop('disabled', false);
    $("#signUpUsername, #signUpEmail").prop('disabled', true);
    $('#myForm').attr('action', 'auth/auth-login.php');
}

function setInputFilter(textbox, inputFilter, errMsg) {
    [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
        textbox.addEventListener(event, function(e) {
        if (inputFilter(this.value)) {
            // Accepted value.
            if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0){
            this.classList.remove("input-error");
            this.setCustomValidity("");
            }

            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
        }
        else if (this.hasOwnProperty("oldValue")) {
            // Rejected value: restore the previous one.
            this.classList.add("input-error");
            this.setCustomValidity(errMsg);
            this.reportValidity();
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        }
        else {
            // Rejected value: nothing to restore.
            this.value = "";
        }
        });
    });
}