<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

// echo $_SESSION['account_type'];

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

// $tablename = "qwert12345asdfg6789";
// // mkdir("../../files/".$tablename);
// $errors= array();
// $file_name = $_FILES['fileToUpload']['name'];
// $file_tmp =$_FILES['fileToUpload']['tmp_name'];
// $extensions= array("jpeg","jpg","png");
// move_uploaded_file($file_tmp,"../../files/".$tablename."/".$file_name);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 2) {
        if (is_int(fetchPatientID())) {
            $hasId = true;
        } else {
            $hasId = false;
        }
        
        if ($hasId) {        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp"/>
    <title>Dashboard - Whitefields Dental Clinic</title>
    <link rel="stylesheet" href="../../resources/css/bootstrap.css">
    <link rel="stylesheet" href="../../resources/css/sidebar.css">
    
    <style>
        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        body {
            /* background-color:lightgrey; */
        }

        .title {
            background-color: white;
            margin-top: 20px;
            padding: 5rem;
            width: 100%;
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
        }


        @media only screen and (min-width: 2501px) {

        }

        @media only screen and (min-width: 1201px) and (max-width: 2500px) {

        }

        @media only screen and (min-width: 992px) and (max-width: 1200px) {

        }

        @media only screen and (min-width: 768px) and (max-width: 900px) {

        }

        @media only screen and (max-width: 600px) {
            .title h1{
                font-size: 1.5rem !important;
            }

            .title svg{
                width: 1.25rem !important;
            }            
        }
    </style>
</head>
<body class="bg-body-secondary">    
    <?php include "../../components/sidebar.php" ?>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center position-relative">
            <div class="title position-sticky top-0 start-0 z-3 bg-white d-flex flex-row shadow align-items-center p-3">
                <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                    <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    <svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg>
                </button>
                <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#columns-gap"/></svg>
                <h1 class="col">Dashboard: Patient</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div id="appointmentForm" class="d-flex justify-content-center">
                <div class="col col-sm-9">
                    <div class="row bg-white rounded shadow mt-3 p-3 d-flex justify-content-center row">
                        <div id="errorMessage" class="" role="alert">
                            <?php echo $hasId ? '' : '<div class="alert alert-danger">Please complete your profile first.</div>' ?>
                        </div>

                        <div class="row">
                            <h4 class="row">Announcements:</h4>
                            <span class="row">No new announcements.</span>
                        </div>
                    </div>

                    <div class="row bg-white rounded shadow mt-3 p-3 d-flex justify-content-center row">
                        <div class="row">                                    
                            <h4 class="row">Upcoming Appointments:</h4>
                            <span class="row">No upcoming appointments.</span>
                        </div>
                    </div>

                    <div class="row bg-white rounded shadow mt-3 p-3 d-flex justify-content-center row">
                        <div class="row">                                    
                            <h4 class="row">Ongoing Procedures:</h4>
                            <span class="row">No ongoing procedures.</span>
                        </div>
                    </div>

                    <div class="row bg-white rounded shadow mt-3 p-3 d-flex justify-content-center row">
                        <div class="row">                                    
                            <h4 class="row">Last Clinic Visit:</h4>
                            <span class="row">You haven't visited the clinic yet.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</body>

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/bootstrap.bundle.min.js"></script>
<script src='../../resources/js/index.global.js'></script>
<script src='../../resources/js/sidebar.js'></script>

<script>
    $(document).ready(function () {
		$("#myForm").submit(function(e){
			$("#errorMessage").empty();
			e.preventDefault();

			// $("#loginUserEmail, #userPassword, #signUpUsername, #signUpEmail").blur;

			var url = $("#myForm").attr('action');

            var formData = {
                timeHour: $('#timeHour').val(),
                timeMinute: $('#timeMinute').val(),
                ampmText: $('#ampmText').text(),
                date: $('#date').val(),
                dentist: $('#dentist').val(),
                concern: $('#concern').val()
            };

            // console.log($("form").serialize());

			$.ajax({
				type: "POST",
				url: url,
				data: formData,
                dataType: 'json'                
			}).done(function (data) {
                if (!data.success) {
                    $("#errorMessage").append('<div class="alert alert-danger">' + data.error +  '</div>');
                } else {                    
                    $("#date, #concern").val("");
                    $("#ampmText").text("--");
                    $('#dentist, #timeHour, #timeMinute').prop('selectedIndex', 0);
                    $("#errorMessage").append('<div class="alert alert-success">' + data.message +  '</div>');
                }
				// console.log(formData);
				//console.log(data);
			}).fail(function(data) {
				// console.log(formData);
				//console.log(data.responseText);
			});
		});

        $("#timeHour").on('change', function() {
            let hour = $("#timeHour").val();

            if (hour >= 9 && hour <= 11) {
                $("#ampmText").text("AM");
            }

            if (hour == 12 || hour >= 1 && hour <= 6){
                $("#ampmText").text("PM");
            }
        });

	});

</script>
</html>

<?php 
        } else {
            header("Location: profile.php");            
        }
    } else {
        header("Location: ../../login.php");
    }
} else {
    session_unset();
	header("Location: ../../login.php");
}
?>