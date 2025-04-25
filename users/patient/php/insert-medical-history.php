<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = [];
$message;
$error;
$timestamp = date('Y-m-d H:i:s', time());
$insertId;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $patient_id = fetchPatientID();

    $medicalHistoryFields = ["physician_name", "speciality", "office_address", "office_number"];
    $medicalQuestionFields = ["is_good_health", "is_under_treatment", "is_under_treatment_condition", "had_operation", "had_operation_illness", "had_hospitalized", "had_hospitalized_when", "had_hospitalized_why", "is_taking_prescription", "is_taking_prescription_medication", "uses_tobacco", "uses_alcohol_drugs", "bleeding_time", "is_pregnant", "is_nursing", "is_birth_control", "blood_type", "blood_pressure"]; 
    $medicalQuestionAllergy = ["is_allergic_anesthetic", "is_allergic_penicillin", "is_allergic_sulfa", "is_allergic_aspirin", "is_allergic_latex", "is_allergic_others", "is_allergic_others_other"];
    $illnessListFields = ["high_blood_pressure", "low_blood_pressure", "epilepsy_convulsions", "aids_hiv_infection", "sexually_transmitted_disease", "stomach_troubles_ulcers", "fainting_seizure", "rapid_weight_loss", "radiation_therapy", "joint_replacement_implant", "heart_surgery", "heart_attack", "thyroid_problem", "heart_disease", "heart_murmur", "hepatitis_liver_disease", "rheumatic_fever", "hay_fever_allergies", "respiratory_problems", "hepatitis_jaundice", "tuberculosis", "swollen_ankles", "kidney_disease", "diabetes", "chest_pain", "stroke", "cancer_tumors", "anemia", "angina", "asthma", "emphysema", "bleeding_problems", "blood_diseases", "head_injuries", "arthritis_rheumatism", "other", "other_illness"];
    
    $illnessInsert = [];
    $illnessInsertValues = [];

    $medicalInsert = [];
    $medicalInsertValues = [];

    $medicalHistoryInsert = [];
    $medicalHistoryInsertValues = [];

    $items = [];

    foreach (array_merge($medicalHistoryFields, $medicalQuestionFields, $medicalQuestionAllergy, $illnessListFields) as $name) {
        $value = $_POST[$name] ?? null;
        if (in_array($name, $medicalHistoryFields)) {
            $medicalHistoryInsert[] = $name;
            $medicalHistoryInsertValues[] = ($value === "" || empty($value)) ? null : $value;
            $items[] = [
                "item" => $name,
                "desc" => $name,
                "value" => ($value === "" || empty($value)) ? "null" : $value
            ];
        } 
        elseif (in_array($name, $medicalQuestionFields)) {
            $medicalInsert[] = $name;
            $medicalInsertValues[] = match ($value) {
                "Yes" => 1,
                "on" => 0,
                null => null,
                "" => null,
                default => $value
            };
            $items[] = [
                "item" => $name,
                "desc" => $name,
                "value" => match ($value) {
                    "Yes" => "Yes",
                    "on" => "No",
                    null => "null",
                    "" => "null",
                    default =>  $value
                }
            ];
        } 
        elseif (in_array($name, $medicalQuestionAllergy)) {
            $medicalInsert[] = $name;
            $medicalInsertValues[] = ($value === "on") ? 1 : $value;

            if ($name == "is_allergic_others") {
                $items[] = [
                    "item" => $name,
                    "desc" => $name,
                    "value" => ($value === "on") ? "Yes, " . $_POST["is_allergic_others_other"] ?? "null"  : $value ?? "null"
                ];
            } else if ($name != "is_allergic_others" && $name != "is_allergic_others_other") {
                $items[] = [
                    "item" => $name,
                    "desc" => $name,
                    "value" => ($value === "on") ? "Yes" : $value ?? "null"
                ];
            }
        } 
        elseif (in_array($name, $illnessListFields)) {
            $illnessInsert[] = $name;
            $illnessInsertValues[] = ($value === "on") ? 1 : $value;
            $items[] = [
                "item" => $name,
                "desc" => $name,
                "value" => ($value === "on") ? "Yes" : $value ?? "null"
            ];
        }
    }

    $medicalIllnessId = checkMedicalIllness($patient_id);
    $medicalQuestionsId = checkMedicalQuestions($patient_id);
    $medicalHistoryId = checkMedicalHistory($patient_id);
    
    if ($medicalHistoryId && $medicalQuestionsId && $medicalIllnessId) {
        $logType = "Update";
        updateMedicalIllness($patient_id, $medicalIllnessId, $illnessInsert, $illnessInsertValues, $timestamp);
        updateMedicalQuestions($patient_id, $medicalQuestionsId, $medicalInsert, $medicalInsertValues, $timestamp);
        updateMedicalHistory($patient_id, $medicalHistoryId, $medicalHistoryInsert, $medicalHistoryInsertValues, $timestamp);
        insertMedicalLogs($conn, $patient_id, $medicalHistoryId, $logType, $items, $timestamp);
    } else {
        $logType = "Added";
        insertIllnessList($patient_id, $illnessInsert, $illnessInsertValues, $timestamp);
        insertMedicalQuestions($patient_id, $medicalInsert, $medicalInsertValues, $timestamp);
        insertMedicalHistory($patient_id, $medicalHistoryInsert, $medicalHistoryInsertValues, $timestamp);
        insertMedicalLogs($conn,$patient_id, $insertId, $logType, $items, $timestamp);
    }
}

