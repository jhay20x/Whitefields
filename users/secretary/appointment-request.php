<?php
session_start();

global $conn;
require_once '../../database/config.php';
require_once 'php/fetch-id.php';

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

// echo date('Y/m/d h:i:s A', time());

// phpinfo();

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
    <title>Appointment Request - Whitefields Dental Clinic</title>
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

        @media only screen and (max-width: 600px) {

            .title h1{
                font-size: 1rem !important;
            }

            .title svg{
                width: 1.25rem !important;
            }

            table tr, .viewAptDetail{
                font-size: 0.8rem;
            }
        }

        #reasonDiv, #reasonOtherDiv {
            display: none;
        }

        #overlay {
            z-index: 999999999 !important;
        }


    </style>
</head>
<body class="bg-body-secondary">
    <?php include "../../components/sidebar.php" ?>

	<div id="overlay" style="display:none;">
		<div id="loader"></div>		
	</div>

    <!-- Modal -->
    <div class="modal fade" id="appointRequestModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="appointRequestLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-calendar3"></i> Appointment Details | Status: <strong id="aptdtlsstatus" class=""></strong>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointRequestClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="errorMessage" role="alert"></div>

                        <div class="col-12 col-lg">
                            <h6>Request Date: <span id="aptdtlsrequestdate" class="fw-normal"></span></h6>
                            <h6>Request Time: <span id="aptdtlsrequesttime" class="fw-normal"></span></h6>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-center align-items-start row">
                            <div class="col-12 col-lg">
                                <h6>
                                    Name: <span id="aptdtlsname" class="fw-normal"></span>
                                    <button class="btn btn-sm text-primary p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                        <i class="bi bi-eye"></i> View Profile
                                    </button>
                                </h6>
                                <h6>Scheduled Dentist: <span id="aptdtlsdentist" class="fw-normal"></span></h6>
                            </div>
                            <div class="col-12 col-lg">
                                <h6>Appointment Date: <span id="aptdtlsstartdate" class="fw-normal"></span></h6>
                                <h6>Appointment Time: <span id="aptdtlsstarttime" class="fw-normal"></span></h6>
                            </div>
                        </div>

                        <hr>

                        <div class="col-12 col-lg">
                            <h6>Oral Concern: <span id="aptdtlsconcern" class="fw-normal"></span></h6>
                        </div>

                        <hr>

                        <div class="col-12 col-lg">
                            <h6>Set Status<span id="aptdtlsconcern" class="fw-normal"></span></h6>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-floating mb-3 mb-lg-3">
                                    <select class="form-select" name="setStatus" id="setStatus">
                                        <option disabled selected value="">Select...</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Denied</option>
                                    </select>
                                    <label for="setStatus">Status</label>
                                </div>
                            </div>    
                            <div class="col-lg-6">
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
                                            <!-- <option value="1">Reason 1</option>
                                            <option value="2">Reason 2</option>
                                            <option value="3">Reason 3</option>
                                            <option value="4">Reason 4</option>
                                            <option value="5">Reason 5</option>
                                            <option value="6">Other...</option> -->
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
                    <button type="button" type="button" value="" id="aptApplyBtn" disabled class="btn btn-sm btn-outline-primary">Apply</button>
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
                            <div id="tratmentItem" class="accordion-item">
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
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointRequestModal">Back</button>
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
                        <h3>Appointment Requests</h3>
                        <span>View and manage all incoming appointment requests.</span>
                    </div>

                    <table id="appointmentTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">ID</th>
                                <th class="col">Request Date</th>
                                <th class="col">Patient Name</th>
                                <th class="col">Appointment Date</th>
                                <th class="col">Status</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody id="appointmentTableTableBody">
                            <?php
                            $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
                                CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID, ar.patient_id AS PID
                                FROM appointment_requests ar
                                LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                WHERE ar.appoint_status_id = 4
                                ORDER BY ar.id DESC;");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();
                            
                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $requesttime = date('Y-m-d', strtotime($row['RequestDateTime']));
                                    $approvedtime = date('Y-m-d', strtotime($row['ApprovedDateTime']));
                                    echo '
                                        <tr>
                                            <td id="appointID">' . $row['ID'] . '</td>
                                            <td id="appointRequestDate">' . $requesttime . '</td>
                                            <td id="appointName">' . $row['Name'] . '</td>
                                            <td id="appointApprovedDate">' . $approvedtime . '</td>
                                            <td id="appointStatus" class="text-warning fw-bold">' . $row['Status'] . '</td>
                                            <td class="appointID">
                                            <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointRequestModal">View
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

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/jquery-ui.js"></script>
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
<script src="../../resources/js/functions.js" defer></script>

