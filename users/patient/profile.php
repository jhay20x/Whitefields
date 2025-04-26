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
        $today = new DateTime(); // Current date
        $age = $today->diff($birthDate)->y; // Calculate age in years
        return $age;
    }

    function fetchDentalHistory($conn, $id) {
        $stmt = $conn->prepare("SELECT dh.prev_dentist AS prevDentist, dh.last_dental AS lastDental FROM dental_history dh WHERE dh.patient_id = ? ORDER BY dh.timestamp DESC");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [];

            $data['prevDentist'] = $row['prevDentist'] ?? "Not Set";
            $data['lastDental'] = $row['lastDental'] ?? "Not Set";
            $data['hasDental'] = true;
            
            // $data['prevDentist'] = str_starts_with($row['prevDentist'], "Updated") ? substr($data['prevDentist'],12) : ($row['prevDentist'] ?? "None");
            
            return $data;
        } else {
            $data['hasDental'] = false;

            return $data;
        }
    }
    function fetchProfileDetails($conn, $id) {
        $stmt = $conn->prepare("SELECT pi.lname, pi.fname, pi.mname, pi.suffix, pi.contactno AS contnum, pi.bdate, pi.gender, 
            pi.religion, pi.nationality, pi.occupation, pi.address, ac.email_address AS email, ac.username
            FROM `patient_info` pi
            LEFT OUTER JOIN accounts ac
            ON ac.id = pi.accounts_id
            WHERE pi.id = ?;");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        $fields = ["fname", "lname", "mname", "suffix", "gender", "religion", "nationality", "contnum", "address", "occupation", "email", "username",];

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
    function fetchMedicalHistory($conn, $id) {
        $stmt = $conn->prepare("SELECT mh.physician_name, mh.speciality, mh.office_address, mh.office_number,
            mq.*, il.*
            FROM medical_history mh
            LEFT OUTER JOIN medical_questions mq
            ON mq.id = mh.medical_questions_id
            LEFT OUTER JOIN illness_list il
            ON il.id = mh.illness_list_id
            WHERE mh.patient_id = ?
            ORDER BY mh.timestamp DESC;");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $fields = [
            "physician_name", "speciality", "office_address", "office_number", "id", "patient_id", "timestamp",
            "is_good_health", "is_under_treatment", "is_under_treatment_condition", "had_operation", "had_operation_illness",
            "had_hospitalized", "had_hospitalized_when", "had_hospitalized_why", "is_taking_prescription", "is_taking_prescription_medication",
            "uses_tobacco", "uses_alcohol_drugs", "is_allergic_anesthetic", "is_allergic_penicillin", "is_allergic_sulfa",
            "is_allergic_aspirin", "is_allergic_latex", "is_allergic_others", "is_allergic_others_other", "bleeding_time",
            "is_pregnant", "is_nursing", "is_birth_control", "blood_type", "blood_pressure", "high_blood_pressure",
            "low_blood_pressure", "epilepsy_convulsions", "aids_hiv_infection", "sexually_transmitted_disease", "stomach_troubles_ulcers",
            "fainting_seizure", "rapid_weight_loss", "radiation_therapy", "joint_replacement_implant", "heart_surgery", "heart_attack",
            "thyroid_problem", "heart_disease", "heart_murmur", "hepatitis_liver_disease", "rheumatic_fever", "hay_fever_allergies",
            "respiratory_problems", "hepatitis_jaundice", "tuberculosis", "swollen_ankles", "kidney_disease", "diabetes",
            "chest_pain", "stroke", "cancer_tumors", "anemia", "angina", "asthma", "emphysema", "bleeding_problems",
            "blood_diseases", "head_injuries", "arthritis_rheumatism", "other", "other_illness"
        ];
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [];
            
            foreach ($fields as $value) {
                $data[$value] = $row[$value] ?? null;
            }

            $data['hasMedical'] = true;

            return $data;
        } else {            
            $data['hasMedical'] = false;

            return $data;
        }
    }
    
    if ($_SESSION['account_type'] == 2) {
        $id = fetchPatientID();

        $profileData = [];
        $dentalData = [];
        $medicalData = [];

        if (is_int($id)) {
            $dentalData = fetchDentalHistory($conn, $id);
            $profileData = fetchProfileDetails($conn, $id);
            $medicalData = fetchMedicalHistory($conn, $id);
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

            #medicalQuestions div h5{
                font-size: 1rem;
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
                        <i class="bi bi-person"></i> Profile Information
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="profileClose" aria-label="Close"></button>
                </div>
                <form autocomplete="off" action="php/insert-update-info.php" method="POST" class="text-center col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="fname" placeholder="First Name"  id="fname" class="form-control onlyLetters">
                                        <label for="fname">First Name</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input type="text" name="mname" placeholder="Middle Name" id="mname" class="form-control onlyLetters">
                                            <label for="mname">Middle Name</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox">
                                            <label class="ms-1" for="nomname">N/A</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="lname" placeholder="Last Name"  id="lname" class="form-control onlyLetters">
                                        <label for="lname">Last Name</label>
                                    </div>
                                </div>
                                    
                                <div class="col-lg">
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
                                <div class="col-lg-3">
                                    <div class="form-floating mb-3">
                                        <select required class="form-select" name="gender" id="gender">
                                            <option disabled selected>Select...</option>
                                            <option value="Female">Female</option>
                                            <option value="Male">Male</option>
                                            <!-- <option value="Nonbinary">Nonbinary</option> -->
                                            <!-- <option value="Other">Other</option> -->
                                            <option value="Decline to state">Decline to state</option>
                                        </select>
                                        <label class="ms-1" for="gender">Gender</label>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-floating mb-3">
                                        <input type="date" name="bdate" placeholder="Code"  id="bdate" class="form-control">
                                        <label for="bdate">Birth Date</label>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="address" placeholder="Address"  id="address" class="form-control onlyAddress">
                                        <label for="address">Address</label>
                                    </div>                                             
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="religion" placeholder="Religion"  id="religion" class="form-control onlyLetters">
                                        <label for="religion">Religion</label>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="nationality" placeholder="Nationality"  id="nationality" class="form-control onlyLetters">
                                        <label for="nationality">Nationality</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" maxlength="11" name="contnumber" placeholder="Contact Number"  id="contnumber" class="form-control onlyNumbers">
                                        <label for="contnumber">Contact No.</label>
                                    </div>
                                </div>
                                    
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input required type="text" name="occupation" placeholder="Occupation" id="occupation" class="form-control onlyLetters">
                                            <label for="occupation">Occupation</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="nooccupation" name="nooccupation" type="checkbox">
                                            <label class="ms-1" for="nooccupation">N/A</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-sm btn-outline-primary btn-md mt-1" value="Submit" name="profileSubmitBtn">
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

    <!-- Modal -->
    <div class="modal fade" id="dentalHistoryModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dentalHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="dentalHistoryLabel">
                        <i class="bi bi-person-vcard"></i> Dental History
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentalHistoryClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="dentalErrorMessage" class="col-12" role="alert">
                            <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger alert-dismissible fade show">Please complete your profile first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>' ?>
                        </div>

                        <form autocomplete="off" action="php/insert-dental-history.php" method="POST" class="text-center col" id="dentalHistory">
                            <div class="row">                                
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="text" required name="prevDentist" placeholder="Name"  id="prevDentist" class="form-control onlyLetters">
                                        <label for="prevDentist">Previous Dentist:</label>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input type="date" required name="lastDentalVisit" id="lastDentalVisit" class="form-control">
                                        <label for="lastDentalVisit">Last Dental Visit:</label>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-sm btn-outline-primary btn-md mt-1" value="Submit" name="dentalHistorySubmitBtn">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="medicalHistoryModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="medicalHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="medicalHistoryLabel">
                        <i class="bi bi-file-medical"></i> Medical History
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="medicalHistoryClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                        
                        <div id="medicalErrorMessage" class="col-12" role="alert">
                            <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger alert-dismissible fade show">Please complete your profile first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>' ?>
                        </div>

                        <form autocomplete="off" action="php/insert-medical-history.php" method="POST" class="row" id="medicalHistory">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="text" maxlength="100" name="physician_name" placeholder="Physician Name"  id="physicianName" class="form-control onlyLetters">
                                            <label for="physicianName">Name of Physician</label>
                                        </div>
                                    </div>

                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="text" maxlength="50" name="speciality" placeholder="Speciality"  id="speciality" class="form-control onlyLetters">
                                            <label for="speciality">Speciality<small class="ms-1">(If applicable)</small></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="text" maxlength="100" name="office_address" placeholder="Address"  id="officeAddress" class="form-control onlyAddress">
                                            <label for="officeAddress">Office Address</label>
                                        </div>
                                    </div>

                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="text" maxlength="20" name="office_number" placeholder="Number"  id="officeNumber" class="form-control onlyNumbers">
                                            <label for="officeNumber">Office Number</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="medicalQuestions" class="row mt-3">
                                    <div class="col">
                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>1. Are you in good health?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_good_health" id="isGoodHealthYes">
                                                            <label class="form-check-label" for="isGoodHealthYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_good_health" id="isGoodHealthNo">
                                                            <label class="form-check-label" for="isGoodHealthNo">No</label>
                                                        </div>
                                                    </div>
                                                    <div id="isGoodHealthDiv" class="row">
                                                        <input type="hidden" name="is_good_health" value="Yes">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>2. Are you under medical treatment now?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_under_treatment" id="isUnderTreatmentYes">
                                                            <label class="form-check-label" for="isUnderTreatmentYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_under_treatment" id="isUnderTreatmentNo">
                                                            <label class="form-check-label" for="isUnderTreatmentNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="isUnderTreatmentDiv" class="row">
                                                <input type="hidden" name="is_under_treatment" value="Yes">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="50" id="is_under_treatment_condition" name="is_under_treatment_condition" required placeholder="If so, what is the condition being treated?" id="isUnderTreatmentCondition" class="form-control">
                                                        <label for="is_under_treatment_condition">Condition</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>3. Have you ever had serious illness or surgical operation?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="had_operation" id="hadOperationYes">
                                                            <label class="form-check-label" for="hadOperationYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="had_operation" id="hadOperationNo">
                                                            <label class="form-check-label" for="hadOperationNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="hadOperationDiv" class="row">
                                                <input type="hidden" name="had_operation" value="Yes">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="50" id="had_operation_illness" name="had_operation_illness" required placeholder="If so, what illness or operation?" id="hadOperationIllness" class="form-control">
                                                        <label for="had_operation_illness">Illness / Operation</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>4. Have you ever been hospitalized?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="had_hospitalized" id="hadHospitalizedYes">
                                                            <label class="form-check-label" for="hadHospitalizedYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="had_hospitalized" id="hadHospitalizedNo">
                                                            <label class="form-check-label" for="hadHospitalizedNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="hadHospitalizedDiv" class="row">
                                                <input type="hidden" name="had_hospitalized" value="Yes">
                                                <div class="col-lg-6">
                                                    <div class="form-floating mb-lg-0 mb-3">
                                                        <input type="date" name="had_hospitalized_when" required placeholder="Address" id="had_hospitalized_when" class="form-control">
                                                        <label for="had_hospitalized_when">If so, when</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="50" name="had_hospitalized_why" required placeholder="Reason" id="had_hospitalized_why" class="form-control">
                                                        <label for="had_hospitalized_why">and why?</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>5. Are you taking any prescription/non-prescription medication?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_taking_prescription" id="isTakingPrescriptionYes">
                                                            <label class="form-check-label" for="isTakingPrescriptionYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="is_taking_prescription" id="isTakingPrescriptionNo">
                                                            <label class="form-check-label" for="isTakingPrescriptionNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="isTakingPrescriptionDiv" class="row">
                                                <input type="hidden" name="is_taking_prescription" value="Yes">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="50" name="is_taking_prescription_medication" required placeholder="If so, please specify?" id="isTakingPrescriptionMedication" class="form-control">
                                                        <label for="isTakingPrescriptionMedication">Medication(s)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>6. Do you use tobacco products?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="uses_tobacco" id="usesTobaccoYes">
                                                            <label class="form-check-label" for="usesTobaccoYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="uses_tobacco" id="usesTobaccoNo">
                                                            <label class="form-check-label" for="usesTobaccoNo">No</label>
                                                        </div>
                                                    </div>
                                                    <div id="usesTobaccoDiv" class="row">
                                                        <input type="hidden" name="uses_tobacco" value="Yes">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>7. Do you use alcohol, cocaine, or other dangerous drugs?</h5>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row d-flex justify-content-lg-end">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="uses_alcohol_drugs" id="usesAlcoholDrugsYes">
                                                            <label class="form-check-label" for="usesAlcoholDrugsYes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" required type="radio" name="uses_alcohol_drugs" id="usesAlcoholDrugsNo">
                                                            <label class="form-check-label" for="usesAlcoholDrugsNo">No</label>
                                                        </div>
                                                    </div>
                                                    <div id="usesAlcoholDrugsDiv" class="row">
                                                        <input type="hidden" name="uses_alcohol_drugs" value="Yes">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>8. Are you allergic to any of the following:</h5>
                                            </div>
                                            <div class="row d-flex justify-content-start">
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_anesthetic" id="isAllergicAnesthetic">
                                                        <label class="form-check-label" for="isAllergicAnesthetic">Local Anesthetic (ex. Lidocaine)</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_penicillin" id="isAllergicPenicillin">
                                                        <label class="form-check-label" for="isAllergicPenicillin">Penicillin, Antibiotics</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_sulfa" id="isAllergicSulfa">
                                                        <label class="form-check-label" for="isAllergicSulfa">Sulfa Drugs</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_aspirin" id="isAllergicAspirin">
                                                        <label class="form-check-label" for="isAllergicAspirin">Aspirin</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_latex" id="isAllergicLatex">
                                                        <label class="form-check-label" for="isAllergicLatex">Latex</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="is_allergic_others" id="isAllergicOthers">
                                                        <label class="form-check-label" for="isAllergicOthers">Others</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="isAllergicOthersDiv" class="row">
                                                <div class="col-lg-6">
                                                    <div class="input-group">
                                                        <input type="text" maxlength="50" name="is_allergic_others_other" required placeholder="If Others, please specify" id="isAllergicOthersOther" class="form-control onlyLetters">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>9. Bleeding Time <span class="fs-6 fw-normal">(Duration in minutes, if applicable)</span></h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="5" name="bleeding_time" placeholder="Bleeding Time"  id="bleeding_time" class="form-control onlyNumbers">
                                                        <label for="bleeding_time">Bleeding Time</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>10. For Women Only:</h5>
                                                <?php if (($profileData['gender'] ?? "") === "Female") { ?>
                                                <div class="row ms-3">
                                                    <div class="col-12 col-md">
                                                        <h6>Are you pregnant?</h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="row d-flex justify-content-lg-end">
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_pregnant" id="isPregnantYes">
                                                                    <label class="form-check-label" for="isPregnantYes">Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_pregnant" id="isPregnantNo">
                                                                    <label class="form-check-label" for="isPregnantNo">No</label>
                                                                </div>
                                                            </div>
                                                            <div id="isPregnantDiv" class="row">
                                                                <input type="hidden" name="is_pregnant" value="Yes">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row ms-3">
                                                    <div class="col-12 col-md">
                                                        <h6>Are you nursing?</h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="row d-flex justify-content-lg-end">
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_nursing" id="isNursingYes">
                                                                    <label class="form-check-label" for="isNursingYes">Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_nursing" id="isNursingNo">
                                                                    <label class="form-check-label" for="isNursingNo">No</label>
                                                                </div>
                                                            </div>
                                                            <div id="isNursingDiv" class="row">
                                                                <input type="hidden" name="is_nursing" value="Yes">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row ms-3">
                                                    <div class="col-12 col-md">
                                                        <h6>Are taking birth control pills?</h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="row d-flex justify-content-lg-end">
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_birth_control" id="isBirthControlYes">
                                                                    <label class="form-check-label" for="isBirthControlYes">Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" required type="radio" name="is_birth_control" id="isBirthControlNo">
                                                                    <label class="form-check-label" for="isBirthControlNo">No</label>
                                                                </div>
                                                            </div>
                                                            <div id="isBirthControlDiv" class="row">
                                                                <input type="hidden" name="is_birth_control" value="Yes">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } echo $profileData['gender'] === "Female" ? "" : "<h6>N/A</h6>";?>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>11. Blood Type <span class="fs-6 fw-normal">(If applicable)</span></h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="5" name="blood_type" placeholder="Blood Type"  id="blood_type" class="form-control onlyBloodType">
                                                        <label for="blood_type">Blood Type</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>12. Blood Pressure <span class="fs-6 fw-normal">(Measured in mmHg, if applicable)</span></h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-floating">
                                                        <input type="text" maxlength="7" name="blood_pressure" placeholder="Blood Pressure"  id="blood_pressure" class="form-control onlyBlood">
                                                        <label for="blood_pressure">Blood Pressure</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="row my-3">
                                            <div class="col-auto">
                                                <h5>13. Do you have or have you had any of the following? Check which apply:</h5>
                                            </div>
                                            <div class="row d-flex justify-content-start">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="high_blood_pressure" id="highBloodPressure">
                                                        <label class="form-check-label" for="highBloodPressure">High Blood Pressure</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="low_blood_pressure" id="lowBloodPressure">
                                                        <label class="form-check-label" for="lowBloodPressure">Low Blood Pressure</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="epilepsy_convulsions" id="epilepsy">
                                                        <label class="form-check-label" for="epilepsy">Epilepsy / Convulsions</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="aids_hiv_infection" id="hivAids">
                                                        <label class="form-check-label" for="hivAids">AIDS or HIV Infection</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="sexually_transmitted_disease" id="std">
                                                        <label class="form-check-label" for="std">Sexually Transmitted Disease</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="stomach_troubles_ulcers" id="stomachUlcers">
                                                        <label class="form-check-label" for="stomachUlcers">Stomach Troubles / Ulcers</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="fainting_seizure" id="faintingSeizures">
                                                        <label class="form-check-label" for="faintingSeizures">Fainting Seizure</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="rapid_weight_loss" id="rapidWeightLoss">
                                                        <label class="form-check-label" for="rapidWeightLoss">Rapid Weight Loss</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="radiation_therapy" id="radiationTherapy">
                                                        <label class="form-check-label" for="radiationTherapy">Radiation Therapy</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="joint_replacement_implant" id="jointImplant">
                                                        <label class="form-check-label" for="jointImplant">Joint Replacement / Implant</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="heart_surgery" id="heartSurgery">
                                                        <label class="form-check-label" for="heartSurgery">Heart Surgery</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="heart_attack" id="heartAttack">
                                                        <label class="form-check-label" for="heartAttack">Heart Attack</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="thyroid_problem" id="thyroidProblem">
                                                        <label class="form-check-label" for="thyroidProblem">Thyroid Problem</label>
                                                    </div>
                                                </div>

                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="heart_disease" id="heartDisease">
                                                        <label class="form-check-label" for="heartDisease">Heart Disease</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="heart_murmur" id="heartMurmur">
                                                        <label class="form-check-label" for="heartMurmur">Heart Murmur</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="hepatitis_liver_disease" id="liverDisease">
                                                        <label class="form-check-label" for="liverDisease">Hepatitis / Liver Disease</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="rheumatic_fever" id="rheumaticFever">
                                                        <label class="form-check-label" for="rheumaticFever">Rheumatic Fever</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="hay_fever_allergies" id="allergies">
                                                        <label class="form-check-label" for="allergies">Hay Fever / Allergies</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="respiratory_problems" id="respiratoryProblems">
                                                        <label class="form-check-label" for="respiratoryProblems">Respiratory Problems</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="hepatitis_jaundice" id="hepatitisJaundice">
                                                        <label class="form-check-label" for="hepatitisJaundice">Hepatitis / Jaundice</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="tuberculosis" id="tuberculosis">
                                                        <label class="form-check-label" for="tuberculosis">Tuberculosis</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="swollen_ankles" id="swollenAnkles">
                                                        <label class="form-check-label" for="swollenAnkles">Swollen Ankles</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="kidney_disease" id="kidneyDisease">
                                                        <label class="form-check-label" for="kidneyDisease">Kidney Disease</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="diabetes" id="diabetes">
                                                        <label class="form-check-label" for="diabetes">Diabetes</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="chest_pain" id="chestPain">
                                                        <label class="form-check-label" for="chestPain">Chest Pain</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="stroke" id="stroke">
                                                        <label class="form-check-label" for="stroke">Stroke</label>
                                                    </div>
                                                </div>

                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="cancer_tumors" id="cancer">
                                                        <label class="form-check-label" for="cancer">Cancer / Tumors</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="anemia" id="anemia">
                                                        <label class="form-check-label" for="anemia">Anemia</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="angina" id="angina">
                                                        <label class="form-check-label" for="angina">Angina</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="asthma" id="asthma">
                                                        <label class="form-check-label" for="asthma">Asthma</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="emphysema" id="emphysema">
                                                        <label class="form-check-label" for="emphysema">Emphysema</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="bleeding_problems" id="bleedingProblems">
                                                        <label class="form-check-label" for="bleedingProblems">Bleeding Problems</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="blood_diseases" id="bloodDiseases">
                                                        <label class="form-check-label" for="bloodDiseases">Blood Diseases</label>
                                                    </div>
        
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="head_injuries" id="headInjuries">
                                                        <label class="form-check-label" for="headInjuries">Head Injuries</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="arthritis_rheumatism" id="arthritis">
                                                        <label class="form-check-label" for="arthritis">Arthritis / Rheumatism</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="other" id="other">
                                                        <label class="form-check-label" for="other">Other</label>
                                                    </div>

                                                    <div id="otherDiv" class="col-lg">
                                                        <div class="input-group">
                                                            <input type="text" maxlength="50" name="other_illness" required placeholder="If Other, please specify" id="otherIllness" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <input type="submit" class="btn btn-sm btn-outline-primary btn-md mt-1" value="Submit" name="medicalSubmitBtn">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dentalHistoryLogsModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dentalHistoryLogsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="dentalHistoryLogsLabel">
                        <i class="bi bi-person-vcard"></i> Dental History Records
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentalHistoryLogsClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="height: 300px;">
                        <table id="myTable" class="table">
                            <thead>
                                <tr>
                                    <th class="col">Remarks</th>
                                    <th class="col">Visit Date</th>
                                    <th class="col">Edit Timestamp</th>
                                </tr>
                            </thead>

                            <tbody id="tableBody">
                                <?php
                                    $stmt = $conn->prepare("SELECT dl.*, dl.visit_date  FROM `dental_history_logs` dl
                                        LEFT OUTER JOIN dental_history dh
                                        ON dh.id = dl.dental_history_id
                                        WHERE dl.patient_id = ?
                                        ORDER BY timestamp DESC;");
                                    $stmt->bind_param('i', $id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $stmt->close();

                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $lastVisit = date('F d, Y', strtotime($row['visit_date']));
                                            $timestamp = date('F d, Y h:i:s A', strtotime($row['timestamp']));
                                            echo '
                                            <tr>
                                                <td>' . $row['remarks'] . '</td>
                                                <td>' . $lastVisit . '</td>
                                                <td>' . $timestamp . '</td>
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

    <!-- Modal -->
    <div class="modal fade" id="medicalHistoryLogsModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="medicalHistoryLogsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="medicalHistoryLogsLabel">
                        <i class="bi bi-file-medical"></i> Medical History Records
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="medicalHistoryLogsClose" aria-label="Close"></button>
                </div>
                <div class="table-responsive" style="height: 400px;">
                    <div class="accordion accordion-flush" id="medicalHistoryLogsAcc">
                        <?php
                            $stmt = $conn->prepare("SELECT * FROM medical_history_logs
                            WHERE patient_id = ?
                            ORDER BY id DESC;");
                            $stmt->bind_param('i', $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            if ($result->num_rows > 0) {
                                $hasMedical = true;

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $title = date('m/d/Y h:i:s A',  strtotime($row['timestamp']));
                                    $data = json_decode($row['remarks'], true);
                        ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $row['id'] ?>" aria-expanded="false" aria-controls="flush-collapse-<?= $row['id'] ?>">
                                                <span class="fw-semibold"><?= $title ?></span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapse-<?= $row['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#medicalHistoryLogsAcc">
                                            <div class="accordion-body">
                                                <div class="table-responsive" style="height: 300px;">
                                                    <table id="myTable" class="table    ">
                                                        <thead>
                                                            <tr>
                                                                <th class="col" colspan="1">Action Made: <span class="fw-normal"><?= $data["type"];?></span></th>
                                                                <th class="col" colspan="2">Timestamp: <span class="fw-normal"><?= $title;?></span></th>
                                                            </tr>
                                                            <tr class="table-group-divider">
                                                            </tr>
                                                            <tr>
                                                                <th class="col">Item</th>
                                                                <th class="col">Value</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="tableBody">
                                                            <?php
                                                                foreach ($data['items'] as $item) {
                                                                    if ($item['value'] !== "null") {
                                                                        echo '
                                                                        <tr>
                                                                            <td>' . $item['desc'] . '</td>
                                                                            <td>' . $item['value'] . '</td>
                                                                        </tr>
                                                                    ';
                                                                        // echo "<h6>• " . $item['desc'] . ": <span class='fw-normal'>" . $item['value'] . "</span></h6>";
                                                                        // echo "<span>• " . $item['desc'] . " = " . ($item['value'] === "null" ? "No" : $item['value']) . "</span>";
                                                                    }
                                                                }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- <div class="row">
                                                    <h6>Action: <?= $data["type"];?></h6>
                                                </div> -->

                                                <!-- <div class="row mt-3">
                                                    <h6>Record Data:</h6>

                                                    <div class="row">
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                        <?php 
                                }
                            } else {
                                $hasMedical = false;
                            }
                        ?>
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
            
            <div class="d-flex rounded shadow flex-column mt-3 col-sm-9 bg-white align-items-center">
                <div id="uploadmessage" class="mt-3" role="alert"></div>

                <div id="profile" class="m-3">
                    <img src="<?php echo $profilePath ? "../../files/{$profilePath}/profile.jpg" : '../../resources/images/blank-profile.webp';?>" alt="" width="300px" height="300px" class="rounded-circle mb-2 border border-5">
                </div>
                
                <div class="m-3 text-center">
                    <form action="php/upload.php" enctype="multipart/form-data" name="uploadForm" method="POST" class="" id="uploadForm">
                        <div class="input-group">
                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                            <input class="btn btn-outline-secondary" type="submit" name="uploadsubmitbtn" value="Upload Image" >
                        </div>
                    </form>
                    <small>*Profile photo might not reflect immediately.</small>
                </div>
            </div>

            <div id="profileForm">
                <div class="d-flex row justify-content-center">
                    <div class="col-sm-9 rounded shadow bg-white m-3 mb-0 p-3 d-flex justify-content-center">
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
                                        <h5 class="">Name: <span class="fw-normal"><?php echo $profileData['fname'] ?? 'Not Set'; ?> <?php echo ($profileData['mname'] ?? '') === "None" || empty($profileData['mname']) ? '' : $profileData['mname']; ?> <?php echo $profileData['lname'] ?? ''; ?> <?php echo ($profileData['suffix'] ?? '') === "None" || empty($profileData['suffix']) ? '' : $profileData['suffix']; ?> </span></h5>
                                        <h5 class="">Username: <span class="fw-normal"><?php echo $profileData['username'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Age: <span class="fw-normal"><?php echo $profileData['age'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Birth Date: <span class="fw-normal"><?php echo $profileData['bdate'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Gender: <span class="fw-normal"><?php echo $profileData['gender'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Occupation: <span class="fw-normal"><?php echo $profileData['occupation'] ?? 'Not Set';?></span></h5>
                                    </div>
                                    <div class="col-xl">
                                        <h5 class="">Contact Number: <span class="fw-normal"><?php echo $profileData['contnum'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Email Address: <span class="fw-normal"><?php echo empty($profileData['email']) || $profileData['email'] == "None" ? 'Not Set' : $profileData['email'];?></span></h5>
                                        <h5 class="">Religion: <span class="fw-normal"><?php echo $profileData['religion'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Nationality: <span class="fw-normal"><?php echo $profileData['nationality'] ?? 'Not Set';?></span></h5>
                                        <h5 class="">Address: <span class="fw-normal"><?php echo $profileData['address'] ?? 'Not Set';?></span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9 rounded shadow bg-white m-3 mb-0 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Dental History</h1>
                                <div class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Records">
                                    <button id="" class="btn btn-outline-secondary position-relative" <?php echo ($dentalData['hasDental'] ?? '') ? '' : 'disabled'; ?> data-bs-toggle="modal" data-bs-target="#dentalHistoryLogsModal">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </div>
                                <div class="col-auto ms-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Dental History">
                                    <button id="" class="btn btn-outline-secondary position-relative" <?php echo ($hasId ?? '') ? '' : 'disabled'; ?> data-bs-toggle="modal" data-bs-target="#dentalHistoryModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>                               
                                </div>
                            </div>
                            
                            <hr>

                            <div class="d-flex justify-content-start row">
                                <div id="dentalMessage" class="col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger alert-dismissible fade show">Please complete your profile first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>' ?>
                                </div>
                                <div class="col-xl">
                                    <h5>Previous Dentist: <span class="fw-normal"><?php echo $dentalData['prevDentist']  ?? "Not Set";?></span></h5>
                                    <h5>Last Dental Visit: <span class="fw-normal"><?php echo  ($dentalData['lastDental'] ?? "") === "None" || empty($dentalData['lastDental']) ? "Not Set" : date("F d, Y", strtotime($dentalData['lastDental']));?></span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-9 rounded shadow bg-white m-3 p-3 d-flex justify-content-center">
                        <div class="p-3 p-md-4 p-lg-4 col">
                            <div class="d-flex align-items-center flex-row">
                                <h1 class="col">Medical History</h1>
                                <div class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Records">
                                    <button id="" class="btn btn-outline-secondary position-relative" <?php echo ($medicalData['hasMedical'] ?? '') ? '' : 'disabled'; ?> data-bs-toggle="modal" data-bs-target="#medicalHistoryLogsModal">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </div>
                                <div class="col-auto ms-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Medical History">
                                    <button id="medicalHistoryEditBtn" class="btn btn-outline-secondary position-relative" <?php echo ($hasId ?? '') ? '' : 'disabled'; ?> data-bs-toggle="modal" data-bs-target="#medicalHistoryModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="d-flex justify-content-start row">
                                <div id="medicalMessage" class="col-12" role="alert">
                                    <?php echo $hasId ? '' : '<div class="mt-3 alert alert-danger alert-dismissible fade show">Please complete your profile first.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>' ?>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <h5 class="col-xl-6">Name of Physician: <span class="fw-normal"><?php echo $medicalData['physician_name'] ?? "Not Set"; ?></span></h5>
                                        <h5 class="col">Speciality: <span class="fw-normal"><?php echo $medicalData['speciality'] ?? "Not Set"; ?></span></h5>
                                    </div>
                                    <div class="row">
                                        <h5 class="col-xl-6">Office Address: <span class="fw-normal"><?php echo $medicalData['office_address'] ?? "Not Set"; ?></span></h5>
                                        <h5 class="col">Office Number: <span class="fw-normal"><?php echo $medicalData['office_number'] ?? "Not Set"; ?></span></h5>
                                    </div>
                                    <div class="row mt-3">
                                        <h5>Is in good health: <span class="fw-normal"><?php echo isset($medicalData['is_good_health']) ? ($medicalData['is_good_health'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h5>
                                        <h5>Is under medical treatment now: <span class="fw-normal"><?php echo isset($medicalData['is_under_treatment']) ? ($medicalData['is_under_treatment'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h5>
                                        <h5>Had serious illness or surgical operation: <span class="fw-normal"><?php echo isset($medicalData['had_operation']) ? ($medicalData['had_operation'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h5>
                                        <h5>Had been hospitalized: <span class="fw-normal"><?php echo isset($medicalData['had_hospitalized']) ? ($medicalData['had_hospitalized'] == "1" ? "Yes, ". ($medicalData['had_hospitalized_when'] ?? "") . ", " . ($medicalData['had_hospitalized_why'] ?? "") : "No") : "Not Set"; ?></span></h5>
                                        <h5>Is taking prescription/non-prescription medication: <span class="fw-normal"><?php echo isset($medicalData['is_taking_prescription']) ? ($medicalData['is_taking_prescription'] == "1" ? "Yes, ". $medicalData['is_taking_prescription_medication'] ?? "" : "No") : "Not Set"; ?></span></h5>
                                        <h5>Uses tobacco products: <span class="fw-normal"><?php echo isset($medicalData['uses_tobacco']) ? ($medicalData['uses_tobacco'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h5>
                                        <h5>Uses alcohol, cocaine, or other dangerous drugs: <span class="fw-normal"><?php echo isset($medicalData['uses_alcohol_drugs']) ? ($medicalData['uses_alcohol_drugs'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h5>
                                        <?php 
                                            $allergic = [
                                                "is_allergic_anesthetic" => "Local Anesthetic",
                                                "is_allergic_aspirin" => "Aspirin",
                                                "is_allergic_penicillin" => "Penicillin, Antibiotics",
                                                "is_allergic_latex" => "Latex",
                                                "is_allergic_sulfa" => "Sulfa Drugs",
                                                "is_allergic_others" => "Others: " . ($medicalData['is_allergic_others_other'] ?? "")
                                            ];

                                            $hasAllergy = false;
                                            
                                            if (!empty($medicalData)) {
                                                foreach ($allergic as $key => $value) {
                                                    if (($medicalData[$key] ?? '') === 1) {
                                                        $hasAllergy = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>

                                        <h5>Is allergic with the following: <span class="fw-normal"><?php echo $hasAllergy ? "Yes" : "None";?></span></h5>

                                        <div class="row col-xl-5 d-flex justify-content-start ms-3">
                                            <?php
                                                if (!empty($medicalData)) {
                                                    foreach ($allergic as $key => $value) {
                                                        if (($medicalData[$key] ?? '') === 1) {
                                                            echo "<h6 class='col-auto'>• $value</h6>";
                                                        }
                                                    }
                                                }
                                            ?>
                                        </div>

                                        <h5>Bleeding Time: <span class="fw-normal"><?php echo ($medicalData['bleeding_time'] ?? "Not Set") ?? 'Not Set'; ?></span></h5>

                                        <?php
                                            if (($profileData['gender'] ?? "") === "Female") {
                                        ?>
                                            <h5>For women only: <span class="fw-normal"></span></h5>

                                            <div class="row d-flex justify-content-start ms-3">
                                                <h6>Is pregnant: <span class="fw-normal"><?php echo isset($medicalData['is_pregnant']) ? ($medicalData['is_pregnant'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h6>
                                                <h6>Is nursing: <span class="fw-normal"><?php echo isset($medicalData['is_nursing']) ? ($medicalData['is_nursing'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h6>
                                                <h6>Is taking birth control pills: <span class="fw-normal"><?php echo isset($medicalData['is_birth_control']) ? ($medicalData['is_birth_control'] == "1" ? 'Yes' : "No") : "Not Set"; ?></span></h6>
                                            </div>
                                        <?php
                                            }
                                        ?>

                                        <h5>Blood Type <span class="fw-normal"><?php echo ($medicalData['blood_type'] ?? "Not Set") ?? 'Not Set'; ?></span></h5>
                                        <h5>Blood Pressure <span class="fw-normal"><?php echo ($medicalData['blood_pressure'] ?? "Not Set") ?? 'Not Set'; ?></span></h5>

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
                                                        if (($medicalData[$key] ?? '') === 1) {
                                                            $hasIllness = true;
                                                            break;
                                                        }
                                                    }
                                                }

                                                if (!empty($medicalData)) {
                                                    $count = 0;
                                                    echo "<h5>Had or have the following: <span class='fw-normal'>" . ($hasIllness ? "Yes" : "None") . "</span></h5>";
                                                    echo "<div class='col-6 col-sm-6 col-lg'>";
                                                    foreach ($illness as $key => $value) {
                                                        if (($medicalData[$key] ?? '') === 1) {
                                                            if ($count % 12 == 0 && $count != 0) {
                                                                echo "</div><div class='col-6 col-sm-6 col-lg'>";
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
        
        $("#isUnderTreatmentDiv, #hadOperationDiv, #hadHospitalizedDiv, #isTakingPrescriptionDiv, #isAllergicOthersDiv, #otherDiv").hide();
        $("#isGoodHealthDiv input, #isUnderTreatmentDiv input, #hadOperationDiv input, #hadHospitalizedDiv input, #isTakingPrescriptionDiv input, #usesTobaccoDiv input, #usesAlcoholDrugsDiv input, #isAllergicOthersDiv input, #isPregnantDiv input, #isNursingDiv input, #isBirthControlDiv input, #otherDiv input").prop('disabled', true);
        
        let medicalInputsId = ["isGoodHealth", "isUnderTreatment", "hadOperation", "hadHospitalized", "isTakingPrescription", "usesTobacco", "usesAlcoholDrugs", "isPregnant", "isNursing", "isBirthControl"];
        let medicalInputsName = ["is_good_health", "is_under_treatment", "had_operation", "had_hospitalized", "is_taking_prescription", "uses_tobacco", "uses_alcohol_drugs", "is_pregnant", "is_nursing", "is_birth_control"];

        medicalInputsName.forEach((input, i) => {
            $(`input[name="${input}"]`).on('click', function () {
                let isYes = this.id === `${medicalInputsId[i]}Yes`;
                let div = $(`#${medicalInputsId[i]}Div`);
                
                div.toggle(isYes);
                div.find('input').prop('disabled', !isYes);
            });
        });

        let otherInputCheck = ["isAllergicOthers", "other"];
        
        otherInputCheck.forEach(input => {
            $(`input[id="${input}"]`).on('click', function () {
                let isChecked = $(`#${input}`).is(':checked');
                let div = $(`#${input}Div`);
                
                div.toggle(isChecked);
                div.find('input').prop('disabled', !isChecked);
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
                    //console.log(data.responseText);
                },
                error: function(data) {
                    //console.log(data.responseText);
                }
            });
        }));

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
                let details = [data.fname, data.lname, data.bdate, data.address, data.gender, data.religion, data.nationality, data.contactno];
                let detailsId = ["#fname", "#lname", "#bdate", "#address", "#gender", "#religion", "#nationality", "#contnumber"];                

                for (let index = 0; index < details.length; index++) {
                    $(detailsId[index]).val(details[index]);
                }

                let fields = [data.mname, data.suffix, data.occupation];
                let nofields = ["#nomname", "#nosuffix", "#nooccupation"];

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
        

        $('body').on('click', '#medicalHistoryEditBtn', function(){
            loadMedicalHistory();
        });

        function loadMedicalHistory() {
            showLoader();
            $.ajax({
                type: "POST",
                url: "php/fetch-medical-history.php",
                dataType: 'json'
            }).done(function(data) {
                hideLoader();
                if (data.length !== 0) {
                    let details = [
                        data.physician_name, data.speciality, data.office_address, data.office_number, data.id, data.patient_id, data.timestamp,
                        data.is_good_health, data.is_under_treatment, data.is_under_treatment_condition, data.had_operation, data.had_operation_illness,
                        data.had_hospitalized, data.had_hospitalized_when, data.had_hospitalized_why, data.is_taking_prescription, data.is_taking_prescription_medication,
                        data.uses_tobacco, data.uses_alcohol_drugs, data.is_allergic_others_other, data.bleeding_time,
                        data.is_pregnant, data.is_nursing, data.is_birth_control, data.blood_type, data.blood_pressure, data.other_illness
                    ];
                    
                    let detailsId = [
                        "physicianName", "speciality", "officeAddress", "officeNumber", "id", "patientId", "timestamp",
                        "isGoodHealth", "isUnderTreatment", "isUnderTreatmentCondition", "hadOperation", "hadOperationIllness",
                        "hadHospitalized", "hadHospitalizedWhen", "hadHospitalizedWhy", "isTakingPrescription", "isTakingPrescriptionMedication",
                        "usesTobacco", "usesAlcoholDrugs", "isAllergicOthersOther", "bleedingTime",
                        "isPregnant", "isNursing", "isBirthControl", "bloodType", "bloodPressure", "otherIllness"
                    ];
    
                    for (let index = 0; index < details.length; index++) {
                        $(`#${detailsId[index]}`).val(details[index]);
                        
                        if (details[index] === 1) {
                            $(`#${detailsId[index]}Yes`).prop("checked", true);
                            $(`#${detailsId[index]}`).prop("checked", true);
                            $(`#${detailsId[index]}Div`).show();
                            $(`#${detailsId[index]}Div input`).prop('disabled', false);
                        } else {
                            $(`#${detailsId[index]}Div`).hide();
                            $(`#${detailsId[index]}No`).prop("checked", true);
                        }
                    }
    
                    let checkBoxes = [
                        data.is_allergic_anesthetic, data.is_allergic_penicillin, data.is_allergic_sulfa,
                        data.is_allergic_aspirin, data.is_allergic_latex, data.is_allergic_others, data.high_blood_pressure,
                        data.low_blood_pressure, data.epilepsy_convulsions, data.aids_hiv_infection, data.sexually_transmitted_disease, data.stomach_troubles_ulcers,
                        data.fainting_seizure, data.rapid_weight_loss, data.radiation_therapy, data.joint_replacement_implant, data.heart_surgery, data.heart_attack,
                        data.thyroid_problem, data.heart_disease, data.heart_murmur, data.hepatitis_liver_disease, data.rheumatic_fever, data.hay_fever_allergies,
                        data.respiratory_problems, data.hepatitis_jaundice, data.tuberculosis, data.swollen_ankles, data.kidney_disease, data.diabetes,
                        data.chest_pain, data.stroke, data.cancer_tumors, data.anemia, data.angina, data.asthma, data.emphysema, data.bleeding_problems,
                        data.blood_diseases, data.head_injuries, data.arthritis_rheumatism, data.other
                    ];
    
                    let checkBoxesId = [
                        "isAllergicAnesthetic", "isAllergicPenicillin", "isAllergicSulfa",
                        "isAllergicAspirin", "isAllergicLatex", "isAllergicOthers", "highBloodPressure",
                        "lowBloodPressure", "epilepsy", "hivAids", "std", "stomachUlcers",
                        "faintingSeizures", "rapidWeightLoss", "radiationTherapy", "jointImplant", "heartSurgery", "heartAttack",
                        "thyroidProblem", "heartDisease", "heartMurmur", "liverDisease", "rheumaticFever", "allergies",
                        "respiratoryProblems", "hepatitisJaundice", "tuberculosis", "swollenAnkles", "kidneyDisease", "diabetes",
                        "chestPain", "stroke", "cancer", "anemia", "angina", "asthma", "emphysema", "bleedingProblems",
                        "bloodDiseases", "headInjuries", "arthritis", "other"
                    ];
    
                    for (let index = 0; index < checkBoxes.length; index++) {                    
                        if (checkBoxes[index] === 1) {
                            $(`#${checkBoxesId[index]}Yes`).prop("checked", true);
                            $(`#${checkBoxesId[index]}`).prop("checked", true);
                            $(`#${checkBoxesId[index]}Div`).toggle();
                            $(`#${checkBoxesId[index]}Div input`).prop('disabled', false);
                        } else {                
                            $(`#${checkBoxesId[index]}No`).prop("checked", true);
                        }
                    }
                }

                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

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
        
        // $('#medicalHistoryModal').modal('show');

        $("#medicalHistory").submit(function(e){
            showLoader();
            $("#medicalErrorMessage").empty();
            e.preventDefault();

            var url = $("#medicalHistory").attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: $("#medicalHistory").serialize(),
                dataType: "json"
            }).done(function (data) {
                if (!data.success) {
                    $("#medicalErrorMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    hideLoader();
                } else {
                    localStorage.setItem("medicalMessage", data.message);
                    location.reload();
                }
                // console.log(data.responseText);
            }).fail(function(data) {
                // console.log(data.responseText);
            });
        });

		$("#dentalHistory").submit(function(e){            
            showLoader();
            $("#dentalErrorMessage").empty();
            $("#dentalErrorMessage").empty();
			e.preventDefault();

			var url = $("#dentalHistory").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#dentalHistory").serialize(),
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    $("#dentalErrorMessage").append('<div class="alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    hideLoader();
                } else {
                    localStorage.setItem("dentalMessage", data.message);
                    location.reload();
                }
				//console.log(data);
			}).fail(function(data) {
				// console.log(data.responseText);
			});
		});

        $(".textOnly").on("input", function() {
            $(this).val($(this).val().replace(/[^a-zA-Z\s]/g, ""));
        });

        $('body').on('click', '#dentalHistoryClose', function(){
            $('#lastDentalVisit').val("")
        });
        
        if (localStorage.getItem("uploadmessage")) {
            let message = localStorage.getItem("uploadmessage");
            $("#uploadmessage").append('<div class="alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            localStorage.removeItem("uploadmessage");

        } else if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");
            $("#errorMessage").append(`<div class="alert alert-success  alert-dismissible fade show">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
            localStorage.removeItem("errorMessage");

        } else if (localStorage.getItem("dentalMessage")){
            let message = localStorage.getItem("dentalMessage");
            $("#dentalMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            localStorage.removeItem("dentalMessage");
            
        } else if (localStorage.getItem("medicalMessage")){
            let message = localStorage.getItem("medicalMessage");
            $("#medicalMessage").append('<div class="alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            localStorage.removeItem("medicalMessage");
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