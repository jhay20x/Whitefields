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
    <title>Patients - Whitefields Dental Clinic</title>
    <link rel="stylesheet" href="../../resources/css/bootstrap.css">
    <link rel="stylesheet" href="../../resources/css/bootstrap-select.min.css">
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

        /* body {
            background-color: lightgrey;
        } */

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
        
        input[type="date"]::-webkit-calendar-picker-indicator, input[type="datetime-local"]::-webkit-calendar-picker-indicator {
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
    <div class="modal fade" id="patientViewModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="patientViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-file-medical"></i> Medical Information
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="patientViewClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="accordion" id="patientView">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" id="patientViewBtn" type="button" data-bs-toggle="collapse" data-bs-target="#patientInfo" aria-expanded="true" aria-controls="patientInfo">
                                        <span class="fw-semibold">Personal Information</span>
                                    </button>
                                </h2>
                                <div id="patientInfo" class="accordion-collapse collapse show" data-bs-parent="#patientView">
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
                                <div id="dentalInfo" class="accordion-collapse collapse" data-bs-parent="#patientView">
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
                                <div id="medicalInfo" class="accordion-collapse collapse" data-bs-parent="#patientView">
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
                                                    <tbody id="medicalTableBody">
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
                                                        <tr>
                                                            <td colspan="4" class="fw-semibold">Had or have the following:</td>
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
                                <div id="treatmentInfo" class="accordion-collapse collapse" data-bs-parent="#patientView">
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
                            <div id="uploadedMedia" class="accordion-item">
                                <h2 class="accordion-header text-center">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#uploadedMediaList" aria-expanded="false" aria-controls="uploadedMediaList">
                                        <span class="fw-semibold">Uploaded Media</span>
                                    </button>
                                </h2>
                                <div id="uploadedMediaList" class="accordion-collapse collapse" data-bs-parent="#patientView">
                                    <div class="row justify-content-center">
                                        <div id="deleteMediaMessage" class="col-10" role="alert"></div>
                                    </div>
                                    <div class="accordion-body row justify-content-around text-center">
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
    <div class="modal fade" id="dentalHistoryLogsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="dentalHistoryLogsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="dentalHistoryLogsLabel">
                        <i class="bi bi-person-vcard"></i> Dental History Records
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentalHistoryLogsClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 50vh;">
                        <table id="dentalTable" class="table">
                            <thead>
                                <tr>
                                    <th class="col">Remarks</th>
                                    <th class="col">Visit Date</th>
                                    <th class="col">Edit Timestamp</th>
                                </tr>
                            </thead>

                            <tbody id="dentalTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientViewModal">Back</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="medicalHistoryLogsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="medicalHistoryLogsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="medicalHistoryLogsLabel">
                        <i class="bi bi-file-medical"></i> Medical History Records
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="medicalHistoryLogsClose" aria-label="Close"></button> -->
                </div>
                <div class="table-responsive" style="max-height: 50vh;">
                    <div class="accordion accordion-flush" id="medicalHistoryLogsAcc">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientViewModal">Back</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPatientModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPatientLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-person"></i> Add New Patient
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="addPatientClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/add-patient.php" method="POST" class="col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="addPatientMessage" class="col-12" role="alert"></div>

                            <div class="col-lg-12">
                                <h5>Login Details</h5>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input maxlength="35" autocomplete="off" required name="username" placeholder="Username"  id="username" class="form-control onlyLettersNumbersNoSpace">
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
                        <button type="submit" class="btn btn-sm btn-outline-success" name="profileSubmitBtn">Submit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"data-bs-target="#cancelAddPatientConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPatientRecordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPatientRecordLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-person"></i> Add New Patient Record
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="addPatientRecordClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/add-patient-record.php" method="POST" class="col" id="addPatientRecordForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="addPatientRecordMessage" class="col-12" role="alert"></div>

                            <div id="pastAppointDetails" class="mb-3">
                                <div class="col-lg-12">
                                    <h5>Past Appointment Details</h5>
                                    <hr>
                                </div>

                                <div class="row my-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input required type="date" name="date" placeholder="Date"  id="date" id="date" class="form-control">
                                            <label for="date">Date</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row my-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label" for="selectPatientId">Patient Name</label>
                                        <select required class="selectpicker form-control show-tick" data-size="5" data-live-search="true" name="selectPatientId" id="selectPatientId">
                                            <option disabled selected value="">Select a patient...</option>
                                            <?php
                                                $stmt = $conn->prepare("SELECT pi.id,
                                                    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                                    CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name
                                                    FROM patient_info pi;");
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '
                                                            <option value="' . $row['id'] . '">' . $row['Name'] . '</option>
                                                        ';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6 mt-3 mt-lg-0">
                                        <label class="form-label" for="selectDentistId">Dentist Name</label>
                                        <select required class="selectpicker form-control show-tick" data-size="5" data-live-search="true" name="selectDentistId" id="selectDentistId">
                                            <option disabled selected value="">Select a dentist...</option>
                                            <?php
                                                $stmt = $conn->prepare("SELECT di.id,
                                                    CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
                                                    CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Name
                                                    FROM dentist_info di
                                                    LEFT OUTER JOIN accounts ac ON ac.id = di.accounts_id
                                                    WHERE ac.status != 0;");
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '
                                                            <option value="' . $row['id'] . '">' . $row['Name'] . '</option>
                                                        ';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="my-3 row align-items-center">
                                    <div class="col">
                                        <div class="form-floating">
                                            <select required class="form-select" name="timeHour" id="timeHour">
                                                <option disabled selected value="">--</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            <label for="timeHour">Hour</label>
                                        </div>
                                    </div>
                                    
                                    <h3 class="col-auto">:</h3>

                                    <div class="col">
                                        <div class="form-floating">
                                            <select required class="form-select" name="timeMinute" id="timeMinute">
                                                <option disabled selected value="">--</option>
                                                <option value="00">00</option>
                                                <option value="30">30</option>
                                            </select>
                                            <label for="timeMinute">Minute</label>
                                        </div>
                                    </div>

                                    <div class="col col-lg-2">
                                        <div class="form-floating">
                                            <select required class="form-select" name="timeAMPM" id="timeAMPM">
                                                <option disabled selected value="">--</option>
                                                <option value="AM">AM</option>
                                                <option value="PM">PM</option>
                                            </select>
                                            <label for="timeAMPM">AM/PM</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- <div class="input-group my-3">
                                    <label class="input-group-text" for="dentist">Dentist</label>
                                    <input maxlength="100" required disabled type="text" name="dentist" placeholder="Dentist"  id="dentist" class="form-control">
                                </div> -->
                                
                                <div class="form-floating my-3">
                                    <input maxlength="100" required type="text" name="concern" placeholder="Oral Concern (100 characters only)"  id="concern" class="form-control onlyLettersNumbers">
                                    <label for="concern">Oral Concern (100 characters only)</label>
                                </div>
                            </div>

                            <div id="pastAppointmentProcedures" class="mb-3">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <h5 class="col">Past Appointment Procedures</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary col-auto" id="addPatientRecordProcedureBtn">Add Procedure</button>
                                    </div>
                                    <hr>
                                </div>

                                <div class="row justify-content-center align-items-center overflow-auto" style="max-height: 300px;" id="proceduresList">
                                    <div class="row justify-content-center align-items-center mb-3 procedureRow" id="procedureRow_0">
                                        <div class="col-12 col-lg-1 mb-3 mb-lg-0">
                                            <h6><span class="d-inline d-lg-none">Procedure </span>1</h6>
                                        </div>
                                        <div class="col" id="procedureRow_0">
                                            <div class="row flex-row justify-content-center mb-3">
                                                <div class="col-12 col-lg-4 mb-3 order-0 order-lg-0">
                                                    <div class="form-floating">
                                                        <input type="text" name="patientToothNo[]" placeholder="Tooth No./s" id="patientToothNo_0" class="form-control onlyNumbers">
                                                        <label for="patientToothNo_0">Tooth No./s</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4 mb-3 order-1 order-lg-1">
                                                    <div class="form-floating">
                                                        <select required class="form-control" name="patientProcedure[]" id="patientProcedure_0">
                                                            <?php
                                                                $stmt = $conn->prepare("SELECT * FROM `procedures`");
                                                                $stmt->execute();
                                                                $result = $stmt->get_result();
                                                                $stmt->close();
                    
                                                                if ($result->num_rows > 0) {
                                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                                        !$row['status'] ? $disabled = "disabled" : $disabled = "";
        
                                                                        echo '
                                                                            <option ' . $disabled . ' value="' . $row['id'] . '">' . $row['name'] . '</option>
                                                                        ';
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                        <label class="form-label" for="patientProcedure_0">Procedure</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4 mb-3 order-2 order-lg-2">
                                                    <div class="form-floating">
                                                        <input type="text" required name="patientTransactionPrice[]" placeholder="Procedure Price" id="patientTransactionPrice_0" class="form-control onlyNumbersDots patientPrice">
                                                        <label for="patientTransactionPrice_0">Procedure Price</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-3 order-lg-3">
                                                    <div class="form-floating">
                                                        <input type="text" required name="patientTransactionAmountPaid[]" placeholder="Amount Paid" id="patientTransactionAmountPaid_0" class="form-control onlyNumbersDots patientPrice">
                                                        <label for="patientTransactionAmountPaid_0">Amount Paid</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-4 order-lg-4">
                                                    <div class="form-floating">
                                                        <input type="text" required name="patientTransactionRemainingBalance[]" placeholder="Remaining Balance" id="patientTransactionRemainingBalance_0" class="form-control onlyNumbersDots patientPrice">
                                                        <label for="patientTransactionRemainingBalance_0">Remaining Balance</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-5 order-lg-5">
                                                    <div class="form-floating">
                                                        <input required type="datetime-local" name="lastPaidDate" placeholder="Date"  id="lastPaidDate" id="lastPaidDate" class="form-control">
                                                        <label for="lastPaidDate">Last Paid Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto col-lg-1 mb-3 mb-lg-0">
                                            <button type="button" class="btn btn-outline-danger procedure-remove"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea required maxlength="255" style="height: 125px;" name="dentistNote" placeholder="Code" id="dentistNote" id="dentistNote" class="form-control"></textarea>
                                                <label for="dentistNote">Notes</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success" name="addPatientRecordSubmitBtn">Submit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"data-bs-target="#cancelAddPatientRecordConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelAddPatientConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelAddPatientConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelAddPatientConfirmLabel">
                        <i class="bi bi-person"></i> Add Patient Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addPatientModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelAddPatientRecordConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelAddPatientRecordConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelAddPatientRecordConfirmLabel">
                        <i class="bi bi-person"></i> Add Patient Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="aptCancelRecordYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="aptCancelRecordNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addPatientRecordModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelDeleteMediaConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelDeleteMediaConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelDeleteMediaConfirmLabel">
                        <i class="bi bi-person"></i> Delete Media
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to delete this image?</h6>
                            <button type="button" value="" id="aptDeleteMediaYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="aptDeleteMediaNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#patientViewModal">No</button>
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
                            <h6>Checking this box will allow the system to create a patient account without an email. Moreover, password recovery via email will not be possible until the patient registers an email themselves. Do you want to proceed?</h6>
                            <button type="button" id="addPatientYesBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addPatientModal">Yes</button>
                            <button type="button" id="addPatientNoBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#addPatientModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 100vh;">
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
                <h1 class="col ms-3">Patients</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div class="col-md-9 my-3 rounded shadow bg-white row">
                <div class="my-3">
                    <div class="col">
                        <h3>Patients Lists</h3>                        
                        <span>View all related information about the clinic's patients.</span>
                    </div>

                    <div id="errorMessage" class="col-12" role="alert"></div>

                    <table id="myTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">Patient ID</th>
                                <th class="col">Full Name</th>
                                <th class="col">Contact Number</th>
                                <th class="col">Birth Date</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, 
                                pi.id AS ID, pi.contactno AS Contact, pi.bdate AS Bdate, ar.id AS AppointmentID
                                FROM patient_info pi
                                LEFT OUTER JOIN appointment_requests ar
                                ON pi.id = ar.patient_id
                                GROUP BY pi.id, pi.fname, pi.mname, pi.lname, pi.contactno, pi.bdate;");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td id="patientID">' . $row['ID'] . '</td>
                                        <td id="patientName">' . $row['Name'] . '</td>
                                        <td id="patientContact">' .  $row['Contact'] . '</td>
                                        <td id="patientAge">' . $row['Bdate'] . '</td>
                                        <td class="appointID">
                                        <button type="button" data-p-id="' . $row['ID'] . '" value="' . $row['AppointmentID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">View
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
<script src="../../resources/js/bootstrap-select.min.js"></script>
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
        let procedureIndex = 1;
        let procedureOptions = $('#patientProcedure_0').html();        

		$("#addPatientRecordForm").submit(function(e){
            showLoader();
            $("#errorMessage, #addPatientMessage, #addPatientRecordMessage, #deleteMediaMessage").empty();
			e.preventDefault();

			var url = $("#addPatientRecordForm").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#addPatientRecordForm").serialize(),
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    hideLoader();
                    $("#addPatientRecordMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		});

        $("#status").on("change", function () {
            let val = $(this).val();

            switch (val) {
                case "5":
                    $(this).addClass("text-success").removeClass("text-warning text-danger");
                    break;

                case "6":
                    $(this).addClass("text-warning").removeClass("text-success text-danger");
                    break;

                case "7":
                    $(this).addClass("text-danger").removeClass("text-warning text-success");
                    break;
            
                default:
                    break;
            }
        });

        $('#addPatientRecordProcedureBtn').on('click', function () {
            $("#addPatientRecordMessage").empty();

            if (procedureIndex == 20) {
                $("#addPatientRecordMessage").append('<div class="alert alert-danger alert-dismissible fade show">Max procedures reached.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                return;
            }

            appendProcedureRow();
        });

        $('#proceduresList').on('click', '.procedure-remove', function () {
            console.log($('#proceduresList .procedureRow').length);
            $("#addPatientRecordMessage").empty();
                
            if ($('#proceduresList .procedureRow').length <= 1) {
                $("#addPatientRecordMessage").append('<div class="alert alert-danger alert-dismissible fade show">At least one procedure is required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                return;
            }

            $(this).closest('.procedureRow').remove();

            $('#proceduresList .procedureRow').each(function (index) {
                $(this).attr('id', `procedureRow_${index}`);

                $(this).find('h6').html(`<span class="d-inline d-lg-none">Procedure </span>${index + 1}`);

                $(this).find('input[name="patientToothNo[]"]').attr('id', `patientToothNo_${index}`);
                $(this).find('label[for^="patientToothNo_"]').attr('for', `patientToothNo_${index}`);

                $(this).find('select[name="patientProcedure[]"]').attr('id', `patientProcedure_${index}`);
                $(this).find('label[for^="patientProcedure_"]').attr('for', `patientProcedure_${index}`);

                $(this).find('input[name="patientPrice[]"]').attr('id', `patientPrice_${index}`);
                $(this).find('label[for^="patientPrice_"]').attr('for', `patientPrice_${index}`);
            });

            procedureIndex = $('#proceduresList .procedureRow').length;
        });

        function appendProcedureRow() {
            const newProcedure = `                
                <div class="row justify-content-center align-items-center mb-3 procedureRow" id="procedureRow_${procedureIndex}">
                    <div class="col-12 col-lg-1 mb-3 mb-lg-0">
                        <h6><span class="d-inline d-lg-none">Procedure </span>${procedureIndex + 1}</h6>
                    </div>
                    <div class="col" id="procedureRow_${procedureIndex}">
                        <div class="row flex-row justify-content-center mb-3">
                            <div class="col-12 col-lg-4 mb-3 order-0 order-lg-0">
                                <div class="form-floating">
                                    <input type="text" name="patientToothNo[]" placeholder="Tooth No./s" id="patientToothNo_${procedureIndex}" class="form-control onlyNumbers">
                                    <label for="patientToothNo_${procedureIndex}">Tooth No./s</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mb-3 order-1 order-lg-1">
                                <div class="form-floating">
                                    <select required class="form-control" name="patientProcedure[]" id="patientProcedure_${procedureIndex}">
                                        ${procedureOptions}
                                    </select>
                                    <label class="form-label" for="patientProcedure_${procedureIndex}">Procedure</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mb-3 order-2 order-lg-2">
                                <div class="form-floating">
                                    <input type="text" required name="patientTransactionPrice[]" placeholder="Procedure Price" id="patientTransactionPrice_${procedureIndex}" class="form-control onlyNumbersDots patientPrice">
                                    <label for="patientTransactionPrice_${procedureIndex}">Procedure Price</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-3 order-lg-3">
                                <div class="form-floating">
                                    <input type="text" required name="patientTransactionAmountPaid[]" placeholder="Amount Paid" id="patientTransactionAmountPaid_${procedureIndex}" class="form-control onlyNumbersDots patientPrice">
                                    <label for="patientTransactionAmountPaid_${procedureIndex}">Amount Paid</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-4 order-lg-4">
                                <div class="form-floating">
                                    <input type="text" required name="patientTransactionRemainingBalance[]" placeholder="Remaining Balance" id="patientTransactionRemainingBalance_${procedureIndex}" class="form-control onlyNumbersDots patientPrice">
                                    <label for="patientTransactionRemainingBalance_${procedureIndex}">Remaining Balance</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-5 order-lg-5">
                                <div class="form-floating">
                                    <input required type="datetime-local" name="lastPaidDate" placeholder="Date"  id="lastPaidDate" id="lastPaidDate" class="form-control">
                                    <label for="lastPaidDate">Last Paid Date</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto col-lg-1 mb-3 mb-lg-0 order-1 order-lg-4">
                        <button type="button" class="btn btn-outline-danger procedure-remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            `;

            $('#proceduresList').append(newProcedure);
            procedureIndex++;
            inputFilters();
        }

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
                            text: 'Add Patient Record',
                            action: function (e, dt, node, config) {
                                $("#errorMessage").empty();
                                $('#addPatientRecordModal').modal('show');
                            }
                        },
                        {
                            text: 'Add Patient',
                            action: function (e, dt, node, config) {
                                $("#errorMessage").empty();
                                $('#addPatientModal').modal('show');
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

        // $('#patientViewModal').modal('show');    

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
                order: [[0, "desc"]],
                columns: [null, { width: '15%' }, null, { width: '25%' }, null]
            });
        }

		$("#myForm").submit(function(e){
            showLoader();
            $("#errorMessage, #addPatientMessage, #addPatientRecordMessage, #deleteMediaMessage").empty();
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
                    $("#addPatientMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
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
            refreshMedia(patient_id);
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
            $('#patientViewBtn').removeClass("collapsed");
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

                let illness = {
                    "• High Blood Pressure:": data.high_blood_pressure, "• Low Blood Pressure:": data.low_blood_pressure, "• Epilepsy/Convulsions:": data.epilepsy_convulsions,
                    "• AIDS/HIV Infection:": data.aids_hiv_infection, "• Sexually Transmitted Disease:": data.sexually_transmitted_disease, "• Stomach Troubles/Ulcers:": data.stomach_troubles_ulcers,
                    "• Fainting/Seizure:": data.fainting_seizure, "• Rapid Weight Loss:": data.rapid_weight_loss, "• Radiation Therapy:": data.radiation_therapy,
                    "• Joint Replacement/Implant:": data.joint_replacement_implant, "• Heart Surgery:": data.heart_surgery, "• Heart Attack:": data.heart_attack,
                    "• Thyroid Problem:": data.thyroid_problem, "• Heart Disease:": data.heart_disease, "• Heart Murmur:": data.heart_murmur,
                    "• Hepatitis/Liver Disease:": data.hepatitis_liver_disease, "• Rheumatic Fever:": data.rheumatic_fever, "• Hay Fever/Allergies:": data.hay_fever_allergies,
                    "• Respiratory Problems:": data.respiratory_problems, "• Hepatitis/Jaundice:": data.hepatitis_jaundice, "• Tuberculosis:": data.tuberculosis,
                    "• Swollen Ankles:": data.swollen_ankles, "• Kidney Disease:": data.kidney_disease, "• Diabetes:": data.diabetes, "• Chest Pain:": data.chest_pain,
                    "• Stroke:": data.stroke, "• Cancer/Tumors:": data.cancer_tumors, "• Anemia:": data.anemia, "• Angina:": data.angina, "• Asthma:": data.asthma,
                    "• Emphysema:": data.emphysema, "• Bleeding Problems:": data.bleeding_problems, "• Blood Diseases:": data.blood_diseases,
                    "• Head Injuries:": data.head_injuries, "• Arthritis/Rheumatism:": data.arthritis_rheumatism, "• Other Illness:": data.other_illness
                };

                $("#medicalTableBody .dynamic-row").remove();

                $.each(illness, function(selector, value) {
                    if (value != null && value != "No Record") {
                        $("#medicalTableBody").append(`
                            <tr class="dynamic-row">
                                <td colspan="2" class="fw-semibold"><span class="ms-3">${selector}</span></td>
                                <td colspan="2">${value}</td>
                            </tr>
                        `);
                    }
                });
                
                $.each(allergic, function(selector, value) {
                    let text = (value === "Yes") ? value : (value === "No Record" ? "No Record" : "No");

                    $(selector).text(text);
                });

                $.each(detailsText, function(selector, value) {
                    let extraValue = detailsText[selector + "_condition"] || detailsText[selector + "_illness"]
                    || detailsText[selector + "_medication"] || detailsText[selector + "_other"] || "";

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
            
        function refreshMedia(pid) {
            var formData = {
                pid: pid
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-media.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                let html = '';

                if (data.length === 0) {
                    html = '<h6>No media uploaded.</h6>';
                } else {
                    data.forEach(img => {
                        html += `
                            <div class="card mb-3" style="max-width: 500px;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-md-4">
                                        <img src="${img.url}" 
                                            class="img-thumbnail image-preview" 
                                            style="max-height: 120px; cursor: pointer;" 
                                            alt="${img.filename}">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <p class="card-text mb-2"><strong>Appointment ID:</strong> ${img.appointment_id ?? 'N/A'}</p>
                                            <p class="card-text mb-1"><strong>Date:</strong> ${img.date}</p>
                                            <p class="card-text mb-1"><strong>File:</strong> ${img.filename}</p>
                                            <a href="${img.url}" download="${img.filename}" class="btn btn-sm btn-outline-primary">
                                                Download
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-media-btn" 
                                                    data-url="${img.url}" 
                                                    data-filename="${img.filename}" data-p-id="${pid}" data-bs-toggle="modal" data-bs-target="#cancelDeleteMediaConfirmModal">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                };

                $("#uploadedMediaList .accordion-body").html(html);
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

        $('body').on("click", '.delete-media-btn', function() {
            $("#errorMessage, #addPatientMessage, #addPatientRecordMessage, #deleteMediaMessage").empty();
            let url = $(this).data('url');
            let pid = $(this).attr('data-p-id');

            $("#aptDeleteMediaYesBtn").attr("data-url", url).attr("data-p-id", pid);
        });

        $('body').on("click", '#aptDeleteMediaYesBtn', function(e) {
            showLoader();
			e.preventDefault();
            $("#errorMessage, #addPatientMessage, #addPatientRecordMessage, #deleteMediaMessage").empty();

            let pid = $(this).attr('data-p-id');
            let url = $(this).attr('data-url');

            var formData = {
                url: url
            }

			$.ajax({
				type: "POST",
				url: 'php/delete-media.php',
				data: formData,
                dataType: "json"
			}).done(function (data) {
                hideLoader();
                refreshMedia(pid);
                $("#deleteMediaMessage").append('<div class="mt-3 alert alert-success alert-dismissible fade show">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                $('#patientViewModal').modal('show');
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
        });

        $('body').on("click", '.image-preview', function() {
            let src = $(this).attr("src");

            $('#previewImage').attr('src', src);
            $('#patientViewModal').modal('hide');
            $('#imagePreviewModal').modal('show');
        });

        $('#imagePreviewModal').on('hide.bs.modal', function () {
            $('#patientViewModal').modal('show');
        });

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

        $("#aptCancelRecordYesBtn").on("click", function() {
            $("#addPatientRecordForm")[0].reset();

            ["#selectDentistId", "#selectPatientId"].forEach(function (selector) {
                $(selector).val('').selectpicker('destroy').selectpicker();
            });
        });
    
        $('body').on("blur", ".patientPrice", function () {                
            let val = parseFloat(this.value);
            if (!isNaN(val)) {
                this.value = val.toFixed(2);
            }
        });

        $("#addPatientNoBtn").on("click", function() {
            $("#noemail").attr("data-bs-toggle", "modal");
            $("#noemail").attr("data-bs-target", "#noEmailConfirmModal");
            $("#noemail").prop("checked", false);
            $("#email").prop("readonly", false);
            $("#email").val("");
        });

        $("#addPatientYesBtn").on("click", function() {
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