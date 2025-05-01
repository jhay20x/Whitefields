<?php 
// session_id("session-customer");
session_start();
// $hash = password_hash('password', PASSWORD_DEFAULT);
// echo $hash;
// echo date('H:i:s', time());
// if (isset($_GET['errors'])) {
// 	echo $_GET['errors'];
// }

// if (isset($_GET['userLogin'])) {
// 	 $userLogin = $_GET['userLogin'];
// } else {
// 	header("Location: ./login.php?userLogin=1");
// }

// if (isset($_SESSION['email_address'])) {
// 	unset($_SESSION['email_address']);
// }

if ((!isset($_SESSION['user_id']) && !isset($_SESSION['user_username']) && !isset($_SESSION['account_type']))) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- <link rel="manifest" href="https://sour-white-pinniped.myfilebase.com/ipfs/QmcN7y7yjdiKZzqPhHRSJyy1SSQF33FmYnZmscuhfYEP7x" /> -->
	<link rel="manifest" href="manifest.json" />
	<link rel="shortcut icon" type="image/x-icon" href="./resources/images/logo-icon-67459a47526b9.webp"/>
    <link rel="stylesheet" href="./resources/css/bootstrap.css">
    <link rel="stylesheet" href="./resources/css/loader.css">
    <!-- <link rel="stylesheet" href="./vendor/twbs/bootstrap-icons/font/bootstrap-icons.css"> -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">	
	
	<title>Login - Whitefields Dental Clinic</title>
</head>

<style>
    .invalidPassword {
        color: red;
    }  
    
    .validPassword {
        color: green;
    }
</style>

<body class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
	<div id="overlay" style="display:none;">
		<div id="loader"></div>		
	</div>
	
	<div class="d-flex justify-content-center align-items-center row container">
		<div class="d-flex align-content-center justify-content-center row">
			<form class="d-flex col justify-content-center align-items-center" method="post" action="" id="myForm">
				<div class="p-5 flex-column rounded shadow d-flex align-items-center row">
					<div class="d-flex flex-column align-items-center">
						<a href="home.php">
							<img src="./resources/images/wfdc-logo-67459a4f483d0.webp" alt="Logo" width="250" height="80">
						</a>
						<h1 id="loginLabel" class="text-center pb-1 mb-3 display-6">Login</h1>
					</div>
					
					<div id="errorMessage"></div>			
		
					<div style="display: none;" class="userSignUp">
						<div class="mb-3 form-floating">
							<input type="text" maxlength="30" autocomplete="off" class="form-control clearInputs disableInputs onlyLettersNumbersNoSpace" name="signUpUsername" id="signUpUsername" placeholder="Username">
							<label for="signUpUsername">Username</label>
						</div>
						
						<div class="mb-3 form-floating">
							<input type="email" maxlength="50" autocomplete="off" class="form-control clearInputs disableInputs" name="signUpEmail" id="signUpEmail" placeholder="Email">
							<label for="signUpEmail">Email</label>
						</div>	
					</div>
					
					<div style="display: block;" class="userLogin">
						<div class="mb-3 form-floating">
							<input type="username" class="form-control clearInputs disableInputs" name="loginUserEmail" id="loginUserEmail" placeholder="Email or Username">
							<label for="loginUserEmail">Email or Username</label>
						</div>
					</div>
		
					<div class="input-group mb-3">
						<div class="form-floating">
							<input type="password" minlength="6" maxlength="20" id="userPassword" autocomplete="off" class="form-control clearInputs disableInputs" name="password" placeholder="Password">
							<label for="userPassword">Password</label>
						</div>
						<button class="btn btn-outline-secondary disableInputs" type="button" id="togglePassword">
							<i id="eyeicon" class="bi bi-eye"></i>
						</button>
					</div>
								
					<div class="mb-3" id="passwordValidation">
						<p id="passLength" class="passwordValidate invalidPassword">• Minimum of 6 characters. Max 20.</p>
						<p id="passSymbol" class="passwordValidate validPassword">• Must not include any symbols except _.</p>
						<p id="passLower" class="passwordValidate invalidPassword">• Must use atleast one lower case letter.</p>
						<p id="passUpper" class="passwordValidate invalidPassword">• Must use atleast one upper case letter.</p>
						<p id="passNumber" class="passwordValidate invalidPassword">• Must use atleast one number.</p>
					</div>	
		
					<div class="g-recaptcha mb-3" data-size="normal" data-sitekey="6LfUKIYqAAAAAG2JGMbi3lzZ-arHMfvejYNp3VVC" data-action="LOGIN"></div>
						
					<div style="display: block;" class="userLogin">
						<div class="d-flex flex-column align-items-center">
							<button type="submit" id="loginBtn" name="userLogin" class="btn p-2 mb-3 col-12 btn-outline-primary disableInputs">LOGIN</button>
							<h6>
								No account yet?
								<button type="button" id="signUpLink" class="btn btn-sm btn-outline-primary disableInputs">Sign up</button>
								<!-- <a id="signUpLink" class="" href="#">Sign up</a> -->
							</h6>
							<h6>
								<button type="button" id="forgotPassword" class="btn btn-sm btn-outline-primary disableInputs">Forgot password?</button>
								<!-- <a href="#">Forgot password?</a> -->
							</h6>
						</div>
					</div>
					
					<div style="display: none;" class="userSignUp">
						<div class="d-flex flex-column align-items-center">
							<button type="submit" id="signUpBtn" class="btn p-2 mb-3 col-12 btn-outline-primary disableInputs">SIGN UP</button>
							<h6>
							Already signed up?
							<button type="button" id="loginLink" class="btn btn-sm btn-outline-primary disableInputs">Login</button>
								<!-- Already signed up? <a id="loginLink" href="#">Login</a> -->
							</h6>
						</div>
					</div>
				</div>
			</form>

			<div class="row col justify-content-center align-items-center d-none d-lg-flex">
				<img src="./resources/images/dentist.svg" width="55px" alt="Dentist">
			</div>
		</div>
	</div>
