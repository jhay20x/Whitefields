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
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

if (isset($_SESSION['email_address'])) {
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

	<title>Email Verification- Whitefields Dental Clinic</title>
</head>

<body>
	<div id="overlay" style="display:none;">
		<div id="loader"></div>
	</div>
	<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">		
		<form class="rounded shadow" id="myForm" autocomplete="off" action="" method="post" style="width: 25rem">
			<button type="button" id="backToLogin" class="mt-4 ms-4 btn btn-sm btn-outline-primary disableInputs"><i class="bi bi-arrow-left"></i> Back to Login</button>
			<div class="p-5">
				<div class="d-flex flex-column align-items-center">
					<img src="./resources/images/wfdc-logo-67459a4f483d0.webp" alt="Logo" width="250" height="80">
					<h1 class="text-center fs-4">Email Verification</h1>
					<h6>An OTP code was sent to your email.</h6>
					<h6 class="pb-1 mb-3 fw-bold"><?php echo $_SESSION['email_address'] ?></h6>
				</div>

				<div id="errorMessage" role="alert"></div>

				<div>
					<div class="mb-3 input-group">
						<input type="text" minlength="6" maxlength="6" class="form-control disableInputs" name="otpCode" id="otpCode" placeholder="OTP">
					</div>

					<div class="d-flex flex-column align-items-center">
						<button type="submit" name="verifyBtn" id="verifyBtn" class="btn p-2 mb-3 col-12 btn-outline-primary disableInputs">VERIFY</button>
						<h6>
							<button type="button" disabled id="resendCodeBtn" class="btn btn-sm btn-outline-primary">Resend New Code</button>
							<!-- <a id="resendCodeBtn" href="#">Resend New Code</a> -->
						</h6>
					</div>
				</div>
			</div>
		</form>
	</div>
</body>
</html>

<script src="./resources/js/loginPasswordToggler.js"></script>
<script type="text/javascript" src="./resources/js/functions.js"></script>
<script>
	$(document).ready(function () {
		let otpFromUser, otpCode;
		const timer = '';
		// let user_id = <?php // echo isset($_SESSION['user_id']) ? "'" . $_SESSION['user_id'] . "'" : "''"; ?>;
		let username = <?php echo isset($_SESSION['username']) ? "'" . $_SESSION['username'] . "'" : "''"; ?>;
		let userEmail = <?php echo isset($_SESSION['email_address']) ? "'" . $_SESSION['email_address'] . "'" : "''"; ?>;
		
		checkTimer();

		function checkTimer() {
			if (sessionStorage.getItem("timer") !== null) {
				startTimer();
			} else {			
				$("#resendCodeBtn").prop("disabled", false);
			}
		}

		$("#backToLogin").on("click", function () {
			sessionStorage.removeItem("timer");
			clearInterval(timer);
			$.post("auth/session-end.php", function(data) {
				window.location.href = "login.php"
			});
		});

		$("#resendCodeBtn").on("click", function() {
			showLoader();
			$("#errorMessage").empty();
			$("#resendCodeBtn").prop("disabled", true);
			sessionStorage.setItem("timer", 60);
			disableInputs();	
			setTimeout(sendOTP, 2000);
		});
		
		$("#myForm").submit(function(e){
			$("#errorMessage").empty();
			e.preventDefault();
			showLoader();
			disableInputs();
			fetchOTP();
			$("#otpCode").blur();
			setTimeout(checkOTP, 2000);
		});
		
		function startTimer() {
			let count = sessionStorage.getItem("timer");
			
			timer = setInterval(function() {
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

		function checkOTP() {
			otpFromUser = $("#otpCode").val();

			if (otpFromUser == "") {
				hideLoader();
				$("#errorMessage").append('<div class="alert alert-danger">OTP is required. Please try again.</div>');
				enableInputs();
				$("#otpCode").focus();
			}
			else if (otpFromUser == otpCode) {
				hideLoader();
				disableInputs();
				sessionStorage.removeItem("timer");
				clearInterval(timer);
				$("#errorMessage").append('<div class="alert alert-success">Email is successfully verified.</div>');
				setTimeout(showLoader, 1000);
				setTimeout(updateEmailStatus, 2000);
				setTimeout(redirectDashboard, 2500);

			} else {
				hideLoader();
				$("#errorMessage").append('<div class="alert alert-danger">OTP code mismatch. Please try again.</div>');
				$("#otpCode").focus();
			}
		}

		function updateEmailStatus() {
			$.ajax({
				type: "POST",
				url: "auth/update-email-status.php",
				dataType: "json"
			}).done(function (data) {
				//console.log(data);
			}).fail(function(data) {
				//console.log(data);
			});
		}

		function fetchOTP() {
			$.ajax({
				type: "POST",
				url: "auth/fetch-otp.php",
				dataType: "json"
			}).done(function (data) {
				otpCode = data.otpCode;
				//console.log(data);
			}).fail(function(data) {
				//console.log(data);
			});
		}

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
				//console.log(data);
				hideLoader();
				if (!data.success) {
					enableInputs();
					$("#resendCodeBtn").prop("disabled", false);
					$("#forgotEmail").focus();
					$("#errorMessage").append('<div class="alert alert-danger">' + data.error +  '</div>');
				} else {				
					sessionStorage.setItem("timer", 60);
					$("#resendCodeBtn").prop("disabled", true);
					startTimer();
					fetchOTP();
					hideLoader();
					enableInputs();
				}
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		}
		
		setInputFilter(document.getElementById("otpCode"), function(value) {
			return /^-?\d*$/.test(value); }, "Number Only");		
	});	
</script>

<?php 
} else {
    header("Location: ./login.php");
}
// echo $_SESSION['email_address'];
 ?>