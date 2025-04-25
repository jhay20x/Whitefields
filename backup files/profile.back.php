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
    if ($_SESSION['account_type'] == 3) {
        $id = fetchDentistID();

        $fname = "";
        $lname = "";
        $mname = "";
        $specialist = "";
        $bdate = "";
        $gender = "";
        $religion = "";
        $nationality = "";
        $contnum = "";
        $address = "";
        $aboutme = "";

        if (is_int($id)) {
            $stmt = $conn->prepare("SELECT * FROM `dentist_info` WHERE `id` = ?;");
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();            
    
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                
                $fname = $row['fname'];
                $lname = $row['lname'];
                $mname = $row['mname'];
                $specialist = $row['specialist'];
                $bdate = $row['bdate'];
                $gender = $row['gender'];
                $religion = $row['religion'];
                $nationality = $row['nationality'];
                $contnum = $row['contactno'];
                $address = $row['address'];
                $aboutme = $row['about_me'];
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
    
    <style>
        .bi {
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

    <div class="container-fluid">
        <div class="row d-flex justify-content-center">

        <!-- <div id="content">
            <div class="d-flex justify-content-center align-items-center border" style="min-height: 100vh;">
                <div class="p-5 rounded shadow">
                    <p class="display-1 fw-bold">Welcome <?php echo $_SESSION['user_username']; ?></p>
                    <a href="../../auth/logout.php">
                        <button class="btn btn-outline-primary col-12">Logout</button>
                    </a>
                </div>
            </div>
        </div> -->
            
            <div class="title d-flex flex-row align-items-center p-3">
                <div class="col-xs-1">
                    <button id="" class="sidebarCollapse btn btn-outline-secondary me-4"><i class="bi bi-list"></i></button>
                </div>

                <div class="col-xs-1">
                    <i class="bi bi-columns-gap"></i>
                </div>

                <div class="col-xs-auto">
                    <h1>Profile</h1>
                </div>
            </div>
            
            <div class="d-flex flex-column mt-3 col-0 col-sm-9 bg-white align-items-center">
                <div id="errorMessage" class="mt-3" role="alert"></div>

                <div id="profile" class="m-3">
                    <img src="<?php echo $profilePath ? "../../files/" . $profilePath : '../../resources/images/blank-profile.webp';?>" alt="" width="300px" height="300px" class="rounded-circle me-2 mb-2 border border-5">
                </div>
                
                <div id="form" class="m-3">
                    <form action="php/upload.php" enctype="multipart/form-data" name="uploadForm" method="POST" class="text-center" id="uploadForm">
                        <div class="input-group">
                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                            <input class="btn btn-outline-secondary" type="submit" name="uploadsubmitbtn" value="Upload Image" >
                        </div>
                    </form>
                </div>
            </div>

            

            <div id="profileForm">
                <div class="d-flex row justify-content-center">
                    <div class="col-0 col-sm-9 bg-white my-3 p-3 d-flex justify-content-center">
                        <div class="col-11 col-md-9 col-lg-8 col-xl-6 col-xxl-3 my-3">
                            <h1>Personal Information (Lagyan nyo lahat)</h1>
                            <form autocomplete="off" action="php/insert-update-info.php" method="POST" class="text-center" id="myForm">
                                <div class="mt-4">
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="fname">First Name</label>
                                        <input type="text" name="fname" placeholder="First Name"  id="fname" id="fname" value="<?php echo $fname ? $fname : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group d-flex align-items-center my-3">
                                        <label class="input-group-text" for="mname">Middle Name</label>
                                        <input type="text" name="mname" placeholder="Middle Name"  id="mname" value="<?php echo $mname ? $mname : ''; ?>"  class="form-control">
                                        <span class="ms-2">
                                            <input class="input-group-addon" id="nomname" name="nomname" type="checkbox"<?php echo $mname == "None" ? ' checked="checked"' : ''; ?>>
                                            <label for="nomname">No Middle Name</label>
                                        </span>
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="lname">Last Name</label>
                                        <input type="text" name="lname" placeholder="Last Name"  id="lname" value="<?php echo $lname ? $lname : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="specialist">Specialist</label>
                                        <input type="text" name="specialist" placeholder="Specialist"  id="specialist" value="<?php echo $specialist ? $specialist : ''; ?>" class="form-control">
                                    </div>

                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="bdate">Birth Date</label>
                                        <input type="date" name="bdate" placeholder="Code"  id="bdate" value="<?php echo $bdate ? $bdate : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3">
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
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="religion">Religion</label>
                                        <input type="text" name="religion" placeholder="Religion"  id="religion" value="<?php echo $religion ? $religion : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="nationality">Nationality</label>
                                        <input type="text" name="nationality" placeholder="Nationality"  id="nationality" value="<?php echo $nationality ? $nationality : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="contnumber">Contact Number</label>
                                        <input type="text" name="contnumber" placeholder="Contact Number"  id="contnumber" value="<?php echo $contnum ? $contnum : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="address">Address</label>
                                        <input type="text" name="address" placeholder="Address"  id="address" value="<?php echo $address ? $address : ''; ?>" class="form-control">
                                    </div>

                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="aboutme">About Me</label>
                                        <input type="text" name="aboutme" placeholder="About Me"  id="aboutme" value="<?php echo $aboutme ? $aboutme : ''; ?>" class="form-control">
                                    </div>
                                    
                                    <!-- <div class="input-group d-flex align-items-center my-3">
                                        <label class="input-group-text" for="occupation">Occupation</label>
                                        <input type="text" name="occupation" placeholder="Occupation"  id="occupation" id="occupation" class="form-control">
                                        <div class="form-check ms-4">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                No Occupation (Di pa funtional)
                                            </label>
                                        </div>
                                    </div> -->
                                    
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
                                </div>
                                
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

                                <input type="submit" class="btn btn-outline-primary btn-md mt-1" value="Submit" name="submitbtn">
                            </form>
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

<script>
    $(document).ready(function () {
        $("#uploadForm").on('submit',(function(e) {
            e.preventDefault();
            $("#errorMessage").empty();

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
                        $("#errorMessage").append('<div class="alert alert-danger">' + data.error +  '</div>');
                    } else {
                        localStorage.setItem("errordiv", data.message);
                        location.reload();
                    }
                    //console.log(data);
                },
                error: function(data) {
                    //console.log(data);
                }
            });
        }));

		$("#myForm").submit(function(e){
			e.preventDefault();

			// $("#loginUserEmail, #userPassword, #signUpUsername, #signUpEmail").blur;

			var url = $("#myForm").attr('action');
			// var formData = {
			// 	loginUserEmail: $("#loginUserEmail").val(),
			// 	password: $("#userPassword").val(),
			// 	signUpUsername: $("#signUpUsername").val(),
			// 	signUpEmail: $("#signUpEmail").val(),
			// };

            // console.log($("form").serialize());

			$.ajax({
				type: "POST",
				url: url,
				data: $("form").serialize()
			}).done(function (data) {
                // $("#lname, #fname, #mname, #contnumber, #bdate, #specialist, #religion, #nationality, #aboutme, #address").val("");
                // $('#gender').prop('selectedIndex', 0);
                // $('#mname').prop('readonly', false);
                // $("#nomname").prop('checked', false);
				//console.log(data);
			}).fail(function(data) {
				//console.log(data);
			});
		});
        
        if (localStorage.getItem("errordiv")) {
            let message = localStorage.getItem("errordiv");

            $("#errorMessage").append('<div class="alert alert-success">' + message +  '</div>');

            localStorage.removeItem("errordiv")
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

        if ($("#nomname").is(":checked")) {
            $("#mname").prop("readonly", true);
            $("#mname").val("None");
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