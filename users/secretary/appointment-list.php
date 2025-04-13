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
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp" />
        <title>Appointment List - Whitefields Dental Clinic</title>
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
        <script src="../../resources/js/jquery-3.7.1.js"></script>

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
                                                LEFT OUTER JOIN Accounts ac ON ac.id = di.accounts_id
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
                                                    $stmt = $conn->prepare("SELECT * FROM `rejected_reasons`;");
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
                            <div class="d-flex align-items-start row">
                                <div class="col">
                                    <h6>Request Date: <span id="aptdtlsRequestDate" class="fw-normal"></span></h6>
                                    <h6>Request Time: <span id="aptdtlsRequestTime" class="fw-normal"></span></h6>
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
                                <div class="col-12 col-lg-auto">
                                    <h6>Scheduled Dentist: <span id="aptdtlsDentist" class="fw-normal"></span></h6>
                                    <h6>Oral Concern: <span id="aptdtlsConcern" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr class="aptdtlsVerdictDiv">

                            <div class="align-items-start row">
                                <div class="col-12 col-lg aptdtlsVerdictDiv">
                                    <h6><span class="aptdtlsVerdict"></span> Date: <span id="aptdtlsVerdictDate" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsVerdict"></span> Time: <span id="aptdtlsVerdictTime" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsVerdict" class="fw-normal"></span> By: <span id="aptdtlsVerdictBy" class="fw-normal"></span></h6>
                                </div>

                                <div class="col-12 col-lg aptdtlsReasonDiv">
                                    <h6><span class="fw-normal"></span> Reason: <span id="aptdtlsReason" class="fw-normal"></span></h6>
                                    <h6 class="aptdtlsVerdictOther"><span class="fw-normal"></span> Other Details: <span id="aptdtlsReasonOther" class="fw-normal"></span></h6>
                                </div>
                            </div>                  
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="updateStatusBtn" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientUpdateStatusModal">Update Appointment</button>
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
                                            <span class="h6">Personal Information</span>
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
                                            <span class="h6">Dental History</span>
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
                                            <span class="h6">Medical History</span>
                                        </button>
                                    </h2>
                                    <div id="medicalInfo" class="accordion-collapse collapse" data-bs-parent="#patientView">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end float-end">
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Records">
                                                    <button id="" class="btn btn-outline-secondary mb-3 position-relative" data-bs-toggle="modal" data-bs-target="#medicalHistoryLogsModal">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start row">
                                                <div class="col">
                                                    <div class="row">
                                                        <h6 class="col-xl-6">Name of Physician: <span class="fw-normal" id="physician_name"></span></h6>
                                                        <h6 class="col">Speciality: <span class="fw-normal" id="speciality"></span></h6>
                                                    </div>
                                                    <div class="row">
                                                        <h6 class="col-xl-6">Office Address: <span class="fw-normal" id="office_address"></span></h6>
                                                        <h6 class="col">Office Number: <span class="fw-normal" id="office_number"></span></h6>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <h6>Is in good health: <span class="fw-normal" id="is_good_health"></span></h6>
                                                        <h6>Is under medical treatment now: <span class="fw-normal" id="is_under_treatment"></span></h6>
                                                        <h6>Had serious illness or surgical operation: <span class="fw-normal" id="had_operation"></span></h6>
                                                        <h6>Had been hospitalized: <span class="fw-normal" id="had_hospitalized"></span></h6>
                                                        <h6>Is taking prescription/non-prescription medication: <span class="fw-normal" id="is_taking_prescription"></span></h6>
                                                        <h6>Uses tobacco products: <span class="fw-normal" id="uses_tobacco"></span></h6>
                                                        <h6>Uses alcohol, cocaine, or other dangerous drugs: <span class="fw-normal" id="uses_alcohol_drugs"></span></h6>
                                                        <?php 
                                                            $allergic = [
                                                                "is_allergic_anesthetic" => "Local Anesthetic",
                                                                "is_allergic_aspirin" => "Aspirin",
                                                                "is_allergic_penicillin" => "Penicillin, Antibiotics",
                                                                "is_allergic_latex" => "Latex",
                                                                "is_allergic_sulfa" => "Sulfa Drugs",
                                                                "is_allergic_others" => "Others: " . ($medicalData['is_allergic_others_other'] ?? "")
                                                            ];
                                                        ?>
                                                        <h6>Is allergic with the following: <span class="fw-normal" id=""></span></h6>
                                                                                                    
                                                        <div class="row col-xl-5 d-flex justify-content-start ms-3">
                                                            <h6>• Local Anesthetic: <span class="fw-normal" id="is_allergic_anesthetic"></span></h6>
                                                            <h6>• Aspirin: <span class="fw-normal" id="is_allergic_aspirin"></span></h6>
                                                            <h6>• Penicillin, Antibiotics: <span class="fw-normal" id="is_allergic_penicillin"></span></h6>
                                                            <h6>• Latex: <span class="fw-normal" id="is_allergic_latex"></span></h6>
                                                            <h6>• Sulfa Drugs: <span class="fw-normal" id="is_allergic_sulfa"></span></h6>
                                                            <h6>• Others: <span class="fw-normal" id="is_allergic_others"></span></h6>
                                                        </div>

                                                        <h6>Bleeding Time: <span class="fw-normal" id="bleeding_time"></span></h6>

                                                        <h6>For women only: <span class="fw-normal" id="for_women_only"></span></h6>

                                                        <div class="row d-flex justify-content-start ms-3">
                                                            <h6>Is pregnant: <span class="fw-normal" id="is_pregnant"></span></h6>
                                                            <h6>Is nursing: <span class="fw-normal" id="is_nursing"></span></h6>
                                                            <h6>Is taking birth control pills: <span class="fw-normal" id="is_birth_control"></span></h6>
                                                        </div>

                                                        <h6>Blood Type <span class="fw-normal" id="blood_type"></span></h6>
                                                        <h6>Blood Pressure <span class="fw-normal" id="blood_pressure"></span></h6>

                                                        <div class="row d-flex justify-content-start">
                                                            <?php
                                                                $illness = [
                                                                    "high_blood_pressure" => "High Blood Pressure", "low_blood_pressure" => "Low Blood Pressure",
                                                                    "epilepsy_convulsions" => "Epilepsy / Convulsions", "aids_hiv_infection" => "AIDS / HIV Infection",
                                                                    "sexually_transmitted_disease" => "Sexually Transmitted Disease", "stomach_troubles_ulcers" => "Stomach Troubles / Ulcers",
                                                                    "fainting_seizure" => "Fainting / Seizure", "rapid_weight_loss" => "Rapid Weight Loss",
                                                                    "radiation_therapy" => "Radiation Therapy", "joint_replacement_implant" => "Joint Replacement / Implant",
                                                                    "heart_surgery" => "Heart Surgery", "heart_attack" => "Heart Attack", "thyroid_problem" => "Thyroid Problem",
                                                                    "heart_disease" => "Heart Disease", "heart_murmur" => "Heart Murmur", "hepatitis_liver_disease" => "Hepatitis / Liver Disease",
                                                                    "rheumatic_fever" => "Rheumatic Fever", "hay_fever_allergies" => "Hay Fever / Allergies",
                                                                    "respiratory_problems" => "Respiratory Problems", "hepatitis_jaundice" => "Hepatitis / Jaundice",
                                                                    "tuberculosis" => "Tuberculosis", "swollen_ankles" => "Swollen Ankles", "kidney_disease" => "Kidney Disease",
                                                                    "diabetes" => "Diabetes", "chest_pain" => "Chest Pain", "stroke" => "Stroke", "cancer_tumors" => "Cancer / Tumors",
                                                                    "anemia" => "Anemia", "angina" => "Angina", "asthma" => "Asthma", "emphysema" => "Emphysema",
                                                                    "bleeding_problems" => "Bleeding Problems", "blood_diseases" => "Blood Diseases", "head_injuries" => "Head Injuries",
                                                                    "arthritis_rheumatism" => "Arthritis / Rheumatism", "other" => "Others: " . ($medicalData['other_illness'] ?? "")
                                                                ];

                                                                $hasIllness = false;
                                                                
                                                                if (!empty($medicalData)) {
                                                                    foreach ($illness as $key => $value) {
                                                                        if ($medicalData[$key] === 1) {
                                                                            $hasIllness = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }

                                                                if (!empty($medicalData)) {
                                                                    $count = 0;
                                                                    echo "<h6>Had or have the following: <span class='fw-normal'>" . ($hasIllness ? "Yes" : "No") . "</span></h6>";
                                                                    echo "<div class='col-6 col-sm-6 col-lg-3'>";
                                                                    foreach ($illness as $key => $value) {
                                                                        if ($medicalData[$key] === 1) {
                                                                            if ($count % 12 == 0 && $count != 0) {
                                                                                echo "</div><div class='col-6 col-sm-6 col-lg-3'>";
                                                                            }
                                                                            echo "<h6>• <span class='fw-normal'>$value</span></h6>";
                                                                            $count ++;
                                                                        }
                                                                    }
                                                                    echo "</div>";
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="treatmentItem" class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#treatmentInfo" aria-expanded="false" aria-controls="treatmentInfo">
                                            <span class="h6">Treatment History</span>
                                        </button>
                                    </h2>
                                    <div id="treatmentInfo" class="accordion-collapse collapse" data-bs-parent="#patientView">
                                        <div class="accordion-body">
                                            <div class="col-12 col-sm">
                                                <table id="treatmentTable" class="table-group-divider table table-hover table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Appointment ID</th>
                                                            <th>Tooth No.</th>
                                                            <th>Dentist Note</th>
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
                        <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
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
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#patientViewModal">Back</button>
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
                                        } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
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
            let patient_id;

            loadTable();
            loadModal();
            inputFilters();

            $('#appointmentListTable thead th').eq(3).attr('width', '0%');

            function loadTable() {
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
                        topStart: {
    
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
                            targets: [0,1,2,3,4],
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
                refreshTreatment(patient_id);
                loadDetails(id);
            });

            $('#updateStatusSaveBtn').on('click', function () {
                showLoader();
                let id = $(this).attr('value');
                let patiend_id = $(this).attr('data-p-id');
                let dentist_id = $("#patientChangeDentist").val();
                let setStatus = $("#patientUpdateStatus").val();
                let reason = $("#reason").val();
                let reasonOther = $("#reasonOther").val();

                var formData = {
                    id: id,
                    dentist_id: dentist_id,
                    setStatus: setStatus,
                    reason: reason,
                    reasonOther: reasonOther
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
                    loadDetails(id);
                    $('#patientUpdateStatusModal').modal('hide');
                    $('#appointListModal').modal('show');
                    $("#patientUpdateStatus option, #reasonDiv option").prop('selected', function() {
                        return this.defaultSelected;
                    });
                    $("#reasonOther").val("");
                    hideLoader();
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
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
                            $(".aptdtlsstatus").removeClass("text-danger text-warning text-secondary").addClass("text-success");
                            break;

                        case "Denied":
                        case "Cancelled":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").show();
                            $("#aptdtlsUpdateStatus").hide();
                            $(".aptdtlsstatus").removeClass("text-success text-warning text-secondary").addClass("text-danger");
                            if (data.ReasonOther || data.CancelReasonOther) {
                                $(".aptdtlsVerdictOther").show();
                            } else {
                                $(".aptdtlsVerdictOther").hide();
                            }
                            break;
                        
                        case "Evaluated":
                            $(".aptdtlsVerdictDiv").show();
                            $(".aptdtlsReasonDiv").hide();
                            $(".aptdtlsstatus").removeClass("text-danger text-warning text-success").addClass("text-secondary");
                            break;
                    
                        default:
                            $(".aptdtlsVerdictDiv").hide();
                            $(".aptdtlsReasonDiv").hide();
                            $("#aptdtlsUpdateStatus").hide();
                            $(".aptdtlsstatus").removeClass("text-success text-danger text-secondary").addClass("text-warning");
                            break;
                    }

                    let aptDetails = [data.Name, data.Status, data.Status, data.Dentist, data.Start_Date, data.Start_Time, data.Request_Date, data.Request_Time, data.Approved_By, data.Concern];
                    let aptDetailsId = [".aptdtlsname", ".aptdtlsstatus", ".aptdtlsVerdict", "#aptdtlsDentist", ".aptdtlsStartDate", ".aptdtlsStartTime", "#aptdtlsRequestDate", "#aptdtlsRequestTime", "#aptdtlsVerdictBy", "#aptdtlsConcern"];

                    for (let index = 0; index < aptDetails.length; index++) {
                        $(aptDetailsId[index]).text(aptDetails[index]);
                    }

                    switch (data.Status) {
                        case "Cancelled":
                            $("#aptdtlsReason").text(data.CancelReason);
                            $("#aptdtlsReasonOther").text(data.CancelReasonOther);
                            $("#aptdtlsVerdictDate").text(data.Cancel_Date);
                            $("#aptdtlsVerdictTime").text(data.Cancel_Time);
                            $("#aptdtlsVerdictBy").text("The Client");
                            break;                            
                        case "Denied":
                            $("#aptdtlsVerdictDate").text(data.Approved_Date);
                            $("#aptdtlsVerdictTime").text(data.Approved_Time);
                            $("#aptdtlsReason").text(data.Reason);
                            $("#aptdtlsReasonOther").text(data.ReasonOther);
                            break;
                        case "Evaluated":
                            $("#aptdtlsVerdictDate").text(data.Examined_Date);
                            $("#aptdtlsVerdictTime").text(data.Examined_Time);
                            $("#aptdtlsVerdictBy").text(data.Dentist);
                            break;
                        default:
                            $("#aptdtlsVerdictDate").text(data.Approved_Date);
                            $("#aptdtlsVerdictTime").text(data.Approved_Time);
                            $("#aptdtlsReason").text("");
                            $("#aptdtlsReasonOther").text("");     
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
                let dentistID = $(this).attr('data-dentist-id');
                $("#patientChangeDentist").val(dentistID);
                $('#patientUpdateStatus').val(1);
                $("#reasonDiv").hide();
                $("#reasonOtherDiv").hide();
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