</body>
</html>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="./resources/js/jquery-3.7.1.js"></script>
<script src="./resources/js/loginPasswordToggler.js" defer></script>
<script src="./resources/js/functions.js" defer></script>
<script>	
	$(document).ready(function () {
		inputFilters();

		let userEmail, username;

		$("#passwordValidation").hide();

		$("#myForm").submit(function(e){
			$("#errorMessage").empty();
			e.preventDefault();
			showLoader();
			$("#loginUserEmail, #userPassword, #signUpUsername, #signUpEmail").blur;

			var url = $("#myForm").attr('action');
			// var formData = {
			// 	loginUserEmail: $("#loginUserEmail").val(),
			// 	password: $("#userPassword").val(),
			// 	signUpUsername: $("#signUpUsername").val(),
			// 	signUpEmail: $("#signUpEmail").val(),
			// };

			$.ajax({
				type: "POST",
				url: url,
				data: $("form").serialize(),
				dataType: "json"
			}).done(function (data) {
				userEmail = data.userEmail;
				username = data.username;
				user_id = data.user_id;
				// console.log(data);
				hideLoader();
				if (!data.success) {
					$("#loginUserEmail").focus();
					$("#loginUserEmail").val(data.username);
					$("#userPassword").val("");
					grecaptcha.reset();
					$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show" style="width: 300px;" role="alert">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
				} else {
					$("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show" style="width: 300px;" role="alert">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
					disableInputs();
					setTimeout(showLoader, 2000);
					if (data.emailVerification) {
						// console.log(data.usertype);
						setTimeout(redirectDashboard, 2000);
					} else {
						setTimeout(sendOTP, 1000);
						setTimeout(redirectVerifyEmail, 2000);
					}
				}
				// $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + data.responseText +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
				// console.log(data);
			}).fail(function(data) {
				// $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + data.responseText +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
				// console.log(data);
			});
		});
		
		showLogin();
	});
	

	function sendOTP() {
		var formData = {
			mode: "verifyEmail"
		};

		$.ajax({
			type: "POST",
			url: "auth/send-otp.php",
			data: formData,
			dataType: "json"
		}).done(function (data) {
			// console.log(data);
			hideLoader();
			if (!data.success) {
				$("#forgotEmail").focus();
				$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			} else {
				$("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show" role="alert">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			}
		}).fail(function(data) {
			// console.log(data);
		});
	}
</script>
<?php 
}else {
	if (isset($_SESSION['account_type'])) {
		if ($_SESSION['account_type'] == 1) {
			header("Location: ./users/secretary/dashboard.php");
		}
		
		if ($_SESSION['account_type'] == 2) {
			header("Location: ./users/patient/dashboard.php");			
		}
		
		if ($_SESSION['account_type'] == 3) {
			header("Location: ./users/doctor/dashboard.php");						
		}
	} else {
		session_unset();
		session_destroy();
		header("Location: login.php");
	}
	
	// if ($_SESSION['user_type'] === 2) {
	// 	header("Location: /VMANRacing/POS/index.php");
	// }
}
?>