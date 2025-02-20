<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 1) {
        $id = fetchSecretaryID();
        
        $fname = $lname = $mname = $email = $username = $age = $bdate = $gender = $religion = $nationality = $contnum = $address = $occupation = "";


        if (is_int($id)) {
            $stmt = $conn->prepare("SELECT si.lname, si.fname, si.mname, si.age, si.contactno, si.bdate, si.gender, 
            si.religion, si.nationality, si.occupation, si.address, ac.email_address, ac.username
            FROM `secretary_info` si
            LEFT OUTER JOIN accounts ac
            ON ac.id = si.accounts_id
            WHERE si.id = ?");
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $result = $stmt->get_result();
            
    
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                
                $fname = $row['fname'];
                $lname = $row['lname'];
                $mname = $row['mname'];
                $age = $row['age'];
                $bdate = $row['bdate'];
                $gender = $row['gender'];
                $religion = $row['religion'];
                $nationality = $row['nationality'];
                $contnum = $row['contactno'];
                $address = $row['address'];
                $occupation = $row['occupation'];
                $email = $row['email_address'];
                $username = $row['username'];
            }
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
    
    <style>
        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        body {
            background-color: lightgrey;
        }

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
<body>    
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
                        <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                    </h6>
                    <h6 class="ms-2">Profile Information</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="profileClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                                                
                        <form autocomplete="off" action="php/insert-update-info.php" method="POST" class="text-center col" id="myForm">
                            <div class="row">
                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="fname">First Name</label>
                                        <input type="text" name="fname" placeholder="First Name"  id="fname" value="<?php echo $fname ?? ''; ?>" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="mname">M. Name</label>
                                        <input type="text" name="mname" placeholder="Middle Name" id="mname" value="<?php echo $mname ?? ''; ?>"  class="form-control">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox"<?php echo $mname == "None" ? ' checked="checked"' : ''; ?>>
                                            <label class="ms-1" for="nomname">N/A</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="lname">Last Name</label>
                                        <input type="text" name="lname" placeholder="Last Name"  id="lname" value="<?php echo $lname ?? ''; ?>" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">                                    
                                <div class="col-0 col-lg-2">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="age">Age</label>
                                        <input type="text" name="age" placeholder="Age"  id="age" value="<?php echo $age ?? ''; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-0 col-lg-3">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="bdate">Birth Date</label>
                                        <input type="date" name="bdate" placeholder="Code"  id="bdate" value="<?php echo $bdate ?? ''; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-0 col-lg-6">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="address">Address</label>
                                        <input type="text" name="address" placeholder="Address"  id="address" value="<?php echo $address ?? ''; ?>" class="form-control">
                                    </div>                                             
                                </div>
                            </div>

                            <div class="row">                                        
                                <div class="col-0 col-lg-3">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="gender">Gender</label>
                                        <select class="form-select" name="gender" id="gender">
                                            <option disabled selected value="">Select...</option>
                                            <option value="Female"<?php echo ($gender == "Female") ? ' selected="selected"' : ''; ?>>Female</option>
                                            <option value="Male"<?php echo ($gender == "Male") ? ' selected="selected"' : ''; ?>>Male</option>
                                            <option value="Nonbinary"<?php echo ($gender == "Nonbinary") ? ' selected="selected"' : ''; ?>>Nonbinary</option>
                                            <!-- <option value="Other">Other</option> -->
                                            <option value="Decline to state"<?php echo ($gender == "Decline to state") ? ' selected="selected"' : ''; ?>>Decline to state</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="religion">Religion</label>
                                        <input type="text" name="religion" placeholder="Religion"  id="religion" value="<?php echo $religion ?? ''; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-0 col-lg-3">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="nationality">Nationality</label>
                                        <input type="text" name="nationality" placeholder="Nationality"  id="nationality" value="<?php echo $nationality ?? ''; ?>" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">                                        
                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="contnumber">Contact Number</label>
                                        <input type="text" name="contnumber" placeholder="Contact Number"  id="contnumber" value="<?php echo $contnum ?? ''; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-0 col-lg-4">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="occupation">Occupation</label>
                                        <input type="text" name="occupation" placeholder="Occupation" id="occupation" value="<?php echo $occupation ?? ''; ?>" class="form-control">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nooccupation" name="nooccupation" type="checkbox"<?php echo $occupation == "None" ? ' checked="checked"' : ''; ?>>
                                            <label class="ms-1" for="nooccupation">N/A</label>
                                        </div>
                                    </div>                                            
                                </div>
                            </div>
                            
                            
                            
                            
                            <!-- <div class="input-group my-3">
                                <label class="input-group-text" for="prodName">Religion</label>
                                <select class="form-select" aria-label="Time">
                                    <option disabled selected value="">...</option>
                                    <option value="">Catholic</option>
                                    <option value="">Iglesia ni Cristo</option>
                                    <option value="">Nonbinary</option>
                                    <option value="">Other</option>
                                    <option value="">Decline to state</option>
                                </select>
                            </div> -->
                        
                            <!-- <div class="mt-4">
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="fname">First Name</label>
                                    <input required type="text" name="fname" placeholder="First Name"  id="fname" aria-label="Product SRP" id="fname" class="form-control">
                                </div>
                                
                                <div class="input-group d-flex align-items-center my-3">
                                    <label class="input-group-text" for="mname">Middle Name</label>
                                    <input required type="text" name="mname" placeholder="Middle Name"  id="mname" aria-label="Product SRP" id="mname" class="form-control">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            No Middle Name (Di pa funtional)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="lname">Last Name</label>
                                    <input required type="text" name="lname" placeholder="Last Name"  id="lname" aria-label="Product SRP" id="lname" class="form-control">
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="age">Age</label>
                                    <input required type="text" name="age" placeholder="Age"  id="age" aria-label="Product SRP" id="age" class="form-control">
                                </div>

                                <div class="input-group my-3">
                                    <label class="input-group-text" for="bdate">Birth Date</label>
                                    <input required type="date" name="bdate" placeholder="Code"  id="bdate" aria-label="Product Code" id="bdate" class="form-control">
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="gender">Gender</label>
                                    <select required class="form-select" id="gender">
                                        <option disabled selected value="">...</option>
                                        <option value="">Female</option>
                                        <option value="">Male</option>
                                        <option value="">Nonbinary</option>
                                        <option value="">Other</option>
                                        <option value="">Decline to state</option>
                                    </select>
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="religion">Religion</label>
                                    <input required type="text" name="religion" placeholder="Religion"  id="religion" aria-label="Product SRP" id="religion" class="form-control">
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="nationality">Nationality</label>
                                    <input required type="text" name="nationality" placeholder="Nationality"  id="nationality" aria-label="Product SRP" id="nationality" class="form-control">
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="contnumber">Contact Number</label>
                                    <input required type="text" name="contnumber" placeholder="Contact Number"  id="contnumber" aria-label="Product SRP" id="contnumber" class="form-control">
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="address">Address</label>
                                    <input required type="text" name="address" placeholder="Address"  id="address" aria-label="Product SRP" id="address" class="form-control">
                                </div>
                                
                                <div class="input-group d-flex align-items-center my-3">
                                    <label class="input-group-text" for="occupation">Occupation</label>
                                    <input required type="text" name="occupation" placeholder="Occupation"  id="occupation" aria-label="Product SRP" id="occupation" class="form-control">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            No Occupation (Di pa funtional)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="prodName">Religion</label>
                                    <select class="form-select" aria-label="Time">
                                        <option disabled selected value="">...</option>
                                        <option value="">Catholic</option>
                                        <option value="">Iglesia ni Cristo</option>
                                        <option value="">Nonbinary</option>
                                        <option value="">Other</option>
                                        <option value="">Decline to state</option>
                                    </select>
                                </div>
                            </div> -->

                            <input type="submit" class="btn btn-primary btn-md mt-1" value="Submit" name="submitbtn">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center">

        <!-- <div id="content">
            <div class="d-flex justify-content-center align-items-center border" style="min-height: 100vh;">
                <div class="p-5 rounded shadow">
                    <p class="display-1 fw-bold">Welcome <?php echo $_SESSION['user_username']; ?></p>
                    <a href="../../auth/logout.php">
                        <button class="btn btn-primary col-12">Logout</button>
                    </a>
                </div>
            </div>
        </div> -->

            <div class="title d-flex flex-row align-items-center p-3">
                <div>
                    <button id="" class="sidebarCollapse btn btn-outline-secondary me-4"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg></button>
                </div>

                <div>
                    <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#columns-gap"/></svg>
                </div>

                <div>
                    <h1>Profile</h1>
                </div>
            </div>
            
            <div class="d-flex flex-column mt-3 col-0 col-sm-9 bg-white align-items-center">
                <div id="uploadmessage" class="mt-3" role="alert"></div>

                <div id="profile" class="m-3">
                    <img src="<?php echo $profilePath ? "../../files/{$profilePath}/profile.jpg" : '../../resources/images/blank-profile.webp';?>" alt="" width="300px" height="300px" class="rounded-circle me-2 mb-2 border border-5">
                </div>
                
                <div class="m-3 text-center">
                    <form action="php/upload.php" enctype="multipart/form-data" name="uploadForm" method="POST" class="text-center" id="uploadForm">
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
                    <div class="col-sm-9 bg-white m-3 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Personal Information</h1>
                                <div class="col-auto">
                                    <button id="" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#pencil-square"/></svg>
                                    </button>                                
                                </div>
                            </div>

                            <div class="d-flex justify-content-start row">
                                <div id="errorMessage" class="mt-3 col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="alert alert-danger">Please complete your profile first.</div>' ?>
                                </div>

                                <div class="col">
                                    <h5>Name: <span class="fw-normal"><?php echo $fname ?? ''; ?> <?php echo $mname == "None" ? '' : $mname; ?> <?php echo $lname ?? ''; ?></span></h5>
                                    <h5>Username: <span class="fw-normal"><?php echo $username ?? '';?></span></h5>
                                    <h5>Age: <span class="fw-normal"><?php echo $age ?? '';?></span></h5>
                                    <h5>Birth Date: <span class="fw-normal"><?php echo $bdate ?? '';?></span></h5>
                                    <h5>Gender: <span class="fw-normal"><?php echo $gender ?? '';?></span></h5>
                                    <h5>Contact Number: <span class="fw-normal"><?php echo $contnum ?? '';?></span></h5>
                                    <h5>Email Address: <span class="fw-normal"><?php echo $email ?? '';?></span></h5>
                                    <h5>Religion: <span class="fw-normal"><?php echo $religion ?? '';?></span></h5>
                                    <h5>Nationality: <span class="fw-normal"><?php echo $nationality ?? '';?></span></h5>
                                    <h5>Address: <span class="fw-normal"><?php echo $address ?? '';?></span></h5>
                                    <h5>Occupation: <span class="fw-normal"><?php echo $occupation ?? '';?></span></h5>
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
                        $("#uploadmessage").append('<div class="alert alert-danger">' + data.error +  '</div>');
				        hideLoader();
                    } else {
                        localStorage.setItem("uploadmessage", data.message);
                        location.reload();
                    }
                    console.log(data.responseText);
                },
                error: function(data) {
                    console.log(data.responseText);
                }
            });
        }));

		$("#myForm").submit(function(e){            
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
                // $("#lname, #fname, #mname, #contnumber, #bdate, #age, #gender, #religion, #nationality, #occupation, #address").val("");
                // $('#gender').prop('selectedIndex', 0);
                // $('#mname').prop('readonly', false);
                // $("#nomname, #nooccupation").prop('checked', false);
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		});
        
        if (localStorage.getItem("uploadmessage")) {
            let message = localStorage.getItem("uploadmessage");

            $("#uploadmessage").append('<div class="alert alert-success">' + message +  '</div>');

            localStorage.removeItem("uploadmessage")
        } else if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");

            $("#errorMessage").append('<div class="alert alert-success">' + message +  '</div>');

            localStorage.removeItem("errorMessage")
        }

        $("#nomname").click(function() {
            if ($("#nomname").is(":checked")) {
                $("#mname").prop("readonly", true);
                $("#mname").val("None");
            } else {                
                $("#mname").prop("readonly", false);
                $("#mname").val("");
            }

        });

        $("#nooccupation").click(function() {
            if ($("#nooccupation").is(":checked")) {
                $("#occupation").prop("readonly", true);
                $("#occupation").val("None");
            } else {                
                $("#occupation").prop("readonly", false);
                $("#occupation").val("");
            }

        });

        if ($("#nomname").is(":checked")) {
            $("#mname").prop("readonly", true);
            $("#mname").val("None");
        };
        
        if ($("#nooccupation").is(":checked")) {
            $("#occupation").prop("readonly", true);
            $("#occupation").val("None");
        };
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