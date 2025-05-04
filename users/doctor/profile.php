<?php
session_start();

global $conn;
require_once '../../database/config.php';
require_once 'php/fetch-id.php';

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    function calculateAge($birthdate) {
        $birthDate = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age;
    }

    function fetchProfileDetails($conn, $id) {
        $stmt = $conn->prepare("SELECT di.lname, di.fname, di.mname, di.suffix, di.contactno AS contnum, di.bdate, di.gender, 
            di.religion, di.nationality, di.specialist, di.address, di.about_me, ac.email_address AS email, ac.username
            FROM `dentist_info` di
            LEFT OUTER JOIN accounts ac
            ON ac.id = di.accounts_id
            WHERE di.id = ?;");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        $fields = ["fname", "lname", "mname", "suffix", "gender", "religion", "nationality", "contnum", "address", "specialist", "about_me", "email", "username",];

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [];
            
            foreach ($fields as $value) {
                $data[$value] = $row[$value] ?? null;
            }

            $data["age"] = calculateAge($row["bdate"]);
            $data["bdate"] = date("F d, Y", strtotime($row["bdate"]));

            return $data;
        }
    }

    if ($_SESSION['account_type'] == 3) {
        $id = fetchDentistID();
        
        $profileData = [];

        if (is_int($id)) {
            $profileData = fetchProfileDetails($conn, $id);
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp"/>
    <title>Profile - Whitefields Dental Clinic</title>
    <link rel="stylesheet" href="../../resources/css/bootstrap.css">
    <link rel="stylesheet" href="../../resources/css/sidebar.css">
    <link rel="stylesheet" href="../../resources/css/loader.css">
    <link rel="stylesheet" href="../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        .bi {
            fill: currentColor;
        }

        /* body {
            background-color: lightgrey;
        } */

        /* .container-fluid {
            padding: 0 !important;
            width: 100%;
        } */

        /* #content {
            width: 100%;
        } */

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

        #calendar {
            max-width: 62.5rem;
            margin: 0 auto;
        }

        /* .fc-button {
            font-size: .5em !important;
        }

        .fc-toolbar-title {
            font-size: 1rem !important;
        } */        

        @media only screen and (min-width: 2501px) {            
            .card-text {
                font-size: 1.75rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 2.5rem;
            }
        }

        @media only screen and (min-width: 1201px) and (max-width: 2500px) {
            .card-text {
                font-size: 1.35rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 2.5rem;
            }
        }

        @media only screen and (min-width: 992px) and (max-width: 1200px) {
            .card-text {
                font-size: 1.5rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.5rem;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 900px) {
            .card-text {
                font-size: 1.25rem;
            }

            .card-title {
                font-size: 2.25rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.5rem;
            }
        }

        @media only screen and (max-width: 600px) {
            .fc-button {
                font-size: .4rem !important;
            }

            .fc-toolbar-title {
                font-size: 1rem !important;
            }

            .title h1{
                font-size: 1.5rem !important;
            }

            .title svg{
                width: 1.25rem !important;
            }
            
            .card-text {
                font-size: 1rem;
            }

            .card-title {
                font-size: 2rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.25rem;
            }
            
        }
    </style>
</head>
<body class="bg-body-secondary">    
    <?php include "../../components/sidebar.php" ?>
    
	<div id="overlay" style="display:none;">
		<div id="loader"></div>		
	</div>

    <!-- Modal -->
    <div class="modal fade" id="profileModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="profileLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="profileLabel">
                        <i class="bi bi-calendar3"></i> Profile Information
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="profileClose" aria-label="Close"></button>
                </div>
                <form autocomplete="off" action="php/insert-update-info.php" method="POST" class="text-center col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="fname" placeholder="First Name"  id="fname" class="form-control onlyLetters">
                                        <label for="fname">First Name</label>
                                    </div>
                                </div>
                                
                                <div class="col-0 col-lg-3">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input type="text" name="mname" placeholder="Middle Name" id="mname"  class="form-control onlyLetters">
                                            <label for="mname">Middle Name</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox">
                                            <label class="ms-1" for="nomname">N/A</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="lname" placeholder="Last Name"  id="lname" class="form-control onlyLetters">
                                        <label for="lname">Last Name</label>
                                    </div>
                                </div>
                                    
                                <div class="col-0 col-lg-3">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input type="text" name="suffix" placeholder="Middle Name" id="suffix" class="form-control onlyLetters">
                                            <label for="suffix">Suffix</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nosuffix" name="nosuffix" type="checkbox">
                                            <label class="ms-1" for="nosuffix">N/A</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">                                    
                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="specialist" placeholder="Specialist"  id="specialist" class="form-control onlyLetters">
                                        <label for="specialist">Specialist</label>
                                    </div>
                                </div>

                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="date" name="bdate" placeholder="Birth Date"  id="bdate" class="form-control">
                                        <label for="bdate">Birth Date</label>
                                    </div>
                                </div>

                                <div class="col-0 col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="address" placeholder="Address"  id="address" class="form-control onlyAddress">
                                        <label for="address">Address</label>
                                    </div>                                             
                                </div>
                            </div>

                            <div class="row">                                        
                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="gender" id="gender">
                                            <option disabled selected value="">Select...</option>
                                            <option value="Female">Female</option>
                                            <option value="Male">Male</option>
                                            <!-- <option value="Nonbinary">Nonbinary</option> -->
                                            <!-- <option value="Other">Other</option> -->
                                            <option value="Decline to state">Decline to state</option>
                                        </select>
                                        <label for="gender">Gender</label>
                                    </div>
                                </div>

                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="religion" placeholder="Religion"  id="religion" class="form-control onlyLetters">
                                        <label for="religion">Religion</label>
                                    </div>
                                </div>
                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="nationality" placeholder="Nationality"  id="nationality" class="form-control onlyLetters">
                                        <label for="nationality">Nationality</label>
                                    </div>
                                </div>
                                <div class="col-0 col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="text" maxlength="11" name="contnumber" placeholder="Contact Number"  id="contnumber" class="form-control onlyNumbers">
                                        <label for="contnumber">Contact No.</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-0 col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" maxlength="100" name="aboutme" placeholder="About Me" id="aboutme" class="form-control onlyLettersNumbers">
                                        <label for="aboutme">About Me</label>
                                    </div>                                            
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <input type="submit" class="btn btn-sm btn-outline-primary btn-md mt-1" value="Submit" name="submitbtn">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="emailAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="emailAccountLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="emailAccountLabel">
                        <i class="bi bi-envelope-at"></i> Email Account
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="emailAccountClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="" method="POST" class="col" id="emailAccountFormVerify">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="emailAccountMessage" role="alert"></div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" required disabled data-email-add="<?= empty($profileData['email']) || $profileData['email'] == "None" ? 'Not Set' : $profileData['email'];?>" value="<?= empty($profileData['email']) || $profileData['email'] == "None" ? 'Not Set' : $profileData['email'];?>" name="emailAccount" placeholder="Email Address"  id="emailAccount" class="form-control">
                                        <label for="emailAccount">Email Address</label>
                                    </div>
                                </div>
                                <div id="otpCodeInput" class="col-12 mt-3 d-none">
                                    <div class="form-floating">
                                        <input type="text" maxlength="6" required disabled name="emailAccountOTP" placeholder="OTP Codea"  id="emailAccountOTP" class="form-control onlyNumbers">
                                        <label for="emailAccountOTP">OTP Code</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="emailAccountUpdateBtn">Update</button>
                        <button type="submit" class="btn btn-sm btn-outline-success d-none" id="emailAccountVerifyBtn">Verify</button>
                        <button type="submit" class="btn btn-sm btn-outline-success d-none" disabled id="emailAccountSaveBtn">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="emailAccountCancelBtn" data-bs-toggle="modal" data-bs-target="#cancelEmailChangeModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        
    <!-- Modal -->
    <div class="modal fade" id="cancelEmailChangeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelEmailChangeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelEmailChangeLabel">
                        <i class="bi bi-envelope-at"></i> Email Account Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelEmailChangeClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="emailChangeCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="emailChangeCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#emailAccountModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center position-relative">
            <div class="title position-sticky top-0 start-0 z-3 bg-white d-flex flex-row shadow align-items-center p-3">
                <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                    <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    <i class="bi bi-list"></i>
                </button>
                <h1><i class="bi bi-person"></i></h1>
                <h1 class="col ms-3">Profile</h1>

                <?php include "../../components/notification.php" ?>
            </div>
            
            <div class="d-flex rounded shadow flex-column mt-3 col-0 col-sm-9 bg-white align-items-center">
                <div id="uploadmessage" class="mt-3" role="alert"></div>

                <div id="profile" class="m-3">
                    <img src="<?php echo $profilePath ? "../../files/{$profilePath}/profile.jpg" : '../../resources/images/blank-profile.webp';?>" alt="" width="300px" height="300px" class="rounded-circle mb-2 border border-5">
                </div>
                
                <div class="m-3 text-center">
                    <form action="php/upload.php" enctype="multipart/form-data" name="uploadForm" method="POST" id="uploadForm">
                        <div class="input-group">
                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                            <input class="btn btn-outline-secondary" type="submit" name="uploadsubmitbtn" value="Upload Image" >
                        </div>
                    </form>
                    <small>Note: Profile photo might not reflect immediately.</small>
                </div>
            </div>            
            
            <!-- <div class="col-sm-9 bg-white m-3 p-3 d-flex justify-content-start">
                <div class="col-md-9 col-lg-8 col-xl-6 col-xxl-12 p-3">             -->

            <div id="profileForm">
                <div class="d-flex row justify-content-center">
                    <div class="col-sm-9 rounded shadow  bg-white m-3 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Personal Information</h1>
                                <div class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Email Account">
                                    <button id="emailEditBtn" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#emailAccountModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <i class="bi bi-envelope-at"></i>
                                    </button>                                
                                </div>
                                <div class="col-auto ms-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Profile Information">
                                    <button id="profileEditBtn" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>                                
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-start row">
                                <div id="errorMessage" class="col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger alert-dismissible fade show">Please complete your profile first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>' ?>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <h5>Name: <span class="fw-normal"><?php echo $profileData['fname'] ?? 'Not Set'; ?> <?php echo ($profileData['mname'] ?? '') === "None" || empty($profileData['mname']) ? '' : $profileData['mname']; ?> <?php echo $profileData['lname'] ?? ''; ?> <?php echo ($profileData['suffix'] ?? '') === "None" || empty($profileData['suffix']) ? '' : $profileData['suffix']; ?> </span></h5>
                                        <h5>Username: <span class="fw-normal"><?php echo $profileData['username'] ?? 'Not Set';?></span></h5>
                                        <h5>Specialist: <span class="fw-normal"><?php echo $profileData['specialist'] ?? 'Not Set';?></span></h5>
                                        <h5>Age: <span class="fw-normal"><?php echo $profileData['age'] ?? 'Not Set';?></span></h5>
                                        <h5>Birth Date: <span class="fw-normal"><?php echo $profileData['bdate'] ?? 'Not Set';?></span></h5>
                                        <h5>Gender: <span class="fw-normal"><?php echo $profileData['gender'] ?? 'Not Set';?></span></h5>
                                    </div>
                                    <div class="col-xl">
                                        <h5>Contact Number: <span class="fw-normal"><?php echo $profileData['contnum'] ?? 'Not Set';?></span></h5>
                                        <h5>Email Address: <span class="fw-normal"><?php echo empty($profileData['email']) || $profileData['email'] == "None" ? 'Not Set' : $profileData['email'];?></span></h5>
                                        <h5>Religion: <span class="fw-normal"><?php echo $profileData['religion'] ?? 'Not Set';?></span></h5>
                                        <h5>Nationality: <span class="fw-normal"><?php echo $profileData['nationality'] ?? 'Not Set';?></span></h5>
                                        <h5>Address: <span class="fw-normal"><?php echo $profileData['address'] ?? 'Not Set';?></span></h5>
                                        <h5>About Me: <span class="fw-normal"><?php echo $profileData['about_me'] ?? 'Not Set';?></span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>         
        </div>
    </div>
</body>

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/bootstrap.bundle.min.js"></script>
<script src='../../resources/js/sidebar.js'></script>
<script src='../../resources/js/functions.js'></script>

<script>
    $(document).ready(function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        inputFilters();
        
        $("#emailAccountUpdateBtn").on("click", function() {
            if ($("#emailAccount").val() == "Not Set") {
                $("#emailAccount").prop("disabled", false).val("").focus();
            } else {
                $("#emailAccount").prop("disabled", false).focus();
            }
            $(this).prop("disabled", true).addClass("d-none");
            $("#emailAccountVerifyBtn").prop("disabled", false).removeClass("d-none");
        }); 

        $("#emailChangeCancelYesBtn").on("click", function() {
            $("#emailAccountMessage").empty();
            let email = $("#emailAccount").data("email-add");

            $("#emailAccount").prop("disabled", true).val(email);
            $("#emailAccountUpdateBtn").prop("disabled", false).removeClass("d-none");
            $("#emailAccountSaveBtn").prop("disabled", true).addClass("d-none");
            $("#emailAccountVerifyBtn").prop("disabled", true).addClass("d-none");
            $("#otpCodeInput").addClass("d-none");
            $("#otpCodeInput input").prop("disabled", true);
        });

        $("body").on("submit", "#emailAccountFormVerify", function(e) {
            showLoader();
            $("#emailAccountMessage").empty();
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "php/send-otp.php",
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(data) {
                hideLoader();

                if (data.success) {
                    $('#emailAccountFormVerify').attr('id', 'emailAccountFormSubmit');
                    $("#emailAccountMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $("#emailAccount").prop("disabled", true);
                    $("#otpCodeInput").removeClass("d-none");
                    $("#emailAccountVerifyBtn").prop("disabled", true).addClass("d-none");
                    $("#emailAccountSaveBtn").prop("disabled", false).removeClass("d-none");
                    $("#otpCodeInput input").prop("disabled", false).focus();
                } else {
                    $("#emailAccountMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        });

        $("body").on("submit", "#emailAccountFormSubmit", function(e) {
            showLoader();
            e.preventDefault();
            $("#emailAccountMessage").empty();
            let emailAccount = $("#emailAccount").val();
            $.ajax({
                type: "POST",
                url: "php/verify-otp.php",
                data: $(this).serialize() + '&emailAccount=' + encodeURIComponent(emailAccount),
                dataType: 'json'
            }).done(function(data) {
                hideLoader();

                if (data.success) {
                    $("#emailAccountMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $("#emailAccountFormSubmit").find("input, button").prop("disabled", true);
                    setTimeout(() => {
                        showLoader;
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }, 1000);
                } else {
                    $("#emailAccountMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        });

        $("#uploadForm").on('submit',(function(e) {
            showLoader();
            e.preventDefault();
            $("#errorMessage").empty();
            $("#uploadmessage").empty();

			var url = $("#uploadForm").attr('action');
            
            $.ajax({
                url: url,
                type: "POST",
                data: new FormData(this),
                contentType: false,            
                cache: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    if (!data.success) {
                        $("#uploadmessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
				        hideLoader();
                    } else {
                        localStorage.setItem("uploadmessage", data.message);
                        location.reload();
                    }
                    // console.log(data.responseText);
                },
                error: function(data) {
                    // console.log(data.responseText);
                }
            });
        }));

		$("#myForm").submit(function(e){            
            showLoader();
            $("#errorMessage").empty();
			e.preventDefault();

			var url = $("#myForm").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#myForm").serialize(),
                dataType: "json"
			}).done(function (data) {
                localStorage.setItem("errorMessage", data.message);
                location.reload();

				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		});
        
        if (localStorage.getItem("uploadmessage")) {
            let message = localStorage.getItem("uploadmessage");

            $("#uploadmessage").append('<div class="alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

            localStorage.removeItem("uploadmessage");
        } else if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");

            $("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

            localStorage.removeItem("errorMessage");
        }

        $('body').on('click', '#profileEditBtn', function(){
            loadDetails();
        });

        function loadDetails() {
            showLoader();
            $.ajax({
                type: "POST",
                url: "php/fetch-details.php",
                dataType: 'json'
            }).done(function(data) {
                hideLoader();
                let details = [data.fname, data.lname, data.specialist, data.bdate, data.address, data.gender, data.religion, data.nationality, data.contactno, data.about_me];
                let detailsId = ["#fname", "#lname", "#specialist", "#bdate", "#address", "#gender", "#religion", "#nationality", "#contnumber", "#aboutme"];                

                for (let index = 0; index < details.length; index++) {
                    $(detailsId[index]).val(details[index]);
                }

                let fields = [data.mname, data.suffix];
                let nofields = ["#nomname", "#nosuffix"];

                for (let index = 0; index < fields.length; index++) {
                    if (fields[index] == "None") {
                        $(nofields[index]).prop("checked", true);
                        $(`#${nofields[index].substring(3)}`).prop("readonly", true);
                        $(`#${nofields[index].substring(3)}`).val("None");
                    } else {                
                        $(nofields[index]).prop("checked", false);
                        $(`#${nofields[index].substring(3)}`).prop("readonly", false);
                        $(`#${nofields[index].substring(3)}`).val(fields[index]);
                    }
                }
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

        $("#nomname, #nosuffix").click(function() {
            let id =  "#" + $(this).attr('id').substring(2);

            if ($(this).is(":checked")) {
                $(id).prop("readonly", true);
                $(id).val("None");
            } else {                
                $(id).prop("readonly", false);
                $(id).val("");
            }
        });
	});

</script>
</html>

<?php 

    
    } else {
        header("Location: ../../login.php");
    }
}else {
    session_unset();
    header("Location: ../../login.php");
}
?>