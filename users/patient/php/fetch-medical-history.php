<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchPatientID();

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
    }
}

echo json_encode($data);