function insertMedicalLogs($conn, $patient_id, $medicalHistoryId, $logType, $items, $timestamp) {
    $medicalHistoryFields = [
        "physician_name" => "Physician Name", "speciality" => "Speciality",
        "office_address" => "Office Address", "office_number" => "Office Number"
    ];
    
    $medicalQuestionFields = [
        "is_good_health" => "Is Good Health", "is_under_treatment" => "Is Under Treatment",
        "is_under_treatment_condition" => "Is Under Treatment Condition", "had_operation" => "Had Operation",
        "had_operation_illness" => "Had Operation Illness", "had_hospitalized" => "Had Hospitalized",
        "had_hospitalized_when" => "Had Hospitalized When", "had_hospitalized_why" => "Had Hospitalized Why",
        "is_taking_prescription" => "Is Taking Prescription", "is_taking_prescription_medication" => "Is Taking Prescription Medication",
        "uses_tobacco" => "Uses Tobacco", "uses_alcohol_drugs" => "Uses Alcohol Drugs",
        "bleeding_time" => "Bleeding Time", "is_pregnant" => "Is Pregnant", "is_nursing" => "Is Nursing",
        "is_birth_control" => "Is Birth Control", "blood_type" => "Blood Type", "blood_pressure" => "Blood Pressure"
    ];
    
    $medicalQuestionAllergy = [
        "is_allergic_anesthetic" => "Is Allergic Anesthetic", "is_allergic_penicillin" => "Is Allergic Penicillin",
        "is_allergic_sulfa" => "Is Allergic Sulfa", "is_allergic_aspirin" => "Is Allergic Aspirin",
        "is_allergic_latex" => "Is Allergic Latex", "is_allergic_others" => "Is Allergic Others"
    ];    
    
    $illnessListFields = [
        "high_blood_pressure" => "High Blood Pressure", "low_blood_pressure" => "Low Blood Pressure",
        "epilepsy_convulsions" => "Epilepsy Convulsions", "aids_hiv_infection" => "AIDS HIV Infection",
        "sexually_transmitted_disease" => "Sexually Transmitted Disease", "stomach_troubles_ulcers" => "Stomach Troubles Ulcers",
        "fainting_seizure" => "Fainting Seizure", "rapid_weight_loss" => "Rapid Weight Loss",
        "radiation_therapy" => "Radiation Therapy", "joint_replacement_implant" => "Joint Replacement Implant",
        "heart_surgery" => "Heart Surgery", "heart_attack" => "Heart Attack",
        "thyroid_problem" => "Thyroid Problem", "heart_disease" => "Heart Disease",
        "heart_murmur" => "Heart Murmur", "hepatitis_liver_disease" => "Hepatitis Liver Disease",
        "rheumatic_fever" => "Rheumatic Fever", "hay_fever_allergies" => "Hay Fever Allergies",
        "respiratory_problems" => "Respiratory Problems", "hepatitis_jaundice" => "Hepatitis Jaundice",
        "tuberculosis" => "Tuberculosis", "swollen_ankles" => "Swollen Ankles",
        "kidney_disease" => "Kidney Disease", "diabetes" => "Diabetes",
        "chest_pain" => "Chest Pain", "stroke" => "Stroke",
        "cancer_tumors" => "Cancer Tumors", "anemia" => "Anemia",
        "angina" => "Angina", "asthma" => "Asthma",
        "emphysema" => "Emphysema", "bleeding_problems" => "Bleeding Problems",
        "blood_diseases" => "Blood Diseases", "head_injuries" => "Head Injuries",
        "arthritis_rheumatism" => "Arthritis Rheumatism", "other" => "Other", "other_illness" => "Other Illness"
    ];        

    $fields = array_merge($medicalHistoryFields, $medicalQuestionFields, $medicalQuestionAllergy, $illnessListFields);

    foreach ($items as $index => $item) {
        if (isset($fields[$item["item"]]) && in_array($fields[$item["item"]], $fields)) {
            $items[$index]["desc"] = $fields[$item["item"]];
        }
    }

    $newJsonData = [
        "type" => $logType,
        "items" => $items
    ];

    $data = json_encode($newJsonData);
    
    $stmt = $conn->prepare("INSERT INTO `medical_history_logs`(`patient_id`, `medical_history_id`, `remarks`, `timestamp`) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $patient_id, $medicalHistoryId, $data, $timestamp);
    $stmt->execute();
	$stmt->close();
    
    // echo $data;

    // echo "Record Type: $logType \r\n";

    // foreach ($newJsonData["items"] as $index => $value) {
    //     $logType = $value["desc"];
    //     $record = $value["value"];

    //     echo "$logType = $record \r\n";
    // }
}

function updateMedicalHistory($patient_id, $medicalHistoryId, $medicalHistoryInsert, $medicalHistoryInsertValues, $timestamp) {
    global $conn, $message, $error;

    array_unshift($medicalHistoryInsert, "timestamp");
    array_unshift($medicalHistoryInsertValues, $timestamp);
    
    array_push($medicalHistoryInsertValues, $patient_id, $medicalHistoryId);

    $updateMedical = [];

    for ($i=0; $i < count($medicalHistoryInsert); $i++) {
        $updateMedical[] = $medicalHistoryInsert[$i] . " = ?";
    }
    
    $columnString = implode(", ", $updateMedical);
    $types = str_repeat("s", count($medicalHistoryInsert)) . "ii";

    $stmt = $conn->prepare("UPDATE `medical_history` SET $columnString WHERE patient_id = ? AND id = ?");
    $stmt->bind_param($types, ...$medicalHistoryInsertValues);

    if ($stmt->execute()) {
        $message = "Your medical history has been successfully updated.";
    } else {
        $error = "Your medical history has failed to be updated. Please try again.";
    }
}

function updateMedicalQuestions($patient_id, $medicalQuestionsId, $medicalInsert, $medicalInsertValues, $timestamp) {
    global $conn, $message, $error;

    array_unshift($medicalInsert, "timestamp");
    array_unshift($medicalInsertValues, $timestamp);

    array_push($medicalInsertValues, $patient_id, $medicalQuestionsId);

    $updateMedical = [];

    for ($i=0; $i < count($medicalInsert); $i++) {
        $updateMedical[] = $medicalInsert[$i] . " = ?";
    }

    $columnString = implode(", ", $updateMedical);
    $types = str_repeat("s", count($medicalInsert)) . "ii";
    
    $stmt = $conn->prepare("UPDATE `medical_questions` SET $columnString WHERE patient_id = ? AND id = ?");
    $stmt->bind_param($types, ...$medicalInsertValues);

    if ($stmt->execute()) {
        $message = "Your medical history has been successfully saved.";
    } else {
        $error = $stmt->error;
    }
}

function updateMedicalIllness($patient_id, $medicalIllnessId, $illnessInsert, $illnessInsertValues, $timestamp) {
    global $conn, $message, $error;

    array_unshift($illnessInsert, "timestamp");
    array_unshift($illnessInsertValues, $timestamp);

    array_push($illnessInsertValues, $patient_id, $medicalIllnessId);

    $updateIllness = [];

    for ($i=0; $i < count($illnessInsert); $i++) {
        $updateIllness[] = $illnessInsert[$i] . " = ?";
    }

    $columnString = implode(", ", $updateIllness);
    $types = str_repeat("s", count($illnessInsert)) . "ii";
    
    $stmt = $conn->prepare("UPDATE `illness_list` SET $columnString WHERE patient_id = ? AND id = ?");
    $stmt->bind_param($types, ...$illnessInsertValues);

    if ($stmt->execute()) {
        $message = "Your medical history has been successfully saved.";
    } else {
        $error = $stmt->error;
    }
}

function insertMedicalHistory($patient_id, $medicalHistoryInsert, $medicalHistoryInsertValues, $timestamp) {
    global $conn, $message, $error, $insertId;

    array_unshift($medicalHistoryInsert, "patient_id", "timestamp");
    array_unshift($medicalHistoryInsertValues, $patient_id, $timestamp);

    array_push($medicalHistoryInsert, "medical_questions_id", "illness_list_id");
    array_push($medicalHistoryInsertValues, $insertId, $insertId);

    $placeholders = array_fill(0, count($medicalHistoryInsert), "?");
    $placeholderString = implode(", ", $placeholders);
    $columnString = implode(", ", $medicalHistoryInsert);
    $types = "i" . str_repeat("s", count($medicalHistoryInsert) - 3) . "ii";

    $stmt = $conn->prepare("INSERT INTO `medical_history`($columnString) VALUES ($placeholderString)");
    $stmt->bind_param($types, ...$medicalHistoryInsertValues);

    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
        $message = "Your medical history has been successfully saved.";
    } else {
        $error = "Your medical history has failed to be saved. Please try again.";
    }
}

