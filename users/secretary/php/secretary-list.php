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
    if ($_SESSION['account_type'] == 1) {
        $id = fetchSecretaryID();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp"/>
    <title>Secretary Accounts - Whitefields Dental Clinic</title>
    <link rel="stylesheet" href="../../resources/css/bootstrap.css">
    <link rel="stylesheet" href="../../resources/css/sidebar.css">
    <link rel="stylesheet" href="../../resources/css/loader.css">
    <link rel="stylesheet" href="../../resources/css/jquery-ui.css">
    <link rel="stylesheet" href="../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../resources/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="../../resources/css/buttons.bootstrap5.css">
    <link rel="stylesheet" href="../../resources/css/searchPanes.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/select.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/buttons.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/searchBuilder.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/dataTables.dateTime.css" />
    
    <style>
        .bi {
            fill: currentColor;
        }

        body {
            /* background-color: lightgrey; */
        }

        /* .container-fluid {
            padding: 0 !important;
            width: 100%;
        }

        #content {
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
        
        .invalidPassword {
            color: red;
        }  
        
        .validPassword {
            color: green;
        }

        @media only screen and (max-width: 600px) {

            .title h1 {
                font-size: 1rem !important;
            }

            .title svg {
                width: 1.25rem !important;
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
    <div class="modal fade" id="secretaryViewModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="secretaryViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-file-medical"></i> Medical Information
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="secretaryViewClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="accordion" id="secretaryView">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" id="secretaryViewBtn" type="button" data-bs-toggle="collapse" data-bs-target="#patientInfo" aria-expanded="true" aria-controls="patientInfo">
                                        <span class="fw-semibold">Personal Information</span>
                                    </button>
                                </h2>
                                <div id="patientInfo" class="accordion-collapse collapse show" data-bs-parent="#secretaryView">
                                    <div class="accordion-body">
                                        <div class="col-12 col-sm">
                                            <h6>Name: <span id="" class="fw-normal patientName"></span></h6>
                                            <h6>Age: <span id="" class="fw-normal patientAge"></h6>
                                            <h6>Birth Date: <span id="patientBdate" class="fw-normal"></span></h6>
                                            <h6>Gender: <span id="" class="fw-normal patientGender"></span></h6>
                                            <h6>Religion: <span id="patientReligion" class="fw-normal"></span></h6>
                                            <h6>Occupation: <span id="patientOccupation" class="fw-normal"></span></h6>
                                        </div>
                                        <div class="col-12 col-sm">
                                            <h6>Username: <span id="patientUsername" class="fw-normal"></span></h6>
                                            <h6>Email Address: <span id="patientEmail" class="fw-normal"></span></h6>
                                            <h6>Contact Number: <span id="patientContact" class="fw-normal"></span></h6>
                                            <h6>Nationality: <span id="patientNationality" class="fw-normal"></span></h6>
                                            <h6>Address: <span id="patientAddress" class="fw-normal"></span></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dentalInfo" aria-expanded="false" aria-controls="dentalInfo">
                                        <span class="fw-semibold">Dental History</span>
                                    </button>
                                </h2>
                                <div id="dentalInfo" class="accordion-collapse collapse" data-bs-parent="#secretaryView">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-end float-end">
                                            <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Records">
                                                <button id="" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#dentalHistoryLogsModal">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm">
                                            <h6>Previous Dentist: <span id="patientPrevDentist" class="fw-normal"></span></h6>
                                            <h6>Last Dental Visit: <span id="patientLastVisit" class="fw-normal"></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#medicalInfo" aria-expanded="false" aria-controls="medicalInfo">
                                        <span class="fw-semibold">Medical History</span>
                                    </button>
                                </h2>
                                <div id="medicalInfo" class="accordion-collapse collapse" data-bs-parent="#secretaryView">
                                    <div class="accordion-body">
                                        <div class="row justify-content-end">
                                            <div class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Records">
                                                <button id="" class="btn btn-outline-secondary mb-3 position-relative" data-bs-toggle="modal" data-bs-target="#medicalHistoryLogsModal">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="justify-content-start row">
                                            <div class="col">
                                                <table id="medicalTable" class="table-group-divider table-hover table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="fw-semibold">Name of Physician:</td>
                                                            <td id="physician_name"></td>
                                                            <td class="fw-semibold">Speciality:</td>
                                                            <td id="speciality"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-semibold">Office Address:</td>
                                                            <td id="office_address"></td>
                                                            <td class="fw-semibold">Office Number:</td>
                                                            <td id="office_number"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Is in good health:</td>
                                                            <td colspan="2" id="is_good_health"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Is under medical treatment now:</td>
                                                            <td colspan="2" id="is_under_treatment"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Had serious illness or surgical operation:</td>
                                                            <td colspan="2" id="had_operation"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Had been hospitalized:</td>
                                                            <td colspan="2" id="had_hospitalized"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Is taking prescription/non-prescription medication:</td>
                                                            <td colspan="2" id="is_taking_prescription"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Uses tobacco products:</td>
                                                            <td colspan="2" id="uses_tobacco"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Uses alcohol, cocaine, or other dangerous drugs:</td>
                                                            <td colspan="2" id="uses_alcohol_drugs"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="fw-semibold">Is allergic with the following:</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Local Anesthetic:</span></td>
                                                            <td colspan="2" id="is_allergic_anesthetic"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Aspirin:</span></td>
                                                            <td colspan="2" id="is_allergic_aspirin"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Penicillin, Antibiotics:</span></td>
                                                            <td colspan="2" id="is_allergic_penicillin"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Latex:</span></td>
                                                            <td colspan="2" id="is_allergic_latex"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Sulfa Drugs:</span></td>
                                                            <td colspan="2" id="is_allergic_sulfa"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Others:</span></td>
                                                            <td colspan="2" id="is_allergic_others"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Bleeding Time:</td>
                                                            <td colspan="2" id="bleeding_time"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="fw-semibold">For women only:</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Is pregnant:</span></td>
                                                            <td colspan="2" id="is_pregnant"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Is nursing:</span></td>
                                                            <td colspan="2" id="is_nursing"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold"><span class="ms-3">• Is taking birth control pills:</span></td>
                                                            <td colspan="2" id="is_birth_control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Blood Type:</td>
                                                            <td colspan="2" id="blood_type"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="fw-semibold">Blood Pressure:</td>
                                                            <td colspan="2" id="blood_pressure"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="treatmentItem" class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#treatmentInfo" aria-expanded="false" aria-controls="treatmentInfo">
                                        <span class="fw-semibold">Treatment History</span>
                                    </button>
                                </h2>
                                <div id="treatmentInfo" class="accordion-collapse collapse" data-bs-parent="#secretaryView">
                                    <div class="accordion-body">
                                        <div class="col-12 col-sm">
                                            <table id="treatmentTable" class="table-group-divider table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Dentist Name</th>
                                                        <th>Tooth No.</th>
                                                        <th>Dentist Note</th>
                                                        <th>Procedure Price</th>
                                                        <th>Procedure</th>
                                                        <th>Timestamp</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="tableBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal" aria-label="Close">Back</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addSecretaryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addSecretaryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-person"></i> Add New Secretary Account
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="addSecretaryClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/add-secretary.php" method="POST" class="col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="addSecretaryMessage" class="col-12" role="alert"></div>

                            <div class="col-lg-12">
                                <h5>Login Details</h5>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input maxlength="35" autocomplete="off" required name="username" placeholder="Username"  id="username" class="form-control onlyLettersNoSpace">
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                                    
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input maxlength="35" required autocomplete="off" type="email" name="email" placeholder="Email" id="email" class="form-control onlyEmail">
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="noemail" name="noemail" type="checkbox" data-bs-toggle="modal"data-bs-target="#noEmailConfirmModal">
                                            <label class="ms-1" for="noemail">N/A</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="input-group">
                                            <div class="form-floating">
                                                <input required autocomplete="new-password" type="password" minlength="6" maxlength="20" id="userPasswordCheck" class="form-control" name="userPasswordCheck" placeholder="Confirm Password">
                                                <label for="userPasswordCheck">Password</label>
                                            </div>
                                            <button class="btn btn-outline-secondary disableInputs input-group-text" type="button" id="togglePassword">
                                                <i id="eyeicon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div id="userPasswordCheckFeedback" class="mt-3" style="display: none;">
                                            <p id="userPassLower" class="invalidPassword">• Must use atleast one lower case letter.</p>
                                            <p id="userPassUpper" class="invalidPassword">• Must use atleast one upper case letter.</p>
                                            <p id="userPassNumber" class="invalidPassword">• Must use atleast one number.</p>
                                            <p id="userPassSymbol" class="validPassword">• Must not include any symbols except _.</p>
                                            <p id="userPassLength" class="invalidPassword">• Minimum of 6 characters. Max 20.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div id="confirmPass" class="input-group is-invalid">
                                            <div class="form-floating">
                                                <input disabled required autocomplete="new-password" type="password" minlength="6" maxlength="20" id="confirmUserPasswordCheck" class="form-control" name="confirmUserPasswordCheck" placeholder="Confirm Password">
                                                <label for="confirmUserPasswordCheck">Confirm Password</label>
                                            </div>
                                            <button disabled class="btn btn-outline-secondary disableInputs" type="button" id="toggleConfirmPassword">
                                                <i id="eyeicon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div id="confirmUserPasswordCheckFeedback" class="mt-3" style="display: none;">
                                            <p id="confirmPassCompare" class="invalidPassword">• Passwords do not match.</p>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <h5>Personal Details</h5>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="35" type="text" required name="fname" placeholder="First Name"  id="fname" class="form-control onlyLetters">
                                            <label for="fname">First Name</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <div class="form-floating">
                                                <input maxlength="35" type="text" required name="mname" placeholder="Middle Name" id="mname"  class="form-control onlyLetters">
                                                <label for="mname">M. Name</label>
                                            </div>
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox">
                                                <label class="ms-1" for="nomname">N/A</label>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="35" type="text" required name="lname" placeholder="Last Name"  id="lname" class="form-control onlyLetters">
                                            <label for="lname">Last Name</label>
                                        </div>
                                    </div>
                                        
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <div class="form-floating">
                                                <input maxlength="10" type="text" required name="suffix" placeholder="Middle Name" id="suffix" class="form-control onlyLetters">
                                                <label for="suffix">Suffix</label>
                                            </div>
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nosuffix" name="nosuffix" type="checkbox">
                                                <label class="ms-1" for="nosuffix">N/A</label>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="date" required name="bdate" placeholder="Birth Date"  id="bdate" class="form-control">
                                            <label for="bdate">Birth Date</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg">                                            
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <div class="form-floating">
                                                <input maxlength="10" type="text" required name="occupation" placeholder="Middle Name" id="occupation" class="form-control onlyLetters">
                                                <label for="occupation">Occupation</label>
                                            </div>
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nooccupation" name="nooccupation" type="checkbox">
                                                <label class="ms-1" for="nooccupation">N/A</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <select required class="form-select" name="gender" id="gender">
                                                <option disabled selected value="">Select...</option>
                                                <option value="Female">Female</option>
                                                <option value="Male">Male</option>
                                                <option value="Nonbinary">Nonbinary</option>
                                                <!-- <option value="Other">Other</option> -->
                                                <option value="Decline to state">Decline to state</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>
        
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="25" type="text" required name="religion" placeholder="Religion"  id="religion" class="form-control onlyLetters">
                                            <label for="religion">Religion</label>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="25" type="text" required name="nationality" placeholder="Nationality"  id="nationality" class="form-control onlyLetters">
                                            <label for="nationality">Nationality</label>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="11" type="text" required name="contnumber" placeholder="Contact Number" value="09" id="contnumber" class="form-control onlyNumbers">
                                            <label for="contnumber">Contact No.</label>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="form-floating mb-3">
                                    <input maxlength="100" type="text" required name="address" placeholder="Address"  id="address" class="form-control onlyAddress">
                                    <label for="address">Address</label>
                                </div>                                             
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success" name="secretarySubmitBtn">Submit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"data-bs-target="#cancelAddSecretaryConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelAddSecretaryConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelAddSecretaryConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelAddSecretaryConfirmLabel">
                        <i class="bi bi-person"></i> Add Patient Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addSecretaryModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="noEmailConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="noEmailConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="noEmailConfirmLabel">
                        <i class="bi bi-person"></i> No Email Confirmation
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Checking this box will allow the system to create a secretary account without an email. Moreover, password recovery via email will not be possible until the secretary registers an email themselves. Do you want to proceed?</h6>
                            <button type="button" id="addSecretaryYesBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addSecretaryModal">Yes</button>
                            <button type="button" id="addSecretaryNoBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#addSecretaryModal">No</button>
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
                <h1><i class="bi bi-person-gear"></i></h1>
                <h1 class="col ms-3">Secretary Accounts</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div class="col-md-9 my-3 rounded shadow bg-white row">
                <div class="my-3">
                    <div class="col">
                        <h3>Secretary Lists</h3>                        
                        <span>View all related information about the clinic's secretaries.</span>
                    </div>

                    <div id="errorMessage" class="col-12" role="alert"></div>

                    <table id="myTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">Secretary ID</th>
                                <th class="col">Full Name</th>
                                <th class="col">Contact Number</th>
                                <th class="col">Status</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
                                CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS Name, 
                                si.id AS ID, si.contactno AS Contact, si.bdate AS Bdate, ac.status as Status
                                FROM secretary_info si
                                LEFT OUTER JOIN accounts ac
                                ON si.accounts_id = ac.id;");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td id="secretaryID">' . $row['ID'] . '</td>
                                        <td id="secretaryName">' . $row['Name'] . '</td>
                                        <td id="secretaryContact">' .  $row['Contact'] . '</td>
                                        <td class="fw-bold ' . ($row['Status'] === 1 ? "text-success" : "text-danger") . '">' . ($row['Status'] === 1 ? "Active" : "Inactive") . '</td>
                                        <td class="appointID">
                                        <button type="button" data-p-id="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#secretaryViewModal">View
                                        </button>
                                        </td>
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
</body>

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/jquery-ui.js"></script>
<script src="../../resources/js/functions.js"></script>
<script src="../../resources/js/bootstrap.bundle.min.js"></script>
<script src='../../resources/js/index.global.js'></script>
<script src='../../resources/js/sidebar.js'></script>
<script src="../../resources/js/dataTables.js"></script>
<script src="../../resources/js/dataTables.searchBuilder.js"></script>
<script src="../../resources/js/searchBuilder.dataTables.js"></script>
<script src="../../resources/js/dataTables.dateTime.js"></script>
<script src="../../resources/js/dataTables.bootstrap5.js"></script>
<script src="../../resources/js/dataTables.buttons.js"></script>
<script src="../../resources/js/buttons.bootstrap5.js"></script>
<script src="../../resources/js/dataTables.searchPanes.js"></script>
<script src="../../resources/js/searchPanes.dataTables.js"></script>
<script src="../../resources/js/dataTables.select.js"></script>
<script src="../../resources/js/select.dataTables.js"></script>
<script src="../../resources/js/buttons.dataTables.js"></script>

<script>
    $(document).ready(function () {
        inputFilters();

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        DataTable.Buttons.defaults.dom.button.className = 'btn btn-sm btn-outline-primary';
        
        let table = new DataTable('#myTable', {
            language: {
                searchBuilder: {            
                    title: {
                        0: 'Filters',
                        _: 'Filters (%d)'
                    },
                }
            },
            select: false,
            lengthMenu: [
                [15, 25, 50, -1],
                [15, 25, 50, 'All'],
                ],
            layout: {
                topStart:{
                    search: true
                },
                topEnd: {
                    buttons: [
                        {
                            text: 'Add Secretary',
                            action: function (e, dt, node, config) {
                                $("#errorMessage").empty();
                                $('#addSecretaryModal').modal('show');
                            }
                        }
                    ]
                },
                top1: {
                    searchBuilder: {
                        
                    },
                },
                bottomStart: {
                    pageLength: true
                }
            },
            columnDefs: [
                {
                    searchPanes: {
                        show: false
                    },
                    targets: []
                },
                {
                    targets: [0,1,2,3,4],
                    className: 'dt-body-center dt-head-center'
                }
            ],
            autoWidth: false,
            paging: true,
            scrollCollapse: true,
            scrollY: '50vh',
            order: [
                [1, "asc"]
            ]
        });

        // $('#secretaryViewModal').modal('show');    

        function loadTable (){
            let treatmentTable = new DataTable('#treatmentTable', {
                select: false,
                lengthMenu: [
                    [5, 10, 15, -1],
                    [5, 10, 15, 'All'],
                ],
                layout: {
                    topStart:{
                    },
                    topEnd: {
                        search: true,
                    },
                    top1: {
                    },
                    bottomStart: {
                        pageLength: true
                    }
                },
                columnDefs: [
                    {
                        targets: [0,1,2,3,4,5,6],
                        className: 'dt-body-center dt-head-center align-middle'
                    }
                ],
                autoWidth: false,
                paging: true,
                scrollX: true,
                order: [[0, "asc"]],
                columns: [null, { width: '15%' }, null, { width: '25%' }, null]
            });
        }

		$("#myForm").submit(function(e){
            showLoader();
            $("#errorMessage, #addSecretaryMessage").empty();
			e.preventDefault();

			var url = $("#myForm").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#myForm").serialize(),
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    hideLoader();
                    $("#addSecretaryMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				console.log(data);
			}).fail(function(data) {
				console.log(data);
			});
		});

        $('body').on('click', '.viewAptDetail', function(){
            let id = $(this).attr('value');
            patient_id = $(this).attr("data-p-id");
            resetAccordion();
            fetchPatientDetails(patient_id, id);
            refreshDentalList(patient_id);
            refreshMedicalList(patient_id);
            refreshTreatment(patient_id);
        });

        $('#treatmentItem').on('click', function () {
            $('#treatmentTable').DataTable().columns.adjust();
        });
        
        if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");

            $("#errorMessage").append('<div class="mt-3 alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

            localStorage.removeItem("errorMessage");
        };

        function resetAccordion() {
            $('.accordion-collapse.show').each(function () {
                let collapseInstance = bootstrap.Collapse.getInstance(this) || new bootstrap.Collapse(this);
                collapseInstance.hide();
            });

            $('#patientInfo').addClass("show").attr("aria-expanded", true);            
            $('#secretaryViewBtn').removeClass("collapsed");
        }

        function fetchPatientDetails(pid, id) {
            var formData = {
                pid: pid,
                aptId: id
            };

            $.ajax({
                type: "POST",
                url: "php/fetch-patient-info.php",
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                let details = {
                    "#aptId": id, ".patientName": data.Name, "#patientUsername": data.username, ".patientAge": data.age,
                    "#patientBdate": data.bdate, ".patientGender": data.gender, "#patientContact": data.contactno, "#patientEmail": data.email_address,
                    "#patientReligion": data.religion, "#patientNationality": data.nationality, "#patientAddress": data.address, "#patientOccupation": data.occupation,
                    "#patientPrevDentist": data.prevDentist, "#patientLastVisit": data.lastDental, "#physician_name": data.physician_name, "#speciality": data.speciality,
                    "#office_address": data.office_address, "#office_number": data.office_number, "#is_good_health": data.is_good_health, "#uses_tobacco": data.uses_tobacco,
                    "#uses_alcohol_drugs": data.uses_alcohol_drugs, "#bleeding_time": data.bleeding_time, "#is_pregnant": data.is_pregnant, "#is_nursing": data.is_nursing, 
                    "#is_birth_control": data.is_birth_control, "#blood_type": data.blood_type, "#blood_pressure": data.blood_pressure
                };

                let allergic = {
                    "#is_allergic_anesthetic": data.is_allergic_anesthetic, "#is_allergic_penicillin": data.is_allergic_penicillin,
                    "#is_allergic_sulfa": data.is_allergic_sulfa, "#is_allergic_aspirin": data.is_allergic_aspirin, "#is_allergic_latex": data.is_allergic_latex,
                }

                let detailsText = {
                    "#is_under_treatment": data.is_under_treatment, 
                    "#is_under_treatment_condition": data.is_under_treatment_condition,
                    "#had_operation": data.had_operation, 
                    "#had_operation_illness": data.had_operation_illness,
                    "#is_taking_prescription": data.is_taking_prescription,  
                    "#is_taking_prescription_medication": data.is_taking_prescription_medication,
                    "#is_allergic_others": data.is_allergic_others,
                    "#is_allergic_others_other": data.is_allergic_others_other
                };

                let hadHospitalized = {
                    "#had_hospitalized": data.had_hospitalized,
                    "#had_hospitalized_when": data.had_hospitalized_when,
                    "#had_hospitalized_why": data.had_hospitalized_why
                };

                $.each(allergic, function(selector, value) {
                    let text = (value === "Yes") ? value : (value === "No Record" ? "No Record" : "No");

                    $(selector).text(text);
                });

                $.each(detailsText, function(selector, value) {
                    let extraValue = detailsText[selector + "_condition"] || detailsText[selector + "_illness"] || detailsText[selector + "_medication"] || detailsText[selector + "_other"] || "";

                    let text = (value === "Yes" || value === "No") ? (extraValue ? value + ", " + extraValue : value) : (value === "No Record" ? "No Record" : "No");

                    $(selector).text(text);
                });

                $.each(details, function(selector, value) {
                    let text = value ? value : (value === null ? "Not Set" : value);
                    
                    $(selector).text(text);
                });

                if (hadHospitalized['#had_hospitalized'] === "Yes") {
                    let dateString = hadHospitalized['#had_hospitalized_when'];
                    let date = new Date(dateString);
                    let formattedDate = date.toLocaleDateString("en-US", {year: "numeric", month: "long", day: "numeric" });

                    $('#had_hospitalized').append(hadHospitalized['#had_hospitalized'] + ", <span class='fw-semibold'>When: </span>" + formattedDate + " <span class='fw-semibold'>Reason: </span>" + hadHospitalized['#had_hospitalized_why']);
                } else if (hadHospitalized['#had_hospitalized'] === "No") {
                    $('#had_hospitalized').text("No");
                } else {
                    $('#had_hospitalized').text(hadHospitalized['#had_hospitalized']);
                }
                
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }
		
        function refreshDentalList(pid) {
            var formData = {
                pid: pid
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-dental.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                $("#dentalTable tbody").find("tr").remove();
                $('#dentalTable tbody').html(data);
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }
        
        function refreshMedicalList(pid) {
            var formData = {
                pid: pid
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-medical.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                $("#medicalHistoryLogsAcc").empty();
                $('#medicalHistoryLogsAcc').html(data);
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

        function refreshTreatment(patient_id) {
            var formData = {
                pid: patient_id
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-treatment-history.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                $('#treatmentTable').DataTable().destroy().clear();
                $('#tableBody').html(data);
                loadTable();
                // console.log(data.responseText);
            }).fail(function(data) {
                // console.log(data.responseText);
            });
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

        $("#aptCancelYesBtn").on("click", function() {
            $("#myForm")[0].reset();
            $("#userPasswordCheck, #confirmUserPasswordCheck").removeClass("is-invalid");
        });

        $("#addSecretaryNoBtn").on("click", function() {
            $("#noemail").attr("data-bs-toggle", "modal");
            $("#noemail").attr("data-bs-target", "#noEmailConfirmModal");
            $("#noemail").prop("checked", false);
            $("#email").prop("readonly", false);
            $("#email").val("");
        });

        $("#addSecretaryYesBtn").on("click", function() {
            $("#noemail").removeAttr("data-bs-toggle data-bs-target");
            $("#noemail").removeAttr("data-bs-target");
        });

        $("#noemail").on("click", function() {
            let id =  "#" + $(this).attr('id').substring(2);

            if ($(this).is(":checked")) {
                $(this).prop("checked", true);
                $(id).prop("readonly", true);
                $(id).val("None");
            } else {
                $(this).prop("checked", false);
                $(id).prop("readonly", false);
                $(id).val("");
                $(this).attr("data-bs-toggle", "modal");
                $(this).attr("data-bs-target", "#noEmailConfirmModal");
            }
        });

        $("#contnumber").on("focusin keypress focusout", function() {
            if (!this.value.startsWith("09")) {
                this.value = "09";
            }
        });

        $("#togglePassword, #toggleConfirmPassword").on("click", function() {
            let passwordInput = this.id == "togglePassword" ? "#userPasswordCheck" : "#confirmUserPasswordCheck";
            
            if ($(passwordInput).attr("type") == "password") {
                $(passwordInput).attr("type", "text");
                $("#" + this.id + " i").removeClass(['bi', 'bi-eye']);
                $("#" + this.id + " i").addClass(['bi', 'bi-eye-slash']);
            } else {
                $(passwordInput).attr("type", "password");
                $("#" + this.id + " i").removeClass(['bi', 'bi-eye-slash']);
                $("#" + this.id + " i").addClass(['bi', 'bi-eye']);
            }
        });

        $('#userPasswordCheck, #confirmUserPasswordCheck').focusin("click", function() {
            $("#" + this.id + "Feedback").show();
        });

        $('#userPasswordCheck, #confirmUserPasswordCheck').focusout("click", function() {
            $("#" + this.id + "Feedback").hide();
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