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
    let userPassword = $(this).val();
    CheckPassword(userPassword);

    if ($(".passwordValidate").hasClass("invalidPassword")) {
        $("#signUpBtn").prop('disabled', true)
        $("#verifyBtn").prop('disabled', true)
    } else {			
        $("#signUpBtn").prop('disabled', false)
        $("#verifyBtn").prop('disabled', false)
    }
});

function CheckPasswordDentist(inputtxt, type) {
    var lowerCaseLetters = /[a-z]/g;
    var upperCaseLetters = /[A-Z]/g;
    var numbers = /[0-9]/g;
    var symbols = /\W/g;
    var error = 5;

    if(inputtxt.match(lowerCaseLetters)) {
        $("#" + type +"Lower").removeClass("invalidPassword");			
        $("#" + type +"Lower").addClass("validPassword");
        error--;
    } else {
        $("#" + type +"Lower").removeClass("validPassword");			
        $("#" + type +"Lower").addClass("invalidPassword");
        error++;
    } 

    if(inputtxt.match(upperCaseLetters)) {
        $("#" + type +"Upper").removeClass("invalidPassword");			
        $("#" + type +"Upper").addClass("validPassword");
        error--;
    } else {
        $("#" + type +"Upper").removeClass("validPassword");			
        $("#" + type +"Upper").addClass("invalidPassword");
        error++;
    }

    if(inputtxt.match(numbers)) {
        $("#" + type +"Number").removeClass("invalidPassword");			
        $("#" + type +"Number").addClass("validPassword");
        error--;
    } else {
        $("#" + type +"Number").removeClass("validPassword");			
        $("#" + type +"Number").addClass("invalidPassword");
        error++;
    }

    if(inputtxt.match(symbols)) {
        $("#" + type +"Symbol").removeClass("validPassword");			
        $("#" + type +"Symbol").addClass("invalidPassword");
        error++;
    } else {
        $("#" + type +"Symbol").removeClass("invalidPassword");			
        $("#" + type +"Symbol").addClass("validPassword");
        error--;
    }

    if(inputtxt.length >= 6) {
        $("#" + type +"Length").removeClass("invalidPassword");			
        $("#" + type +"Length").addClass("validPassword");
        error--;
    } else {
        $("#" + type +"Length").removeClass("validPassword");			
        $("#" + type +"Length").addClass("invalidPassword");
        error++;
    }

    return error;
}

$("#userPasswordCheck").on("keyup focusout focusin", function(e){
    let userPassword = $(this).val();
    let type = this.id == "userPasswordCheck" ? "userPass" : "confirmPass";

    if (CheckPasswordDentist(userPassword, type)) {
        $(this).addClass("is-invalid");
        $("#confirmPass input, #confirmPass button").prop("disabled", true);
    } else {
        $(this).removeClass("is-invalid");
        $("#confirmPass input, #confirmPass button").prop("disabled", false);
    };
});

$("#confirmUserPasswordCheck").on("keyup focusout focusin", function(e){
    let userPassword = $("#userPasswordCheck").val();
    let confirmPassword = $(this).val();
    
    if (userPassword !== confirmPassword) {
        $("#confirmPassCompare").text("• Passwords do not match.");
        $("#confirmPassCompare").removeClass("validPassword");
        $("#confirmPassCompare").addClass("invalidPassword");
        $(this).addClass("is-invalid");
        
    } else if (!confirmPassword) {
        $("#confirmPassCompare").text("• Password box is empty!");
        $("#confirmPassCompare").removeClass("validPassword");
        $("#confirmPassCompare").addClass("invalidPassword");
        $(this).addClass("is-invalid");
    } else {
        $("#confirmPassCompare").text("• Passwords match.");
        $("#confirmPassCompare").removeClass("invalidPassword");
        $("#confirmPassCompare").addClass("validPassword");
        $(this).removeClass("is-invalid");
    };
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
}

function hideLoader() {
    $("#overlay").hide();
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

function inputFilters() {
    $('.onlyNumbers').each(function() {
        setInputFilter(this, function(value) {
            return /^-?\d*$/.test(value); }, "Numbers Only.");
    });

    $('.onlyNumbersDots').each(function() {
        setInputFilter(this, function(value) {
            return /^-?\d*\.?\d*$/.test(value); }, "Numbers Only.");
    });
    
    $('.onlyLetters').each(function() {
        setInputFilter(this, function(value) {
            return /^[a-zA-Z.\-\s]*$/.test(value);
        }, "Letters, dots, hyphens, and spaces only.");
    });
    
    $('.onlyLettersNoSpace').each(function() {
        setInputFilter(this, function(value) {
            return /^[a-zA-Z.\-]*$/.test(value);
        }, "Letters, dots, hyphens only.");
    });
    
    $('.onlyEmail').each(function() {
        setInputFilter(this, function(value) {
            return /^[a-zA-Z0-9@._-]*$/.test(value);
        }, "Only letters, numbers, @, dots, underscores, and hyphens are allowed.");
    });
    
    $('.onlyLettersNumbers').each(function() {
        setInputFilter(this, function(value) {
            return /^[a-zA-Z0-9.\-\s]*$/.test(value);
        }, "Letters, numbers, dots, hyphens, and spaces only.");
    });
    
    $('.onlyAddress').each(function() {
        setInputFilter(this, function(value) {
            return /^[a-zA-Z0-9\s,.'-\/()#]*$/.test(value);
        }, "Valid address characters only.");
    });
    
    $('.onlyBlood').each(function() {
        setInputFilter(this, function(value) {
            return /^[0-9/]*$/.test(value);
        }, "Numbers and slashes only.");
    });
    
    $("#contnumber").on("focusin keypress focusout", function() {
        if (!this.value.startsWith("09")) {
            this.value = "09";
        }
    });
}


function setInputFilter(textbox, inputFilter, errMsg) {
    [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
        textbox.addEventListener(event, function(e) {
        if (inputFilter(this.value)) {
            if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0){
            this.classList.remove("input-error");
            this.setCustomValidity("");
            }

            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
        }
        else if (this.hasOwnProperty("oldValue")) {
            this.classList.add("input-error");
            this.setCustomValidity(errMsg);
            this.reportValidity();
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        }
        else {
            this.value = "";
        }
        });
    });
}