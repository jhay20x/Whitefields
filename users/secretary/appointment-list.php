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
        <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp" />
        <title>Appointment List - Whitefields Dental Clinic</title>
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
        <script src="../../resources/js/jquery-3.7.1.js"></script>

        <style>
            .bi {
                fill: currentColor;
            }

            /* body {
                backkground-color: lightgrey;
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

            @media only screen and (max-width: 600px) {

                .title h1 {
                    font-size: 1rem !important;
                }

                .title svg {
                    width: 1.25rem !important;
                }

                .modal-body h5{
                    font-size: 1rem;
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
        <div class="modal fade" id="patientUpdateStatusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="patientUpdateStatusLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-calendar3"></i> Appointment Details | Status: <strong id="" class="aptdtlsstatus"></strong>
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="patientUpdateStatusClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="appointmentUpdateMessage" class="" role="alert"></div>

                            <div class="d-flex align-items-start row">
                                <div class="col">
                                    <h6>Patient Name: <span id="" class="aptdtlsname fw-normal"></span></h6>
                                    <h6>Appointment Date: <span id="" class="aptdtlsStartDate fw-normal"></span></h6>
                                    <h6>Appointment Time: <span id="" class="aptdtlsStartTime fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="col-12 col-lg">
                                        <h6>Change Dentist</span></h6>
                                    </div>
                                    <div class="form-floating mb-3 mb-lg-3">
                                        <select class="form-select" name="patientChangeDentist" id="patientChangeDentist">
                                            <option disabled selected value="">Select Dentist...</option>
                                            <?php
                                                $stmt = $conn->prepare("SELECT di.id AS ID, 
                                                CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
                                                CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Dentist
                                                FROM dentist_info di
                                                LEFT OUTER JOIN accounts ac ON ac.id = di.accounts_id
                                                WHERE ac.status != 0;");
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $stmt->close();

                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '
                                                            <option value="' . $row['ID'] . '">' . $row['Dentist'] . '</option>
                                                        ';
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <label for="patientChangeDentist">Dentist</label>
                                    </div>

                                    <div id="aptdtlsUpdatePastApt" class="col">
                                        <div class="col-12 col-lg">
                                            <h6>Follow-up Appointment ID:</span></h6>
                                        </div>
                                        <div class="mb-3 mb-lg-3">
                                            <select required disabled class="selectpicker form-control show-tick" data-size="5" data-live-search="true" name="updateFollowUpAppointId" id="updateFollowUpAppointId"></select>
                                        </div>
                                    </div>

                                    <div id="aptdtlsUpdateUploadMedia" class="col">                                        
                                        <div class="col-12 col-lg">
                                            <h6>Upload Media</span></h6>
                                        </div>

                                        <form action="php/upload-media.php" enctype="multipart/form-data" name="uploadForm" method="POST" id="uploadForm">
                                            <div class="input-group">
                                                <input type="file" class="form-control" name="fileToUpload[]" id="fileToUpload" multiple>
                                                <input class="btn btn-outline-secondary" type="submit" name="uploadsubmitbtn" value="Upload" >
                                            </div>
                                        </form>
                                    </div>
                                </div> 

                                <div id="aptdtlsUpdateStatus" class="col-lg-6">
                                    <div class="col-12 col-lg">
                                        <h6>Set Status</span></h6>
                                    </div>
                                    <div class="form-floating mb-3 mb-lg-3">
                                        <select class="form-select" name="patientUpdateStatus" id="patientUpdateStatus">
                                            <option disabled selected value="">Select...</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Denied</option>
                                        </select>
                                        <label for="patientUpdateStatus">Status</label>
                                    </div>

                                    <div id="reasonDiv" class="col-lg mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" name="reason" id="reason">
                                                <option disabled selected value="">Select...</option>
                                                <?php
                                                    $stmt = $conn->prepare("SELECT * FROM `rejected_reasons` WHERE status != 0;");
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    $stmt->close();
    
                                                    if ($result->num_rows > 0) {
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '
                                                                <option value="' . $row['id'] . '">' . $row['reason'] . '</option>
                                                            ';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <label for="reason">Reason</label>
                                        </div>
                                    </div>
    
                                    <div id="reasonOtherDiv" class="col-lg">
                                        <div class="form-floating">
                                            <input autocomplete="off" type="text" name="reasonOther" placeholder="Reason"  id="reasonOther" class="form-control onlyLetters">
                                            <label for="reasonOther">Reason for Other</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" value="" id="updateStatusSaveBtn" class="btn btn-sm btn-outline-success m-2 me-0">Save</button>
                        <button type="button" value="" id="updateStatusCancelBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelSetStatusModal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="appointListModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="appointListLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-calendar3"></i> Appointment Details | Status: <strong id="" class="aptdtlsstatus"></strong>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointListClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">                            
                            <div id="appointmentMessage" class="" role="alert"></div>
                            
                            <div class="d-flex align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Request Date: <span id="aptdtlsRequestDate" class="fw-normal"></span></h6>
                                    <h6>Request Time: <span id="aptdtlsRequestTime" class="fw-normal"></span></h6>
                                </div>
                                <div class="col-12 col-lg">
                                    <h6>Appointment ID: <span id="aptId" class="fw-normal"></span></h6>
                                    <h6>Follow-up Appointment ID: <span id="pastAptId" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-start row">
                                <div class="col-12 col-lg">                                 
                                    <h6>Name: 
                                        <span id="" class="aptdtlsname fw-normal"></span>
                                        <button class="btn btn-sm text-primary p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                            <i class="bi bi-eye"></i> View Records
                                        </button>
                                    </h6>
                                    <h6>Appointment Date: <span id="" class="aptdtlsStartDate fw-normal"></span></h6>
                                    <h6>Appointment Time: <span id="" class="aptdtlsStartTime fw-normal"></span></h6>
                                </div>
                                <div class="col-12 col-lg">
                                    <h6>Scheduled Dentist: <span id="aptdtlsDentist" class="fw-normal"></span></h6>
                                    <h6>Oral Concern: <span id="aptdtlsConcern" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr class="aptdtlsVerdictDiv">

                            <div class="align-items-start row">
                                <div class="col-12 col-lg aptdtlsVerdictDiv">
                                    <h6><span class="aptdtlsVerdict"></span> Date: <span id="aptdtlsVerdictDate" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsVerdict"></span> Time: <span id="aptdtlsVerdictTime" class="fw-normal"></span></h6>
                                    <h6 id="aptdtlsVerdictText"><span class="aptdtlsVerdict" class="fw-normal"></span> By: <span id="aptdtlsVerdictBy" class="fw-normal"></span></h6>
                                </div>

                                <div class="col-12 col-lg aptdtlsReasonDiv">
                                    <h6><span class="fw-normal"></span> Reason: <span id="aptdtlsReason" class="fw-normal"></span></h6>
                                    <h6 class="aptdtlsVerdictOther"><span class="fw-normal"></span> Other Details: <span id="aptdtlsReasonOther" class="fw-normal"></span></h6>
                                </div>
                            </div>                  
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="updateStatusBtn" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientUpdateStatusModal">Update Appointment</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="cancelSetStatusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelSetStatusLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelSetStatusLabel">
                            <i class="bi bi-calendar3"></i> Update Appointment Form
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelSetStatusClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal"data-bs-target="#appointListModal">Yes</button>
                                <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#patientUpdateStatusModal">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientViewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="patientViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-file-medical"></i> Medical Information
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="patientViewClose" aria-label="Close"></button> -->
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
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#uploadedMediaList" aria-expanded="false" aria-controls="uploadedMediaList">
                                            <span class="fw-semibold">Uploaded Media</span>
                                        </button>
                                    </h2>
                                    <div id="uploadedMediaList" class="accordion-collapse collapse" data-bs-parent="#patientView">
                                        <div class="accordion-body text-center">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
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
        <div class="modal fade" id="manualAppointmentModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="manualAppointmentLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="manualAppointmentLabel">
                            <i class="bi bi-calendar3"></i> Appointment Request Form
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="manualAppointmentClose" aria-label="Close"></button>
                    </div>
                    <form autocomplete="off" action="php/insert-manual-appointment.php" method="POST" class="" id="myForm">
                        <div class="modal-body">
                            <div class="container-fluid">
				                <div id="errorMessage" class="" role="alert"></div>
                                
                                <div class="form-floating col">
                                    <input required type="date" name="date" placeholder="Date"  id="date" id="date" class="form-control">
                                    <label for="date">Date</label>
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
                                        <input class="form-check-input" type="checkbox" id="isFollowUp">
                                        <label class="form-label" for="isFollowUp">Follow-up Visit <span class="text-primary" id="exclamationIcon" data-bs-toggle="tooltip" data-bs-title="Check this box if the appointment was for a follow-up procedure."><i class="bi bi-exclamation-circle"></i></span></label>
                                        <select required disabled class="selectpicker form-control show-tick" data-size="5" data-live-search="true" name="followUpAppointId" id="followUpAppointId"></select>
                                        <!-- <input required type="text" disabled name="followUpAppointId" placeholder="Appointment ID"  id="followUpAppointId" id="followUpAppointId" class="form-control onlyNumbers"> -->
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
                                    <input required type="hidden" name="dentist" id="dentist">
                                    <input maxlength="100" required type="text" name="concern" placeholder="Oral Concern (100 characters only)"  id="concern" class="form-control onlyLettersNumbers">
                                    <label for="concern">Oral Concern (100 characters only)</label>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-outline-primary btn-sm" value="Submit" name="addbtn">
                        </div>
                    </form>
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
                    <h1><i class="bi bi-calendar3"></i></h1>
                    <h1 class="col ms-3">Appointments</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="my-3">                        
                        <div class="col">
                            <h3>Appointment Lists</h3>
                            <span>View and manage all of the appointments received by the clinic.</span>
                        </div>

                        <table id="appointmentListTable" class="table-group-divider table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="col">ID</th>
                                    <th class="col">Request Date</th>
                                    <!-- <th class="col">Request Time</th> -->
                                    <th class="col-3">Patient Name</th>
                                    <th class="col">Appointment Date</th>
                                    <th class="col-3">Dentist Name</th>
                                    <th class="col">Status</th>
                                    <!-- <th class="col">Appointment Time</th> -->
                                    <th class="col">Action</th>
                                </tr>
                            </thead>

                            <tbody id="appointmentListTableBody">
                                <?php
                                $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
                                    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                    CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID, ar.patient_id AS PID,
                                    CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
                                    CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Dentist
                                    FROM appointment_requests ar
                                    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                    LEFT OUTER JOIN dentist_info di ON di.id = ar.dentist_info_id
                                    LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                    WHERE ar.appoint_status_id != 4
                                    ORDER BY ar.id  DESC;");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $stmt->close();

                                $status;

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $requesttime = date('Y-m-d', strtotime($row['RequestDateTime']));
                                        $approvedtime = date('Y-m-d', strtotime($row['ApprovedDateTime']));

                                        if ($row['Status'] == "Approved") {
                                            $status = "text-success";
                                        } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled" || $row['Status'] == "Partially Paid") {
                                            $status = "text-danger";
                                        } else {
                                            $status = "text-secondary";
                                        }
                                        echo '
                                        <tr>
                                            <td id="appointID">' . $row['ID'] . '</td>
                                            <td id="appointRequestDate">' . $requesttime . '</td>
                                            <td id="appointName">' . $row['Name'] . '</td>
                                            <td id="appointApprovedDate">' . $approvedtime . '</td>
                                            <td id="appointDentistName">' . $row['Dentist'] . '</td>
                                            <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                                            <td class="appointID">
                                            <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
                                            </button>
                                            </td>
                                        </tr>
                                    ';
                                    }
                                }
                                ?>
                            </tbody>

                            <!-- <tfoot>
                                <tr>
                                    <th class="col">Date</th>
                                    <th class="col">Time</th>
                                    <th class="col-3">Name</th>
                                    <th class="col">Status</th>
                                    <th class="col">Action</th>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script src="../../resources/js/jquery-ui.js"></script>
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
    <script src="../../resources/js/functions.js" defer></script>

    <script>
        $(document).ready(function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            let patient_id;

            loadTable();
            loadModal();
            inputFilters();

            $("#uploadForm").on('submit',(function(e) {
                e.preventDefault();
                showLoader();
                $("#errorMessage, #appointmentUpdateMessage").empty();

                let id = $("#updateStatusSaveBtn").attr('value');
                let pid = $("#updateStatusSaveBtn").attr('data-p-id');

                var url = $("#uploadForm").attr('action');

                var formData = new FormData(this);

                formData.append('id', id);
                formData.append('pid', pid);
                
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,            
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        refreshList();
                        fetchPatientDetails(patient_id, id);
                        refreshDentalList(patient_id);
                        refreshMedicalList(patient_id);
                        refreshTreatment(patient_id);
                        refreshMedia(patient_id, id);
                        loadDetails(id);
                        $('#patientUpdateStatusModal').modal('hide');
                        $('#appointListModal').modal('show');
                        $("#patientUpdateStatus option, #reasonDiv option").prop('selected', function() {
                            return this.defaultSelected;
                        });
                        $("#reasonOther").val("");
                        $("#appointmentMessage").append(`<div class="alert alert-success alert-dismissible fade show">${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                        hideLoader();
                        // console.log(data.responseText);
                    },
                    error: function(data) {
                        // console.log(data.responseText);
                    }
                });
            }));

            $("#myForm").submit(function(e) {
                $("#errorMessage").empty();
                e.preventDefault();

                var url = $("#myForm").attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $("#myForm").serialize(),
                    dataType: "json"
                }).done(function (data) {       
                    if (!data.success) {
                        $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        refreshList();
                        $("#myForm")[0].reset();
                        $('.selectpicker').selectpicker('val', '');
                        $("#followUpAppointId")
                            .prop('disabled', true).empty()
                            .selectpicker('destroy')
                            .selectpicker('refresh');
                        $("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            });            

            $("#date").on('change', function() {
                var formData = {
                    date: $('#date').val()
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-dentist.php",
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    if (!data.success) {
                        $("#dentist").val(data.error);
                    } else {
                        $("#dentist").val(data.dentist_id);
                    }
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            });

            $('#appointmentListTable thead th').eq(3).attr('width', '0%');            
            
            $('body').on('hide.bs.modal', function (e) {
                $("#myForm")[0].reset();
                $('.selectpicker').selectpicker('val', '');
                $("#followUpAppointId")
                    .prop('disabled', true).empty()
                    .selectpicker('destroy')
                    .selectpicker('refresh');
            })

            function loadTable() {
                DataTable.Buttons.defaults.dom.button.className = 'btn btn-sm btn-outline-primary';
                let table = new DataTable('#appointmentListTable', {
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
                        top1: {
                            searchBuilder: {
    
                            },
                        },
                        topEnd: {
                            buttons: [
                                {
                                    text: 'Add Appointment',
                                    action: function (e, dt, node, config) {
                                        $("#errorMessage").empty();
                                        $('#manualAppointmentModal').modal('show');
                                    }
                                }
                            ]
                        },
                        topStart:{
                            search: true
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
                            targets: [0,1,2,3,4,5,6],
                            className: 'dt-body-center dt-head-center'
                        }
                    ],
                    autoWidth: false,
                    paging: true,
                    scrollCollapse: true,
                    scrollY: '50vh',
                    order: [
                        [0, "desc"]
                    ]
                });
            }

            function loadtreatmentTable (){
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

            $('#treatmentItem').on('click', function () {
                $('#treatmentTable').DataTable().columns.adjust();
            });

            $('body').on('click', '.viewAptDetail', function(){
                let id = $(this).attr('value');
                patient_id = $(this).attr("data-p-id");
                $("#updateStatusSaveBtn").attr("value", id).attr("data-p-id", patient_id);
                resetAccordion();
                fetchPatientDetails(patient_id, id);
                refreshDentalList(patient_id);
                refreshMedicalList(patient_id);
                refreshMedia(patient_id, id);
                refreshTreatment(patient_id);
                loadDetails(id);
            });

            $('body').on('change', '#isFollowUp', function(){
                let state = $(this).prop("checked");
                let pid = $("#selectPatientId").val();
                let select = "#followUpAppointId";
                
                if (state) {
                    fetchAppointments(pid, select, false, "");
                } else {
                    $("#followUpAppointId").prop('disabled', true).empty();
                    $("#followUpAppointId").selectpicker('destroy');
                    $("#followUpAppointId").selectpicker('refresh');
                }
            });

            $('body').on('change', '#selectPatientId', function(){
                let state = $("#isFollowUp").prop("checked");
                let pid = $(this).val();
                let select = "#followUpAppointId";

                if (state) {
                    fetchAppointments(pid, select, false, "");
                }
            });

            function fetchAppointments(pid, select, hasPastId, pastAptId) {                
                showLoader();

                let $select = $(select);
                $select.prop('disabled', false).empty();

                var formData = {
                    pid: pid
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-patient-appointments.php",
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    hideLoader();

                    $.each(data, function(index, value) {
                        $select.append($('<option>', {
                            value: value.id,
                            text: `Appointment #${value.id} - ${value.procedures}`
                        }));
                    });

                    if (hasPastId) {
                        $select.prepend($('<option>', {
                            value: "",
                            text: `Nothing selected`
                        }));
                    
                        $select.selectpicker('destroy');
                        $select.selectpicker();

                        if (pastAptId !== "") {
                            $select.selectpicker('val', pastAptId);
                        } else {
                            $select.selectpicker('val', "");
                            $select.val('');
                            $select.find('option').prop('selected', false);
                        }
                    }                    
                    
                    $select.selectpicker('destroy');
                    $select.selectpicker();
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            $('#updateStatusSaveBtn').on('click', function () {
                let id = $(this).attr('value');
                let pid = $(this).attr('data-p-id');
                let dentist_id = $("#patientChangeDentist").val();
                let setStatus = $("#patientUpdateStatus").val();
                let setStatusText = $( "#patientUpdateStatus option:selected" ).text();
                let datetime = $(".aptdtlsStartDate").first().text() + " at " + $(".aptdtlsStartTime").first().text();
                let reason = $("#reason").val();
                let reasonText = $( "#reason option:selected" ).text();
                let reasonOther = $("#reasonOther").val();
                let pastAptId = $("#updateFollowUpAppointId").selectpicker('val');

                if (setStatus == 1) {
                    updateRequestStatus(id, pid, dentist_id, setStatus, setStatusText, datetime, null, null, null, pastAptId);
                } else if (setStatus == 2) {

                    if (reason == "" || reason == null) {
                        $("#appointmentUpdateMessage").empty();
                        $("#appointmentUpdateMessage").append('<div class="alert alert-danger alert-dismissible fade show">Please select a reason first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        return;
                    }

                    if (reason == 6 && reasonOther == "") {
                        $("#appointmentUpdateMessage").empty();
                        $("#appointmentUpdateMessage").append('<div class="alert alert-danger alert-dismissible fade show">Please provide a valid reason for rejecting the appointment.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        return;
                    }

                    if (reason != 6) {
                        updateRequestStatus(id, pid, dentist_id, setStatus, setStatusText, datetime, reason, reasonText, null, pastAptId);
                    } else {
                        updateRequestStatus(id, pid, dentist_id, setStatus, setStatusText, datetime, reason, reasonText, reasonOther, pastAptId);
                    }
                }
            });
            
            function updateRequestStatus(id, pid, dentist_id, setStatus, setStatusText, datetime, reason, reasonText, reasonOther, pastAptId) {
                showLoader();
                var formData = {
                    id: id,
                    pid: pid,
                    dentist_id: dentist_id,
                    setStatus: setStatus,
                    setStatusText: setStatusText,
                    datetime: datetime,
                    reason: reason,
                    reasonText: reasonText,
                    reasonOther: reasonOther,
                    pastAptId: pastAptId
                };

                $.ajax({
                    type: "POST",
                    url: "php/update-request-details.php",
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    refreshList();
                    fetchPatientDetails(patient_id, id);
                    refreshDentalList(patient_id);
                    refreshMedicalList(patient_id);
                    refreshTreatment(patient_id);
                    refreshMedia(patient_id, id);
                    loadDetails(id);
                    $('#patientUpdateStatusModal').modal('hide');
                    $('#appointListModal').modal('show');
                    $("#patientUpdateStatus option, #reasonDiv option").prop('selected', function() {
                        return this.defaultSelected;
                    });
                    $("#reasonOther").val("");
                    $("#appointmentMessage").append(`<div class="alert alert-success alert-dismissible fade show">${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                    hideLoader();
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
            }

            $('#appointListModal').on('hidden.bs.modal', function () {
                $("#appointmentMessage").empty();
            });

            $("#aptCancelYesBtn").on("click", function() {
                $("#patientUpdateStatus option, #reasonDiv option").prop('selected', function() {
                    return this.defaultSelected;
                });
                $("#reasonOther").val("");                
            });

            function loadModal() {
                let url_str = document.URL;

                let url = new URL(url_str);
                let search_params = url.searchParams;

                let id = search_params.get('id');
                let pid = search_params.get('pid');
                window.history.replaceState({}, document.title, window.location.pathname);

                if (id) {
                    resetAccordion();
                    fetchPatientDetails(pid, id);
                    refreshDentalList(pid);
                    refreshMedicalList(pid);
                    refreshTreatment(pid);
                    refreshMedia(pid, id);
                    loadDetails(id);
                    $('#appointListModal').modal('show');
                }
            };

            function loadDetails(id) {
                var formData = {
                    id: id
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-requests-details.php",
                    data: formData,
                    dataType: 'json'
                }).done(function(data) {                    
                    switch (data.Status) {
                        case "Approved":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").hide();
                            $("#aptdtlsUpdateStatus").show();
                            $("#aptdtlsUpdatePastApt").show();
                            $(".aptdtlsstatus").removeClass("text-danger text-warning text-secondary").addClass("text-success");
                            break;

                        case "Denied":
                        case "Cancelled":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").show();
                            $("#aptdtlsUpdateStatus").hide();
                            $("#aptdtlsUpdatePastApt").hide();
                            $(".aptdtlsstatus").removeClass("text-success text-warning text-secondary").addClass("text-danger");
                            if (data.ReasonOther || data.CancelReasonOther) {
                                $(".aptdtlsVerdictOther").show();
                            } else {
                                $(".aptdtlsVerdictOther").hide();
                            }
                            break;
                            
                        case "Partially Paid":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").hide();
                            $("#aptdtlsUpdateStatus").hide();
                            $("#aptdtlsUpdatePastApt").hide();
                            $(".aptdtlsstatus").removeClass("text-success text-warning text-secondary").addClass("text-danger");
                            break;
                        
                        case "Evaluated":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").hide();
                            $("#aptdtlsUpdateStatus").hide();
                            $("#aptdtlsUpdatePastApt").hide();
                            $(".aptdtlsstatus").removeClass("text-danger text-warning text-success").addClass("text-secondary");
                            break;
                    
                        default:
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").hide();
                            $("#aptdtlsUpdateStatus").hide();
                            $("#aptdtlsUpdatePastApt").hide();
                            $(".aptdtlsstatus").removeClass("text-success text-danger text-warning").addClass("text-secondary");
                            break;
                    }

                    let aptDetails = [data.Name, data.Status, data.Status, data.Dentist, data.Start_Date, data.Start_Time, data.Request_Date, data.Request_Time, data.Approved_By, data.Concern, data.PastAptId, data.AptId];
                    let aptDetailsId = [".aptdtlsname", ".aptdtlsstatus", ".aptdtlsVerdict", "#aptdtlsDentist", ".aptdtlsStartDate", ".aptdtlsStartTime", "#aptdtlsRequestDate", "#aptdtlsRequestTime", "#aptdtlsVerdictBy", "#aptdtlsConcern", "#pastAptId", "#aptId"];

                    for (let index = 0; index < aptDetails.length; index++) {
                        $(aptDetailsId[index]).text(aptDetails[index]);
                    }

                    switch (data.Status) {
                        case "Cancelled":
                            $("#aptdtlsReason").text(data.CancelReason);
                            $("#aptdtlsReasonOther").text(data.CancelReasonOther);
                            $("#aptdtlsVerdictDate").text(data.Cancel_Date);
                            $("#aptdtlsVerdictTime").text(data.Cancel_Time);
                            $("#aptdtlsVerdictBy").text(data.Cancelled_By);
                            $("#aptdtlsVerdictText").show();
                            break;    

                        case "Denied":
                            $("#aptdtlsVerdictDate").text(data.Approved_Date);
                            $("#aptdtlsVerdictTime").text(data.Approved_Time);
                            $("#aptdtlsReason").text(data.Reason);
                            $("#aptdtlsReasonOther").text(data.ReasonOther);
                            $("#aptdtlsVerdictText").show();
                            break;

                        case "Evaluated":
                            $("#aptdtlsVerdictDate").text(data.Examined_Date);
                            $("#aptdtlsVerdictTime").text(data.Examined_Time);
                            $("#aptdtlsVerdictBy").text(data.Dentist);
                            $("#aptdtlsVerdictText").show();
                            break;

                        case "Completed":
                            $("#aptdtlsVerdictDate").text(data.Completed_Date);
                            $("#aptdtlsVerdictTime").text(data.Completed_Time);
                            $("#aptdtlsVerdictBy").text("");
                            $("#aptdtlsVerdictText").hide();
                            break;

                        case "Partially Paid":
                            $("#aptdtlsVerdictDate").text(data.Partial_Date);
                            $("#aptdtlsVerdictTime").text(data.Partial_Time);
                            $("#aptdtlsVerdictBy").text("");
                            $("#aptdtlsVerdictText").hide();
                            break;

                        default:
                            $("#aptdtlsVerdictDate").text(data.Approved_Date);
                            $("#aptdtlsVerdictTime").text(data.Approved_Time);
                            $("#aptdtlsReason").text("");
                            $("#aptdtlsReasonOther").text("");     
                            $("#aptdtlsVerdictText").show();
                            break;
                    }
                    
                    // if (data.length != 0) {
                    //     $('#appointListModal').modal('show');
                    // }

                    $("#updateStatusBtn").attr("data-dentist-id", data.Dentist_ID)

                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            $('body').on('click', '#updateStatusBtn', function(){
                let status = $(".aptdtlsstatus").first().text();
                let dentistID = $(this).attr('data-dentist-id');
                let pid = $("#updateStatusSaveBtn").attr("data-p-id");
                let select = "#updateFollowUpAppointId";
                let pastAptId = $("#pastAptId").text();
                pastAptId = pastAptId !== "N/A" ? pastAptId : "";

                $("#patientChangeDentist").val(dentistID);
                $('#patientUpdateStatus').val(1);
                $("#reasonDiv").hide();
                $("#reasonOtherDiv").hide();

                if (status == "Approved") {
                    fetchAppointments(pid, select, true, pastAptId);
                }
                
            });

            $("#patientUpdateStatus").on('change', function() {
                let status = $("#patientUpdateStatus").val();

                if (status == 2) {
                    $("#reasonDiv").show();
                } else {
                    $("#reasonDiv").hide();
                    $("#reasonDiv option").prop('selected', function() {
                        return this.defaultSelected;
                    });
                    $("#reasonOtherDiv").hide();
                    $("#reasonOther").val("");
                }
            });

            $("#reason").on('change', function() {
                let reason = $("#reason").val();

                if (reason == 6) {
                    $("#reasonOtherDiv").show();
                    $("#reasonOther").focus();
                } else {
                    $("#reasonOtherDiv").hide();
                }
            });

            function refreshList() {
                $.ajax({
                    url: 'php/refresh-appointment-list.php',
                    dataType: 'json'
                }).done(function (data) {
                    $("#appointmentListTable").DataTable().destroy().clear();
                    $('#appointmentListTableBody').html(data);
                    loadTable();
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }       

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
                        ".patientName": data.Name, "#patientUsername": data.username, ".patientAge": data.age,
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
            
            function refreshMedia(pid, id) {
                var formData = {
                    pid: pid,
                    id: id
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
                            html += `<img src="${img}" class="img-thumbnail me-2 mb-2 image-preview" style="width:200px; cursor:pointer;">`;
                        });
                    };

                    $("#uploadedMediaList .accordion-body").html(html);
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            $('body').on("click", '.image-preview', function() {
                let src = $(this).attr("src");

                $('#previewImage').attr('src', src);
                $('#patientViewModal').modal('hide');
                $('#imagePreviewModal').modal('show');
            });

            $('#imagePreviewModal').on('hide.bs.modal', function () {
                $('#patientViewModal').modal('show');
            });
            
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
                    loadtreatmentTable();
                    // console.log(data.responseText);
                }).fail(function(data) {
                    // console.log(data.responseText);
                });
            }

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