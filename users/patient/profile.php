<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 2) {
        $id = fetchPatientID();
        
        $fname = $lname = $mname = $email = $username = $age = $bdate = $gender = $religion = $nationality = $contnum = $address = $occupation = "";

        if (is_int($id)) {
            $stmt = $conn->prepare("SELECT pi.lname, pi.fname, pi.mname, pi.suffix, pi.age, pi.contactno, pi.bdate, pi.gender, 
            pi.religion, pi.nationality, pi.occupation, pi.address, ac.email_address, ac.username, dh.prev_dentist, dh.last_dental
            FROM `patient_info` pi
            LEFT OUTER JOIN accounts ac
            ON ac.id = pi.accounts_id
            LEFT OUTER JOIN dental_history dh
            ON dh.patient_id = pi.id
            WHERE pi.id = ?
            ORDER BY dh.last_dental DESC;");
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                $fname = $row['fname'];
                $lname = $row['lname'];
                $mname = $row['mname'];
                $suffix = $row['suffix'];
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
                $prevDentist = $row['prev_dentist'] === null ? "None" : $row['prev_dentist'];
                $lastDental = $row['last_dental'] === null ? "None" : $row['last_dental'];
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
            /* background-color: lightgrey; */
        }

        /* .container-fluid {
            padding: 0 !important;
            width: 100%;
        } */

        /* #content {
            width: 100%;
        } */

        .title {
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

            table tr, .viewAptDetail{
                font-size: 0.8rem;
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="profileLabel">
                        <svg class="bi" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person"/></svg>
                    </h6>
                    <h6 class="ms-2">Profile Information</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="profileClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                                                
                        <form autocomplete="off" action="php/insert-update-info.php" method="POST" class="text-center col" id="myForm">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="lname">Last Name</label>
                                            <input type="text" name="lname" placeholder="Last Name"  id="lname" value="<?php echo $lname ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="fname">First Name</label>
                                            <input type="text" name="fname" placeholder="First Name"  id="fname" value="<?php echo $fname ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="mname">M. Name</label>
                                            <input type="text" name="mname" placeholder="Middle Name" id="mname" value="<?php echo $mname ?? ''; ?>"  class="form-control">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox"<?php echo $mname == "None" ? ' checked="checked"' : ''; ?>>
                                                <label class="ms-1" for="nomname">N/A</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="suffix">Suffix</label>
                                            <input type="text" name="suffix" placeholder="Middle Name" id="suffix" value="<?php echo $suffix ?? ''; ?>"  class="form-control">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nosuffix" name="nosuffix" type="checkbox"<?php echo $suffix == "None" ? ' checked="checked"' : ''; ?>>
                                                <label class="ms-1" for="nosuffix">N/A</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
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
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="contnumber">Contact Number</label>
                                            <input type="text" name="contnumber" placeholder="Contact Number"  id="contnumber" value="<?php echo $contnum ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg">
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="age">Age</label>
                                            <input type="text" name="age" placeholder="Age"  id="age" value="<?php echo $age ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="bdate">Birth Date</label>
                                            <input type="date" name="bdate" placeholder="Code"  id="bdate" value="<?php echo $bdate ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="religion">Religion</label>
                                            <input type="text" name="religion" placeholder="Religion"  id="religion" value="<?php echo $religion ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="nationality">Nationality</label>
                                            <input type="text" name="nationality" placeholder="Nationality"  id="nationality" value="<?php echo $nationality ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="address">Address</label>
                                            <input type="text" name="address" placeholder="Address"  id="address" value="<?php echo $address ?? ''; ?>" class="form-control">
                                        </div>                                             
                                    </div>
                                    
                                    <div class="col-lg">
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
                            </div>
                            <input type="submit" class="btn btn-sm btn-primary btn-md mt-1" value="Submit" name="profileSubmitBtn">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dentalHistoryModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dentalHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="dentalHistoryLabel">
                        <svg class="bi" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person-vcard"/></svg>
                    </h6>
                    <h6 class="ms-2">Dental History</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentalHistoryClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                                                
                        <form autocomplete="off" action="php/insert-dental-history.php" method="POST" class="text-center col" id="dentalHistory">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="prevDentist">Previous Dentist:</label>
                                        <input type="text" required name="prevDentist" placeholder="Name"  id="prevDentist" value="" class="form-control">
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="lastDentalVisit">Last Dental Visit:</label>
                                        <input type="date" required name="lastDentalVisit" id="lastDentalVisit" value="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-sm btn-primary btn-md mt-1" value="Submit" name="dentalHistorySubmitBtn">
                        </form>

                        <div class="table-responsive mt-3" style="height: 200px;">
                            <h5>Past Records</h5>
                            <table id="myTable" class="table-group-divider table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th class="col">ID</th>
                                        <th class="col">Previous Dentist</th>
                                        <th class="col">Last Visit</th>
                                    </tr>
                                </thead>
    
                                <tbody id="tableBody">
                                    <?php
                                        $stmt = $conn->prepare("SELECT * FROM `dental_history`
                                            WHERE patient_id = ?
                                            ORDER BY id DESC;");
                                        $stmt->bind_param('i', $id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result->num_rows > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo '
                                                <tr>
                                                    <td>' . $row['id'] . '</td>
                                                    <td>' . $row['prev_dentist'] . '</td>
                                                    <td>' . $row['last_dental'] . '</td>
                                                </tr>
                                            ';
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="title bg-white d-flex flex-row shadow align-items-center p-3">
                <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                    <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    <svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg>
                </button>
                <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#person"/></svg>
                <h1 class="col">Profile</h1>

                <?php include "../../components/notification.php" ?>
            </div>
            
            <div class="d-flex rounded shadow flex-column mt-3 col-0 col-sm-9 bg-white align-items-center">
                <div id="uploadmessage" class="mt-3" role="alert"></div>

                <div id="profile" class="m-3">
                    <img src="<?php echo $profilePath ? "../../files/{$profilePath}/profile.jpg" : '../../resources/images/blank-profile.webp';?>" alt="" width="300px" height="300px" class="rounded-circle me-2 mb-2 border border-5">
                </div>
                
                <div class="m-3 text-center">
                    <form action="php/upload.php" enctype="multipart/form-data" name="uploadForm" method="POST" class="" id="uploadForm">
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
                    <div class="col-sm-9 rounded shadow bg-white m-3 mb-0 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Personal Information</h1>
                                <div class="col-auto">
                                    <button id="profileEditBtn" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg>
                                    </button>                                
                                </div>
                            </div>

                            <hr>
                            
                            <div class="d-flex justify-content-start row">
                                <div id="errorMessage" class="col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger">Please complete your profile first.</div>' ?>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <h5 class="">Name: <span class="fw-normal"><?php echo $fname ?? ''; ?> <?php echo $mname == "None" ? '' : $mname; ?> <?php echo $lname ?? ''; ?> <?php echo $suffix == "None" ? '' : $suffix; ?> </span></h5>
                                        <h5 class="">Username: <span class="fw-normal"><?php echo $username ?? '';?></span></h5>
                                        <h5 class="">Age: <span class="fw-normal"><?php echo $age ?? '';?></span></h5>
                                        <h5 class="">Birth Date: <span class="fw-normal"><?php echo $bdate ?? '';?></span></h5>
                                        <h5 class="">Gender: <span class="fw-normal"><?php echo $gender ?? '';?></span></h5>
                                        <h5 class="">Occupation: <span class="fw-normal"><?php echo $occupation ?? '';?></span></h5>
                                    </div>
                                    <div class="col-xl">
                                        <h5 class="">Contact Number: <span class="fw-normal"><?php echo $contnum ?? '';?></span></h5>
                                        <h5 class="">Email Address: <span class="fw-normal"><?php echo $email ?? '';?></span></h5>
                                        <h5 class="">Religion: <span class="fw-normal"><?php echo $religion ?? '';?></span></h5>
                                        <h5 class="">Nationality: <span class="fw-normal"><?php echo $nationality ?? '';?></span></h5>
                                        <h5 class="">Address: <span class="fw-normal"><?php echo $address ?? '';?></span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9 rounded shadow bg-white m-3 mb-0 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Dental History</h1>
                                <div class="col-auto">
                                    <button id="" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#dentalHistoryModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg>
                                    </button>                               
                                </div>
                            </div>
                            
                            <hr>

                            <div class="d-flex justify-content-start row">
                                <div id="dentalMessage" class="col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger">Please complete your profile first.</div>' ?>
                                </div>
                                <div class="col-xl">
                                    <h5>Previous Dentist: <span class="fw-normal"><?php echo $prevDentist ?? 'None';?></span></h5>
                                    <h5>Last Dental Visit: <span class="fw-normal"><?php echo  $lastDental != "None" ? date("F d, Y", strtotime($lastDental)) : "None";?></span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9 rounded shadow bg-white m-3 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Medical History</h1>
                                <div class="col-auto">
                                    <button id="" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg>
                                    </button>                                
                                </div>
                            </div>
                            
                            <hr>

                            <div class="d-flex justify-content-start row">
                                <div class="col">
                                    <div class="row">
                                        <h5 class="col-xl-6">Previous Dentist: <span class="fw-normal"></span></h5>
                                        <h5 class="col">Specialty: <span class="fw-normal"></span></h5>
                                    </div>
                                    <div class="row">
                                        <h5 class="col-xl-6">Office Address: <span class="fw-normal"></span></h5>
                                        <h5 class="col">Office Number: <span class="fw-normal"></span></h5>
                                    </div>
                                    <div class="row mt-3">
                                        <h5>Is in good health: <span class="fw-normal"></span></h5>
                                        <h5>Is under medical treatment now: <span class="fw-normal"></span></h5>
                                        <h5>Had serious illness or surgical operation: <span class="fw-normal"></span></h5>
                                        <h5>Had been hospitalized: <span class="fw-normal"></span></h5>
                                        <h5>Is taking prescription/non-prescription medication: <span class="fw-normal"></span></h5>
                                        <h5>Uses tobacco products: <span class="fw-normal"></span></h5>
                                        <h5>Uses alcohol, cocaine, or other dangerous drugs: <span class="fw-normal"></span></h5>
                                        <h5>Is allergic with the following: <span class="fw-normal"></span></h5>

                                        <div class="row d-flex justify-content-start">
                                            <div class="col-auto">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice1" id="choice1">
                                                    <label class="form-check-label" for="choice1">Local Anesthetic (ex. Lidocaine)</label>
                                                </div>
                                            </div>

                                            <div class="col-auto">                                            
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice2" id="choice2">
                                                    <label class="form-check-label" for="choice2">Penicillin, Antibiotics</label>
                                                </div>
                                            </div>

                                            <div class="col-auto">                                            
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice3" id="choice3">
                                                    <label class="form-check-label" for="choice3">Sulfa Drugs</label>
                                                </div>
                                            </div>

                                            <div class="col-auto">                                            
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice4" id="choice4">
                                                    <label class="form-check-label" for="choice4">Aspirin</label>
                                                </div>
                                            </div>                                            

                                            <div class="col-auto">                                            
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice5" id="choice5">
                                                    <label class="form-check-label" for="choice5">Latex</label>
                                                </div>
                                            </div>

                                            <div class="col-auto">                                            
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="choice6" id="choice6">
                                                    <label class="form-check-label" for="choice6">Others: Other Allergic</label>
                                                </div>
                                            </div>
                                        </div>

                                        <h5>Bleeding Time: <span class="fw-normal"></span></h5>
                                        <h5>For women only: <span class="fw-normal"></span></h5>

                                        <div class="row d-flex justify-content-start ms-3">
                                            <h6>Is pregnant: <span class="fw-normal"></span></h6>
                                            <h6>Is nursing: <span class="fw-normal"></span></h6>
                                            <h6>Is taking birth control pills: <span class="fw-normal"></span></h6>
                                        </div>

                                        <h5>Blood Type <span class="fw-normal"></span></h5>
                                        <h5>Blood Pressure <span class="fw-normal"></span></h5>
                                        <h5>Had or have the following: <span class="fw-normal"></span></h5>

                                        <div class="row d-flex justify-content-start">
                                            <div class="col">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice1" id="hasChoice1">
                                                    <label class="form-check-label" for="hasChoice1">High Blood Pressure</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice2" id="hasChoice2">
                                                    <label class="form-check-label" for="hasChoice2">Low Blood Pressure</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice3" id="hasChoice3">
                                                    <label class="form-check-label" for="hasChoice3">Epilepsy / Convulsions</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice4" id="hasChoice4">
                                                    <label class="form-check-label" for="hasChoice4">AIDS or HIV Infection</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice5" id="hasChoice5">
                                                    <label class="form-check-label" for="hasChoice5">Sexually Transmitted Disease</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice6" id="hasChoice6">
                                                    <label class="form-check-label" for="hasChoice6">Stomach Troubles / Ulcers</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice7" id="hasChoice7">
                                                    <label class="form-check-label" for="hasChoice7">Fainting Seizure</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice8" id="hasChoice8">
                                                    <label class="form-check-label" for="hasChoice8">Rapid Weight Loss</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice9" id="hasChoice9">
                                                    <label class="form-check-label" for="hasChoice9">Radiation Therapy</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice10" id="hasChoice10">
                                                    <label class="form-check-label" for="hasChoice10">Joint Replacement / Implant</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice11" id="hasChoice11">
                                                    <label class="form-check-label" for="hasChoice11">Heart Surgery</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice12" id="hasChoice12">
                                                    <label class="form-check-label" for="hasChoice12">Heart Attack</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice13" id="hasChoice13">
                                                    <label class="form-check-label" for="hasChoice13">Thyroid Problem</label>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice14" id="hasChoice14">
                                                    <label class="form-check-label" for="hasChoice14">Heart Disease</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice15" id="hasChoice15">
                                                    <label class="form-check-label" for="hasChoice15">Heart Murmur</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice16" id="hasChoice16">
                                                    <label class="form-check-label" for="hasChoice16">Hepatitis / Liver Disease</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice17" id="hasChoice17">
                                                    <label class="form-check-label" for="hasChoice17">Rheumatic Fever</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice18" id="hasChoice18">
                                                    <label class="form-check-label" for="hasChoice18">Hay Fever / Allergies</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice19" id="hasChoice19">
                                                    <label class="form-check-label" for="hasChoice19">Respiratory Problems</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice20" id="hasChoice20">
                                                    <label class="form-check-label" for="hasChoice20">Hepatitis / Jaundice</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice21" id="hasChoice21">
                                                    <label class="form-check-label" for="hasChoice21">Tuberculosis</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice22" id="hasChoice22">
                                                    <label class="form-check-label" for="hasChoice22">Swollen Ankles</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice23" id="hasChoice23">
                                                    <label class="form-check-label" for="hasChoice23">Kidney Disease</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice24" id="hasChoice24">
                                                    <label class="form-check-label" for="hasChoice24">Diabetes</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice25" id="hasChoice25">
                                                    <label class="form-check-label" for="hasChoice25">Chest Pain</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice26" id="hasChoice26">
                                                    <label class="form-check-label" for="hasChoice26">Stroke</label>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice27" id="hasChoice27">
                                                    <label class="form-check-label" for="hasChoice27">Cancer / Tumors</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice28" id="hasChoice28">
                                                    <label class="form-check-label" for="hasChoice28">Anemia</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice29" id="hasChoice29">
                                                    <label class="form-check-label" for="hasChoice29">Angina</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice30" id="hasChoice30">
                                                    <label class="form-check-label" for="hasChoice30">Asthma</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice31" id="hasChoice31">
                                                    <label class="form-check-label" for="hasChoice31">Emphysema</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice32" id="hasChoice32">
                                                    <label class="form-check-label" for="hasChoice32">Bleeding Problems</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice33" id="hasChoice33">
                                                    <label class="form-check-label" for="hasChoice33">Blood Diseases</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice34" id="hasChoice34">
                                                    <label class="form-check-label" for="hasChoice34">Head Injuries</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice35" id="hasChoice35">
                                                    <label class="form-check-label" for="hasChoice35">Arthritis / Rheumatism</label>
                                                </div>

                                                <div class="form-check me-3">
                                                    <input class="form-check-input history-check" type="checkbox" name="hasChoice36" id="hasChoice36">
                                                    <label class="form-check-label" for="hasChoice36">Other</label>
                                                </div>
                                            </div>
                                        </div>
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

        $('body').on('click', '#profileEditBtn', function(){
            loadDetails();
        });

        function loadDetails() {
            $.ajax({
                type: "POST",
                url: "php/fetch-details.php",
                dataType: 'json'
            }).done(function(data) {
                let details = [data.fname, data.lname, data.age, data.bdate, data.address, data.gender, data.religion, data.nationality, data.contactno, data.occupation];
                let detailsId = ["#fname", "#lname", "#age", "#bdate", "#address", "#gender", "#religion", "#nationality", "#contnumber", "#occupation"];                

                for (let index = 0; index < details.length; index++) {
                    $(detailsId[index]).val(details[index]);
                }

                let fields = [data.mname, data.suffix];
                let nofields = ["#nomname", "#nosuffix"];

                for (let index = 0; index < fields.length; index++) {           
                    if (fields[index] == "None") {
                        $(fields[index]).prop("readonly", true);
                        $(nofields[index]).prop("checked", true);
                    } else {                
                        $(fields[index]).prop("readonly", false);
                        $(nofields[index]).prop("checked", false);
                        $(nofields[index].substring(2)).val(fields[index]);
                    }
                }
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

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
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		});

		$("#dentalHistory").submit(function(e){            
            $("#errorMessage").empty();
			e.preventDefault();

			var url = $("#dentalHistory").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#dentalHistory").serialize(),
                dataType: "json"
			}).done(function (data) {
                localStorage.setItem("dentalMessage", data.message);
                location.reload();
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
        } else if (localStorage.getItem("dentalMessage")){
            let message = localStorage.getItem("dentalMessage");

            $("#dentalMessage").append('<div class="alert alert-success">' + message +  '</div>');

            localStorage.removeItem("dentalMessage")
        }


        $("#nomname, #nosuffix, #nooccupation").click(function() {
            let id =  "#" + $(this).attr('id').substring(2);

            if ($(this).is(":checked")) {
                $(id).prop("readonly", true);
                $(id).val("None");
            } else {                
                $(id).prop("readonly", false);
                $(id).val("");
            }
        });

        $('.history-check').on('click', function() {
            return false;
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