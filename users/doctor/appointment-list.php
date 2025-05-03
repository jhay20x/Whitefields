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
    <link rel="stylesheet" href="../../resources/css/loader.css">
    <link rel="stylesheet" href="../../resources/css/sidebar.css">
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
    </style>
</head>

<body class="bg-body-secondary">
    <?php include "../../components/sidebar.php" ?>
    
	<div id="overlay" style="display:none;">
		<div id="loader"></div>		
	</div>

    <!-- Modal -->
    <div class="modal fade" id="appointListModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="appointListLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-calendar3"></i> Appointment Details | Status: <strong id="aptdtlsstatus" class=""></strong>
                    </h6>                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointListClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-center align-items-start row">
                            <div class="col-12 col-lg">
                                <h6>Request Date: <span id="aptdtlsrequestdate" class="fw-normal"></span></h6>
                                <h6>Request Time: <span id="aptdtlsrequesttime" class="fw-normal"></span></h6>
                            </div>
                            <div class="col-12 col-lg">
                                <h6>Appointment ID: <span id="" class="fw-normal aptId"></span></h6>
                                <h6>Follow-up Appointment ID: <span id="" class="fw-normal pastAptId"></span></h6>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-center align-items-start row">
                            <div class="col-12 col-lg">
                                <h6>Name: 
                                    <span id="aptdtlsname" class="fw-normal"></span>
                                    <button class="btn btn-sm text-primary p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                        <i class="bi bi-eye"></i> View Records
                                    </button>
                                </h6>
                                <h6>Appointment Date: <span id="aptdtlsstartdate" class="fw-normal"></span></h6>
                                <h6>Appointment Time: <span id="aptdtlsstarttime" class="fw-normal"></span></h6>
                            </div>

                            <div class="col-12 col-lg">
                                <h6>Oral Concern: <span id="aptdtlsconcern" class="fw-normal"></span></h6>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="col-12 col-lg">
                            <h6><span class="aptdtlsverdict" class="fw-normal"></span> Date: <span id="aptdtlsapproveddate" class="fw-normal"></span></h6>
                            <h6><span class="aptdtlsverdict" class="fw-normal"></span> Time: <span id="aptdtlsapprovedtime" class="fw-normal"></span></h6>
                            <h6 id="aptdtlsapprovedbytext"><span class="aptdtlsverdict" class="fw-normal"></span> By: <span id="aptdtlsapprovedby" class="fw-normal"></span></h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" value="" id="aptTreatmentRecord" class="btn btn-sm btn-outline-primary m-2 me-0" data-bs-toggle="modal" data-bs-target="#treatPatientModal">Treatment Record</button>                        
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="treatPatientModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="treatPatientLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="treatPatientLabel">
                        <i class="bi bi-person"></i> Treatment Record
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointListClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="errorMessage" role="alert"></div>
                        
                        <form autocomplete="off" action="php/insert-update-treatment-record.php" method="POST" class="" id="myForm">
                            <div class="d-flex justify-content-center align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Patient Name: <span id="" class="fw-normal patientName"></span></h6>
                                    <h6>Age: <span id="" class="fw-normal patientAge"></span></h6>
                                    <h6>Gender: <span id="" class="fw-normal patientGender"></span></h6>
                                </div>
                                <div class="col-12 col-lg">
                                    <h6></span> Appointment ID: <span id="" class="fw-normal aptId"></span></h6>
                                    <h6></span> Follow-up Appointment ID: <span id="" class="fw-normal pastAptId"></span></h6>
                                    <h6></span> Date: <span id="" class="fw-normal"><?php echo date(format: 'Y-m-d', timestamp: time()); ?></span></h6>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="row justify-content-center overflow-auto" style="max-height: 300px;" id="proceduresList">
                                <div class="row flex-row justify-content-center align-items-center mb-3" id="procedureRow_0">
                                    <div class="col col-lg-1 mb-3 mb-lg-0 order-0 order-lg-0">
                                        <h6><span class="d-inline d-lg-none">Procedure </span>1</h6>
                                    </div>
                                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-2 order-lg-1">
                                        <div class="form-floating">
                                            <input type="text" name="patientToothNo[]" placeholder="Tooth No./s" id="patientToothNo_0" class="form-control onlyNumbers">
                                            <label for="patientToothNo_0">Tooth No./s</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-3 order-lg-2">
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
                                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-4 order-lg-3">
                                        <div class="form-floating">
                                            <input type="text" required name="patientPrice[]" placeholder="Procedure Price" id="patientPrice_0" class="form-control patientPrice onlyNumbersDots">
                                            <label for="patientPrice_0">Procedure Price</label>
                                        </div>
                                    </div>
                                    <div class="col-auto col-lg-1 mb-3 mb-lg-0 order-1 order-lg-4">
                                        <button type="button" class="btn btn-outline-danger procedure-remove"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea maxlength="255" style="height: 125px;" name="dentistNote" placeholder="Code" id="dentistNote" id="dentistNote" class="form-control"></textarea>
                                            <label for="dentistNote">Notes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                            <div id="aptTreatPatientUpdateDiv">
                                <div class="d-flex justify-content-end">
                                    <button type="button" id="aptTreatPatientUpdateBtn" class="btn btn-sm btn-outline-primary m-2 me-0">Update</button>
                                    <button type="button" id="aptTreatPatientBackBtn" class="btn btn-sm btn-outline-primary m-2 me-0" data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
                                </div>
                            </div>
                                
                            <div id="aptTreatPatientSaveDiv">
                                <div class="d-flex justify-content-end">
                                    <button type="button" id="aptTreatPatientAddProcedureBtn" class="btn btn-sm btn-outline-primary m-2 me-0">Add Procedure</button>
                                    <button type="submit" id="aptTreatPatientSaveBtn" class="btn btn-sm btn-outline-success m-2 me-0">Save</button>
                                    <button type="button" id="aptTreatPatientCancelBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelRequestConfirmModal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelRequestConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelRequestConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelRequestConfirmLabel">
                        <i class="bi bi-calendar3"></i> Appointment Cancellation Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal"data-bs-target="#appointListModal">Yes</button>
                            <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#treatPatientModal">No</button>
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
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
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientViewModal">Back</button>
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
                <h1 class="col ms-3">My Appointments</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div class="col-md-9 my-3 rounded shadow bg-white row">
                <div class="my-3">
                    <div class="col">
                        <h3>Appointment Lists</h3>
                        <span>View and manage all of your patient appointments. Set or Update their treatment records.</span>
                    </div>

                    <table id="dentistTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">ID</th>
                                <th class="col">Appointment Date</th>
                                <th class="col">Appointment Time</th>
                                <th class="col">Name</th>
                                <th class="col">Status</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            <?php
                            $id = fetchDentistID();

                            $stmt = $conn->prepare("SELECT DATE(ar.start_datetime) AS Date, TIME(ar.start_datetime) AS Time, st.status_name AS Status, 
                                CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID, ar.patient_id AS PID, ar.past_appoint_id AS PAID
                                FROM appointment_requests ar
                                LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                WHERE (ar.appoint_status_id = 1 OR ar.appoint_status_id = 6 OR ar.appoint_status_id = 5 OR ar.appoint_status_id = 7) AND ar.dentist_info_id = ?
                                ORDER BY Date DESC, Time ASC;");
                            $stmt->bind_param('i', $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            $status;

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $time = date('h:i A', strtotime($row['Time']));

                                    if ($row['Status'] == "Approved") {
                                        $status = "text-success";
                                    } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled" || $row['Status'] == "Partially Paid") {
                                        $status = "text-danger";
                                    } else if ($row['Status'] == "Completed") {
                                        $status = "text-secondary";
                                    } else {
                                        $status = "text-warning";
                                    }
                                    echo '
                                    <tr>
                                        <td id="appointID">' . $row['ID'] . '</td>
                                        <td id="appointDate">' . $row['Date'] . '</td>
                                        <td id="appointTime">' .  $time . '</td>
                                        <td id="appointName">' . $row['Name'] . '</td>
                                        <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                                        <td class="appointID">
                                            <button type="button" data-past-apt-id="' . ($row['PAID'] ?? 0) . '" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
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

<script src="../../resources/js/bootstrap.bundle.js"></script>
<script src="../../resources/js/bootstrap-select.min.js"></script>
<script src="../../resources/js/jquery-ui.js"></script>
<script src='../../resources/js/index.global.js'></script>
<script src='../../resources/js/sidebar.js'></script>
<script src='../../resources/js/functions.js'></script>
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
    $(document).ready(function() {        
        loadModal();
        inputFilters();

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;

        // console.log(today); 

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
                let status = $("#aptdtlsstatus").text();
                hideLoader();
                refreshList();
                $(".patientPrice").removeClass("is-valid is-invalid");
                $(this).find("[data-bs-toggle='tooltip']").attr("title", "").tooltip('dispose');
                $("#errorMessage").append('<div class="alert alert-success  alert-dismissible fade show mt-3">' + data.message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                $("#myForm").find("input, .procedure-remove, select, textarea").prop("disabled", true);
                $("#aptTreatPatientUpdateDiv").show();
                $("#aptTreatPatientSaveDiv").hide();

                if (status == "Approved") {
                    $("#aptdtlsstatus")
                        .removeClass("text-success text-warning text-danger")
                        .addClass("text-secondary")
                        .text("Evaluated");
                }

                refreshTreatment(patient_id);
				// console.log(data.responseText);
			}).fail(function(data) {
				// console.log(data.responseText);
			});
		});

        let patient_id;

        let procedureIndex = 1;

        $('#aptTreatPatientAddProcedureBtn').on('click', function () {
            $("#errorMessage").empty();

            if (procedureIndex == 20) {
                $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">Max procedures reached.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                return;
            }

            appendProcedureRow();
        });

        $(".viewPatientDetail").on("click", function() {
            resetAccordion();
        });

        function appendProcedureRow() {
            const newProcedure = `
                <div class="row flex-row align-items-center mb-3" id="procedureRow_${procedureIndex}">
                    <div class="col col-lg-1 mb-3 mb-lg-0 order-0 order-lg-0">
                        <h6><span class="d-inline d-lg-none">Procedure </span>${procedureIndex + 1}</h6>
                    </div>
                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-2 order-lg-1">
                        <div class="form-floating">
                            <input type="text" name="patientToothNo[]" placeholder="Tooth No./s" id="patientToothNo_${procedureIndex}" class="form-control onlyNumbers">
                            <label for="patientToothNo_${procedureIndex}">Tooth No./s</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-3 order-lg-2">
                        <div class="form-floating">
                            <select required class="form-control" name="patientProcedure[]" id="patientProcedure_${procedureIndex}">
                                ${procedureOptions}
                            </select>
                            <label for="patientProcedure_${procedureIndex}">Procedure</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-4 order-lg-3" data-bs-toggle="tooltip" data-bs-placement="top" title="">
                        <div class="form-floating">
                            <input type="text" required name="patientPrice[]" placeholder="Procedure Price" id="patientPrice_${procedureIndex}" class="form-control patientPrice onlyNumbersDots">
                            <label for="patientPrice_${procedureIndex}">Procedure Price</label>
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
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            fetchPrice(1);
        }

        let procedurePrice;

        $("body").on("keyup blur click focusin", ".patientPrice", function (){
            const $input = $(this);
            const $row = $input.closest(".row, [class^='col']");
            const $procedureSelect = $row.parent().find("select[name='patientProcedure[]']");
            const selectedProcedure = $procedureSelect.val();
            
            if (!procedurePrice || $.isEmptyObject(procedurePrice)) {
                fetchPrice(selectedProcedure);
            }

            const price = parseFloat($input.val());
            const tooltipTarget = $input.closest("[data-bs-toggle='tooltip']");
            tooltipTarget.attr("title", "").tooltip('dispose');

            showTooltip (selectedProcedure, tooltipTarget, $input, price);
        });

        $("body").on("change", "select[name='patientProcedure[]']", function () {
            procedurePrice = [];

            const $select = $(this);
            const $row = $select.closest(".row");
            const $input = $row.find(".patientPrice");
            const selectedProcedure = $select.val();

            fetchPrice(selectedProcedure);

            const price = parseFloat($input.val());
            const tooltipTarget = $input.closest("[data-bs-toggle='tooltip']");
            tooltipTarget.attr("title", "").tooltip('dispose');

            setTimeout(() => {
                showTooltip(selectedProcedure, tooltipTarget, $input, price);                
            }, 1000);            
        });

        function showTooltip (selectedProcedure, tooltipTarget, $input, price) {
            let priceSet = parseFloat(price);
            let price_Min = parseFloat(procedurePrice.price_min);
            let price_Max = parseFloat(procedurePrice.price_max);

            if (priceSet > price_Max) {
                $input.addClass("is-invalid").removeClass("is-valid");
                tooltipTarget.attr("title", "Price can't be over than maximum price " + price_Max + ".").tooltip('show');
                return;
            } 
            
            if (priceSet < price_Min) {
                $input.addClass("is-invalid").removeClass("is-valid");
                tooltipTarget.attr("title", "Price can't be less than minimum price " + price_Min + ".").tooltip('dispose').tooltip('show');
                return;
            } 
            
            if (isNaN(priceSet) || (priceSet === 0.00 && price_Min !== 0.00)) {
                $input.addClass("is-invalid").removeClass("is-valid");
                tooltipTarget.attr("title", "Price can't be zero.").tooltip('dispose').tooltip('show');
                return;
            }
                                
            $input.removeClass("is-invalid").addClass("is-valid");
            tooltipTarget.attr("title", "").tooltip('dispose');
        }

        function fetchPrice(pid) {
            showLoader();
            var formData = {
                pid: pid
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-procedure-price.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                setTimeout(() => {
                    hideLoader();
                }, 1000);
                procedurePrice = data;
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        };
        
        $('#proceduresList').on('click', '.procedure-remove', function () {
            $("#errorMessage").empty();
                
            if ($('#proceduresList .row').length <= 1) {
                $("#errorMessage").append('<div class="alert alert-danger alert-dismissible fade show">At least one procedure is required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                return;
            }

            $(this).closest('.row').remove();

            $('#proceduresList .row').each(function (index) {
                $(this).attr('id', `procedureRow_${index}`);

                $(this).find('h6').html(`<span class="d-inline d-lg-none">Procedure </span>${index + 1}`);

                $(this).find('input[name="patientToothNo[]"]').attr('id', `patientToothNo_${index}`);
                $(this).find('label[for^="patientToothNo_"]').attr('for', `patientToothNo_${index}`);

                $(this).find('select[name="patientProcedure[]"]').attr('id', `patientProcedure_${index}`);
                $(this).find('label[for^="patientProcedure_"]').attr('for', `patientProcedure_${index}`);

                $(this).find('input[name="patientPrice[]"]').attr('id', `patientPrice_${index}`);
                $(this).find('label[for^="patientPrice_"]').attr('for', `patientPrice_${index}`);
            });

            procedureIndex = $('#proceduresList .row').length;
        });
    
        $('body').on("blur", ".patientPrice", function () {                
            let val = parseFloat(this.value);
            if (!isNaN(val)) {
                this.value = val.toFixed(2);
            }
        });

        loadTable();

        $('#dentistTable thead th').eq(3).attr('width', '0%');

        function loadTable (){
            let table = new DataTable('#dentistTable', {
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
                    topStart: {

                    },
                    topEnd: {
                        search: true
                    },
                    top1: {
                        // searchPanes: {
                        //     initCollapsed: true,
                        // },
                        searchBuilder: {
                            // preDefined: {
                            //     criteria: [
                            //         {
                            //             data: 'Appointment Date',
                            //             condition: '=',
                            //             // value: ["2025-02-08"]
                            //             value: [today]
                            //         }
                            //     ]
                            // }
                        },
                    },
                    bottomStart: {
                        pageLength: true
                    }
                },
                columnDefs: [
                    {
                        targets: [0,1,2,3,4,5],
                        className: 'dt-body-center dt-head-center',
                    },
                ],
                autoWidth: false,
                paging: true,
                scrollCollapse: true,
                scrollY: '50vh',
                order: [
                    [1, "desc"],
                    [2, "desc"],
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

        $('#treatmentItem').on('click', function () {
            $('#treatmentTable').DataTable().columns.adjust();
        });      

        function refreshList() {
            $.ajax({
                url: 'php/refresh-list.php',
                dataType: 'json'
            }).done(function (data) {
                $('#dentistTable').DataTable().destroy().clear();
                $('#dentistTable tbody').html(data);
                loadTable();
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }
        
        $("#aptTreatPatientUpdateDiv, #aptTreatPatientSaveDiv").hide();

        $('body').on('click', '.viewAptDetail', function(){
            let id = $(this).attr('value');
            let patient_id = $(this).attr("data-p-id");
            let past_appoint_id = $(this).attr("data-past-apt-id");
            $('#aptTreatmentRecord').attr('data-aptId', id).attr('data-past-apt-id', past_appoint_id);
            resetAccordion();
            fetchPatientDetails(patient_id, id);
            refreshDentalList(patient_id);
            refreshMedicalList(patient_id);
            refreshTreatment(patient_id);
            loadDetails(id);
        });

        $('body').on('click', '#aptCancelYesBtn', function(){
            let detailsId = ["#patientToothNo", "#dentistNote"];
    
            for (let index = 0; index < detailsId.length; index++) {
                $(detailsId[index]).val("");
                $(detailsId[index]).prop('disabled', true);
            }

            $('#procedure').selectpicker('val', "");
        });

        $('body').on('click', '#aptTreatmentRecord', function(){
            let id = $(this).attr('data-aptId');
            let pastAptId = $(this).attr("data-past-apt-id");

            loadTreatmentRecord(id, pastAptId);
        });

        $('body').on('click', '#aptTreatPatientUpdateBtn', function(){
            let status = $("#aptdtlsstatus").text();
            let pastAptId = $("#aptTreatmentRecord").attr("data-past-apt-id");

            if (status == "Evaluated") {
                $("#myForm").find("input, select, textarea, button").prop("disabled", false);
                $("#myForm").find(".no-enable").prop("disabled", true);
                
                $("#aptTreatPatientSaveDiv").show();
                $("#aptTreatPatientAddProcedureBtn").show();
                $("#aptTreatPatientUpdateDiv").hide();
            } else {
                $("#myForm").find("textarea").prop("disabled", false);
                
                $("#aptTreatPatientSaveDiv").show();
                $("#aptTreatPatientUpdateDiv").hide();
                $("#aptTreatPatientAddProcedureBtn").hide();
            }
        });

        function loadModal() {
            let url_str = document.URL;

            let url = new URL(url_str);
            let search_params = url.searchParams;

            let id = search_params.get('id');
            let pid = search_params.get('pid');
            let paid = search_params.get('paid');
            $('#aptTreatmentRecord').attr('data-aptId', id);
            window.history.replaceState({}, document.title, window.location.pathname);

            if (id) {
                resetAccordion();
                fetchPatientDetails(pid, id);
                refreshDentalList(pid);
                refreshMedicalList(pid);
                refreshTreatment(pid);
                loadTreatmentRecord(id, paid);
                loadDetails(id);
            }
        };
        
        // $(window).on('hide.bs.modal', function (e) {
        //     window.history.replaceState({}, document.title, window.location.pathname);
        // })

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
                if (data.Status == "Approved") {
                    $("#aptdtlsstatus").removeClass("text-danger text-warning text-secondary");
                    $("#aptdtlsstatus").addClass("text-success");
                } else if (data.Status == "Denied" || data.Status == "Cancelled" || data.Status == "Partially Paid") {
                    $("#aptdtlsstatus").removeClass("text-success text-warning text-secondary");
                    $("#aptdtlsstatus").addClass("text-danger");
                } else if (data.Status == "Completed") {
                    $("#aptdtlsstatus").removeClass("text-success text-warning text-danger");
                    $("#aptdtlsstatus").addClass(" text-secondary");
                } else {                        
                    $("#aptdtlsstatus").removeClass("text-success text-danger text-secondary");
                    $("#aptdtlsstatus").addClass("text-warning");
                }

                let details = [data.Status, data.Status, data.Dentist, data.Name, data.Start_Date, data.Start_Time, data.Request_Date, data.Request_Time, data.Approved_By, data.Concern, data.PastAptId, data.AptId];
                let detailsId = ["#aptdtlsstatus", ".aptdtlsverdict", "#aptdtlsdentist", "#aptdtlsname", "#aptdtlsstartdate", "#aptdtlsstarttime", "#aptdtlsrequestdate", "#aptdtlsrequesttime", "#aptdtlsapprovedby", "#aptdtlsconcern", ".pastAptId", ".aptId"];                

                for (let index = 0; index < details.length; index++) {
                    $(detailsId[index]).text(details[index]);
                }
                
                switch (data.Status) {
                    case "Cancelled":
                        $("#aptdtlsReason").text(data.CancelReason);
                        $("#aptdtlsReasonOther").text(data.CancelReasonOther);
                        $("#aptdtlsapproveddate").text(data.Cancel_Date);
                        $("#aptdtlsapprovedtime").text(data.Cancel_Time);
                        $("#aptdtlsapprovedby").text("The Client");
                        $("#aptdtlsapprovedbytext").show();
                        break;

                    case "Denied":
                        $("#aptdtlsapproveddate").text(data.Approved_Date);
                        $("#aptdtlsapprovedtime").text(data.Approved_Time);
                        $("#aptdtlsReason").text(data.Reason);
                        $("#aptdtlsReasonOther").text(data.ReasonOther);
                        $("#aptdtlsapprovedbytext").show();
                        break;

                    case "Evaluated":
                        $("#aptdtlsapproveddate").text(data.Examined_Date);
                        $("#aptdtlsapprovedtime").text(data.Examined_Time);
                        $("#aptdtlsapprovedby").text(data.Dentist);
                        $("#aptdtlsapprovedbytext").show();
                        break;

                    case "Completed":
                        $("#aptdtlsapproveddate").text(data.Completed_Date);
                        $("#aptdtlsapprovedtime").text(data.Completed_Time);
                        $("#aptdtlsapprovedby").text("");
                        $("#aptdtlsapprovedbytext").hide();
                        break;

                    case "Partially Paid":
                        $("#aptdtlsapproveddate").text(data.Partial_Date);
                        $("#aptdtlsapprovedtime").text(data.Partial_Time);
                        $("#aptdtlsapprovedby").text("");
                        $("#aptdtlsapprovedbytext").hide();
                        break;

                    default:
                        $("#aptdtlsapproveddate").text(data.Approved_Date);
                        $("#aptdtlsapprovedtime").text(data.Approved_Time);
                        $("#aptdtlsReason").text("");
                        $("#aptdtlsReasonOther").text("");     
                        $("#aptdtlsapprovedbytext").show();
                        break;
                }
                    
                if (data.length != 0) {
                    $('#appointListModal').modal('show');
                }
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
                    ".patientName": data.Name, ".patientUsername": data.username, ".patientAge": data.age,
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
        
        setInterval(refreshList, 60000);

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

        let procedureOptions = $('#patientProcedure_0').html();

        function loadTreatmentRecord(aptId, pastAptId) {
            $("#errorMessage").empty();
            $("#aptId").text(aptId);
            let status = $("#aptdtlsstatus").text();
            $("#pastAptId").text(pastAptId == 0 ? "N/A" : pastAptId);

            var formData = {
                aptId: aptId,
                pastAptId: pastAptId
            };

            $.ajax({
                type: "POST",
                url: 'php/fetch-treatment-record.php',
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                if (data.length != 0) {
                    $('#proceduresList').empty();
                    let state = "";

                    if (pastAptId != 0 && status == "Approved") {
                        $("#myForm").find("input, select, textarea").prop("disabled", false);
                        $("#aptTreatPatientUpdateDiv").hide();
                        $("#aptTreatPatientSaveDiv").show();
                        $("#aptTreatPatientAddProcedureBtn").show();
                    } else {
                        $("#myForm").find("input, select, textarea").prop("disabled", true);
                        $("#aptTreatPatientSaveDiv").hide();
                        $("#aptTreatPatientUpdateDiv").show();
                        $("#aptTreatPatientAddProcedureBtn").hide();
                    }
    
                    data.forEach((item, index) => {
                        if (pastAptId == item.appointment_requests_id) {
                            state = "no-enable";
                            $("#dentistNote").val("");
                        } else {
                            state = "";
                            $("#dentistNote").val(item.dentist_note);
                        }
                        
                        const newRow = `
                            <div class="row flex-row align-items-center mb-3" id="procedureRow_${index}">
                                <div class="col col-lg-1 mb-3 mb-lg-0 order-0 order-lg-0">
                                    <h6><span class="d-inline d-lg-none">Procedure </span>${index + 1}</h6>
                                </div>
                                <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-2 order-lg-1">
                                    <div class="form-floating">
                                        <input disabled type="text" name="patientToothNo[]" placeholder="Tooth No./s" id="patientToothNo_${index}" class="form-control ${state}" value="${item.tooth_number}">
                                        <label for="patientToothNo_${index}">Tooth No./s</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4 mb-3 mb-lg-0 order-3 order-lg-2">
                                    <div class="form-floating">
                                        <select disabled required class="form-control ${state}" name="patientProcedure[]" id="patientProcedure_${index}">
                                            ${procedureOptions}
                                        </select>
                                        <label for="patientProcedure_${index}">Procedure</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-4 order-lg-3" data-bs-toggle="tooltip" data-bs-placement="top" title="">
                                    <div class="form-floating">
                                        <input disabled type="text" name="patientPrice[]" placeholder="Procedure Price" id="patientPrice_${index}" class="form-control patientPrice ${state}" value="${parseFloat(item.procedure_price).toFixed(2)}">
                                        <label for="patientPrice_${index}">Procedure Price</label>
                                    </div>
                                </div>
                                <div class="col-auto col-lg-1 mb-3 mb-lg-0 order-1 order-lg-4">
                                    <button disabled type="button" class="btn btn-outline-danger procedure-remove ${state}"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        `;
    
                        $('#proceduresList').append(newRow);
    
                        $(`#patientProcedure_${index}`).val(item.procedures_id);
                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
                        fetchPrice(1);
                    });
                    
                    procedureIndex = data.length;                 
                } else {
                    $('#proceduresList').empty();
                    $("#dentistNote").val("");
                    procedureIndex = data.length;

                    appendProcedureRow();

                    $("#myForm").find("input, select, textarea").prop("disabled", false);
                    $("#aptTreatPatientUpdateDiv").hide();
                    $("#aptTreatPatientSaveDiv").show();
                    $("#aptTreatPatientAddProcedureBtn").show();
                }
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
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