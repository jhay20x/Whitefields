<?php 
// session_id("session-customer");
session_start();

// if (isset($_GET['errors'])) {
// 	echo $_GET['errors'];
// }

// if (isset($_GET['userLogin'])) {
// 	 $userLogin = $_GET['userLogin'];
// } else {
	// header("Location: ./login.php?userLogin=1");
// }
// echo $_SESSION["email_address"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/x-icon" href="./resources/images/logo-icon-67459a47526b9.webp"/>
    <link rel="stylesheet" href="./resources/css/bootstrap.css">
    <link rel="stylesheet" href="./resources/css/loader.css">
	<script type="text/javascript" src="./resources/js/jquery-3.7.1.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

	<title>Forgot Password - Whitefields Dental Clinic</title>
</head>

<style>
    .invalidPassword {
        color: red;
    }  
    
    .validPassword {
        color: green;
    }
</style>

<body>
	<div id="overlay" style="display:none;">
		<div id="loader"></div>
	</div>
	<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">		
		<form class="rounded shadow" id="myForm" autocomplete="off" action="" method="post" style="width: 25rem">
			<button type="button" id="backToLogin" class="mt-4 ms-4 btn btn-sm btn-outline-primary disableInputs"><i class="bi bi-arrow-left"></i> Back to Login</button>
			<div class="p-5">
				<div class="d-flex flex-column align-items-center">
					<img src="./resources/images/wfdc-logo-67459a4f483d0.webp" class="mb-3" alt="Logo" width="250" height="80">
					<h1 id="loginLabel" class="fs-4 mb-3">Forgot Password</h1>
					<h6 class="emailBox">Enter your username or email address to proceed.</h6>
					<h6 id="otpCodeBox" class="otpCodeBox">Enter the OTP code that was sent to your email address to proceed.</h6>
					<h6 class="passwordBox">Enter your new password.</h6>
				</div>

				<div id="errorMessage" role="alert"></div>

				<div class="mb-3 form-floating emailBox">
					<input type="text" maxlength="30" class="form-control disableInputs" name="forgotEmail" id="forgotEmail" placeholder="Email Address">
					<label for="forgotEmail">Username or Email Address</label>
				</div>

				
				<div class="mb-3 form-floating otpCodeBox">
					<input type="text" minlength="6" maxlength="6" class="form-control disableInputs" name="otpCode" id="otpCode" placeholder="OTP">
					<label for="otpCode">OTP Code</label>
				</div>		
				
				<div class="passwordBox">
					<div class="mb-3 input-group">
						<div class="form-floating">
							<input type="password" minlength="6" maxlength="20" id="userPassword" autocomplete="off" class="form-control" name="" placeholder="Password">
							<label for="userPassword">New Password</label>
						</div>
						<button class="btn btn-outline-secondary" id="togglePassword" type="button">
							<i id="eyeicon" class="bi bi-eye"></i>
						</button>
					</div>
				</div>
				
				<div class="mb-3" id="passwordValidation">
					<p id="passLength" class="passwordValidate invalidPassword">• Minimum of 6 characters. Max 20.</p>
					<p id="passSymbol" class="passwordValidate validPassword">• Must not include any symbols except _.</p>
					<p id="passLower" class="passwordValidate invalidPassword">• Must use atleast one lower case letter.</p>
					<p id="passUpper" class="passwordValidate invalidPassword">• Must use atleast one upper case letter.</p>
					<p id="passNumber" class="passwordValidate invalidPassword">• Must use atleast one number.</p>
				</div>	
				
				<div class="d-flex flex-column align-items-center mb-3">
					<button type="submit" name="verifyBtn" id="verifyBtn" class="btn p-2 col-12 mb-3 btn-outline-primary disableInputs">CHECK</button>
					<button type="button" disabled id="resendCodeBtn" class="btn btn-sm btn-outline-primary otpCodeBox">Resend New Code</button>
				</div>			

				<!-- <h6 class="fw-bold mb-3">Note: Leaving/Refreshing this page will restart the process.</h6> -->
			</div>
		</form>
	</div>
</body>
</html>