function checkMedicalHistory($patient_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `medical_history` WHERE patient_id = ?");
    $stmt->bind_param('i',$patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['id'];
    } else {
        return false;
    }
}

function checkMedicalQuestions($patient_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `medical_questions` WHERE patient_id = ?");
    $stmt->bind_param('i',$patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['id'];
    } else {
        return false;
    }
}

function checkMedicalIllness($patient_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `illness_list` WHERE patient_id = ?");
    $stmt->bind_param('i',$patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['id'];
    } else {
        return false;
    }
}

function insertMedicalQuestions($patient_id, $medicalInsert, $medicalInsertValues, $timestamp) {
    global $conn, $message, $error, $insertId;

    array_unshift($medicalInsert, "patient_id", "timestamp");
    array_unshift($medicalInsertValues, $patient_id, $timestamp);

    $placeholders = array_fill(0, count($medicalInsert), "?");
    $placeholderString = implode(", ", $placeholders);
    $columnString = implode(", ", $medicalInsert);
    $types = "i" . str_repeat("s", count($medicalInsert) - 1);
    
    $stmt = $conn->prepare("INSERT INTO `medical_questions`($columnString) VALUES ($placeholderString)");
    $stmt->bind_param($types, ...$medicalInsertValues);

    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
        $message = "Your medical history has been successfully saved.";
    } else {
        $error = $stmt->error;
    }
}

function insertIllnessList($patient_id, $illnessInsert, $illnessInsertValues, $timestamp) {
    global $conn, $message, $error;

    array_unshift($illnessInsert, "patient_id", "timestamp");
    array_unshift($illnessInsertValues, $patient_id, $timestamp);

    $placeholders = array_fill(0, count($illnessInsert), "?");
    $placeholderString = implode(", ", $placeholders);
    $columnString = implode(", ", $illnessInsert);
    $types = "i" . str_repeat("s", count($illnessInsert) - 1);
    
    $stmt = $conn->prepare("INSERT INTO `illness_list`($columnString) VALUES ($placeholderString)");
    $stmt->bind_param($types, ...$illnessInsertValues);

    if ($stmt->execute()) {
        $message = "Your medical history has been successfully saved.";
    } else {
        $error = $stmt->error;
    }  
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);