<script>
    $(document).ready(function () {
        let patient_id;

        loadTable();
        inputFilters();

        function loadTable (){
            let table = new DataTable("#appointmentTable", {
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
                    bottomStart: {
                        
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
                        targets: [0,1,2,3,4,5],
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
                order: [[0, "desc"]],
                columns: [null, { width: '15%' }, null, { width: '25%' }, null]
            });
        }

        $('body').on('click', '.viewAptDetail', function(){

            let id = $(this).attr('value');
            patient_id = $(this).attr("data-p-id");

			$("#errorMessage").empty();
            $("#aptdtlsstatus").removeClass("text-success text-danger");
            $("#aptdtlsstatus").addClass("text-warning");
            $("#aptApplyBtn").attr("value", id);
            $("#setStatus option, #reasonDiv option").prop('selected', function() {
                return this.defaultSelected;
            });
            $("#reasonDiv").hide();
            $("#reasonOtherDiv").hide();
            $("#reasonOther").val("");
            $("#reason, #reasonOther, #setStatus").prop("disabled", false);
            
            fetchRequestDetails(id);
            resetAccordion();
            fetchPatientDetails(patient_id, id);
            refreshDentalList(patient_id);
            refreshMedicalList(patient_id);
            refreshTreatment(patient_id);
            refreshMedia(patient_id, id);
        });        

        $('#tratmentItem').on('click', function () {
            $('#treatmentTable').DataTable().columns.adjust();
        });

        function fetchRequestDetails(id) {            
            var formData = {
                id: id
            };

            $.ajax({
                type: "POST",
                url: "php/fetch-requests-details.php",
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                $("#aptdtlsstatus").text(data.Status);
                $("#aptdtlsdentist").text(data.Dentist);
                $("#aptdtlsname").text(data.Name);
                $("#aptdtlsstartdate").text(data.Start_Date);
                $("#aptdtlsstarttime").text(data.Start_Time);
                $("#aptdtlsrequestdate").text(data.Request_Date);
                $("#aptdtlsrequesttime").text(data.Request_Time);
                $("#aptdtlsapproveddate").text(data.Approved_Date);
                $("#aptdtlsapprovedtime").text(data.Approved_Time);
                $("#aptdtlsconcern").text(data.Concern);

                // console.log(data.responseText);
            }).fail(function(data) {
                // console.log(data.responseText);
            });
        }

        $("#setStatus").on('change', function() {
            let status = $("#setStatus").val();

            if (status == 2) {
                $('#aptApplyBtn').prop('disabled', false);
                $("#reasonDiv").show();
            } else if (status == 1) {
                $('#aptApplyBtn').prop('disabled', false);
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

        $("#aptApplyBtn").click(function() {
			$("#errorMessage").empty();
            let id = $("#aptApplyBtn").attr("value");
            let pid = patient_id;
            let setStatus = $("#setStatus").val();
            let setStatusText = $( "#setStatus option:selected" ).text();
            let datetime = $("#aptdtlsstartdate").text() + " at " + $("#aptdtlsstarttime").text();
            let reason = $("#reason").val();
            let reasonText = $( "#reason option:selected" ).text();
            let reasonOther = $("#reasonOther").val();

            if (setStatus == 1) {
                updateRequestStatus(id, pid, setStatus, setStatusText, datetime, null, null);
            } else if (setStatus == 2) {

                if (reason == "" || reason == null) {
                    $("#errorMessage").empty();
                    $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show mt-3">Please select a reason first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    return;
                }

                if (reason == 6 && reasonOther == "") {
                    $("#errorMessage").empty();
                    $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show mt-3">Please provide a valid reason for rejecting the appointment.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    return;
                }

                if (reason != 6) {
                    updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, reasonText, null);
                } else {
                    updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, reasonText, reasonOther);
                }
            }

        });

        function updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, reasonText, reasonOther) {
			showLoader();
            var formData = {
                id: id,
                pid: pid,
                setStatus: setStatus,
                setStatusText: setStatusText,
                datetime: datetime,
                reason: reason,
                reasonText: reasonText,
                reasonOther: reasonOther
            };
            
            $.ajax({
                type: "POST",
                url: "php/update-request-status.php",
                data: formData,
                dataType: 'json'
            }).done(function (data) {
				hideLoader();
                $("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show mt-3">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                if (setStatus == 1) {
                    $("#aptdtlsstatus").removeClass("text-danger text-warning");
                    $("#aptdtlsstatus").addClass("text-success");
                } else {
                    $("#aptdtlsstatus").removeClass("text-success text-warning");
                    $("#aptdtlsstatus").addClass("text-danger");
                }

                $("#aptApplyBtn, #reason, #reasonOther, #setStatus").prop("disabled", true);
                fetchRequestDetails(id);
                refreshList();
                // console.log(data.responseText);
            }).fail(function(data) {
                // console.log(data.responseText);
            });
        }

        function refreshList() {
            $.ajax({
                url: 'php/refresh-list.php',
                dataType: 'json'
            }).done(function (data) {
                $("#appointmentTable").DataTable().destroy().clear();
                $('#appointmentTableTableBody').html(data);
                loadTable();
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

        // $('body').on('click', '.viewPatientDetail', function(){
        //     fetchPatientDetails(patient_id);
        // });

        function resetAccordion() {
            $('.accordion-collapse.show').each(function () {
                let collapseInstance = bootstrap.Collapse.getInstance(this) || new bootstrap.Collapse(this);
                collapseInstance.hide();
            });
            
            $('#patientInfo').addClass("show").attr("aria-expanded", true);            
            $('#patientViewBtn').removeClass("collapsed");
        }

        setInterval(refreshList, 60000);

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