<script src="./resources/js/loginPasswordToggler.js"></script>
<script type="text/javascript" src="./resources/js/functions.js"></script>
<script>
	let emailVerified, otpSent;
	let otpFromUser, otpCode;
	let username = <?php echo isset($_SESSION['username']) ? "'" . $_SESSION['username'] . "'" : "''"; ?>;
	let userEmail = <?php echo isset($_SESSION['email_address']) ? "'" . $_SESSION['email_address'] . "'" : "''"; ?>;
	let hasEmail = <?php echo isset($_SESSION['email_address']) ? "true" : "false"; ?>;

	$(document).ready(function () {
		if (sessionStorage.getItem("timer") !== null) {
			startTimer();
		} else {			
			$("#resendCodeBtn").prop("disabled", false);
		}

		$("#backToLogin").on("click", function () {
			sessionStorage.removeItem("timer");
			$.post("auth/session-end.php", function(data) {});
			location.href = "login.php"
		});

		$("#resendCodeBtn").on("click", function() {
			$("#resendCodeBtn").prop("disabled", true);
			sessionStorage.setItem("timer", 60);
			disableInputs();
			setTimeout(showLoader, 1000);
			setTimeout(sendOTP, 2000);			
			startTimer();
		});

		$("#forgotEmail").focus();
		$(".otpCodeBox").hide();
		$(".passwordBox").hide();
		$(".emailBox").show();
		$("#passwordValidation").hide();

		if (hasEmail) {
				otpSent = true;
				showOtpBox();
			}

		$("#myForm").submit(function(e){
			$("#errorMessage").empty();
			e.preventDefault();
			showLoader();
			$("#forgotEmail").blur();
			$("#otpCode").blur();
			
			if (!emailVerified && !otpSent) {
				$('#myForm').attr('action', 'auth/check-email.php');

				var url = $("#myForm").attr('action');
				var formData = {
					forgotEmail: $("#forgotEmail").val()
				};
	
				$.ajax({
					type: "POST",
					url: url,
					data: formData,
					dataType: "json"
				}).done(function (data) {
					emailChecked = data.emailVerification;
					username = data.username;
					userEmail = $("#forgotEmail").val();
					// console.log(data);
					hideLoader();
					if (!data.success) {
						$("#forgotEmail").focus();
						$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
					} else {
						$("#errorMessage").append('<div class="alert alert-success alert-dismissible fade show alert-dismissible fade show">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
						disableInputs();
						setTimeout(showLoader, 1000);
						setTimeout(sendOTP, 2000);
					}
				}).fail(function(data) {
					// console.log(data);
				});
			}

			if (otpSent) {
				setTimeout(checkOTP, 2000);
			}

			if (emailVerified) {
				$('#myForm').attr('action', 'auth/update-password.php');

				var url = $("#myForm").attr('action');
				var formData = {
					newPass: $("#userPassword").val(),
					userEmail: userEmail
				};

				$.ajax({
					type: "POST",
					url: url,
					data: formData,
					dataType: "json"
				}).done(function (data) {
					// console.log(data);
					hideLoader();
					if (!data.success) {
						$("#otpCode").focus;
						$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
					} else {
						$("#errorMessage").append('<div class="alert alert-success alert-dismissible fade show">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
						$.post("auth/session-end.php", function(data) {});
						disableInputs();
						setTimeout(showLoader, 2000);
						setTimeout(redirectDashboard, 2000);
					}
				}).fail(function(data) {
					// console.log(data);
				});
			}
		});
	});	

	function startTimer() {
		let count = sessionStorage.getItem("timer");
		
		const timer = setInterval(function() {
			count--;
			sessionStorage.setItem("timer", count);
			$("#resendCodeBtn").text("Resend New Code in " + count + " seconds.");
			
			if (count === 0 || count <= 0) {
				clearInterval(timer);
				sessionStorage.removeItem("timer");
				$("#resendCodeBtn").text("Resend New Code");
				$("#resendCodeBtn").prop("disabled", false);
			}
		}, 1000);
	};

	function showPasswordBox() {
		hideLoader();
		$("#loginLabel").text("Update Password");
		$("#errorMessage").empty();
		enableInputs();
		$(".otpCodeBox").hide();
		$(".emailBox").hide();
		$(".passwordBox").show();
		$("#verifyBtn").text("CHANGE");
		$("#verifyBtn").prop('disabled', true);
		$("#passwordValidation").show();
		$("#userPassword").focus();
	}

	function checkOTP() {
		otpFromUser = $("#otpCode").val();

		if (otpFromUser == "") {
			hideLoader();
			$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">OTP is required. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			$("#otpCode").focus();
			emailVerified = false;
		}
		else if (otpFromUser == otpCode) {
			hideLoader();
			disableInputs();
			$("#errorMessage").append('<div class="alert alert-success alert-dismissible fade show">OTP successfully verified.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			emailVerified = true;
			otpSent = false;
			setTimeout(showLoader, 1000);
			setTimeout(showPasswordBox, 2000);
		} else {
			hideLoader();
			$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">OTP code mismatch. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			$("#otpCode").focus();
			emailVerified = false;
		}
	}

	function fetchOTP() {
		var formData = {
			userEmail: userEmail
		};

		$.ajax({
			type: "POST",
			url: "auth/fetch-otp.php",
			data: formData,
			dataType: "json"
		}).done(function (data) {
			otpCode = data.otpCode;
			// console.log(data);
		}).fail(function(data) {
			// console.log(data);
		});
	}

	function sendOTP() {
		var formData = {
			userEmail: userEmail,
			username: username,
			mode: "passwordReset"
		};

		$.ajax({
			type: "POST",
			url: "auth/send-otp.php",
			data: formData,
			dataType: "json"
		}).done(function (data) {
			otpSent = data.otpSent;
			// console.log(data);
			hideLoader();
			if (!data.success) {
				$("#forgotEmail").focus();
				$("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
			} else {				
				sessionStorage.setItem("timer", 60);
				showOtpBox();
			}
		}).fail(function(data) {
			// console.log(data);
		});
	}
	
	setInputFilter(document.getElementById("otpCode"), function(value) {
		return /^-?\d*$/.test(value); }, "Number Only");	

	function showOtpBox() {		
		disableInputs();		
		$("#errorMessage").empty();
		$(".otpCodeBox").show();
		$(".emailBox").hide();
		enableInputs();
		$("#verifyBtn").text("VERIFY");
		$("#otpCode").text("");
		$("#otpCode").focus();
		fetchOTP();
		hideLoader();
		// $("#resendCodeBtn").prop("disabled", true);
	}
</script>

<?php 

 ?>