<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$pid;

function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime(); // Current date
    $age = $today->diff($birthDate)->y; // Calculate age in years
    return $age;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'];
    $aptId = $_POST['aptId'];

    $_SESSION["pid"] = $pid;
    $_SESSION["aptId"] = $aptId;

    $stmt = $conn->prepare("SELECT CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
    CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name,
    pi.contactno, pi.bdate, pi.gender, 
    pi.religion, pi.nationality, pi.occupation, pi.address, ac.email_address, ac.username
    FROM `patient_info` pi
    LEFT OUTER JOIN accounts ac
    ON ac.id = pi.accounts_id
    WHERE pi.id = ?");
    $stmt->bind_param('i',$pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['Name'] = $row['Name'];
        $data['age'] = calculateAge($row['bdate']);
        $data['bdate'] = date("F d, Y", strtotime($row['bdate']));
        $data['gender'] = $row['gender'];
        $data['religion'] = $row['religion'];
        $data['nationality'] = $row['nationality'];
        $data['contactno'] = $row['contactno'];
        $data['address'] = $row['address'];
        $data['occupation'] = $row['occupation'];
        $data['email_address'] = $row['email_address'];
        $data['username'] = $row['username'];
    }

    $data = array_merge($data, fetchDental($conn, $pid, $data), fetchMedical($conn, $pid, $data));
}

function fetchDental($conn, $pid, $data) {
    $stmt = $conn->prepare("SELECT dh.prev_dentist AS prevDentist, dh.last_dental AS lastDental FROM dental_history dh WHERE dh.patient_id = ? ORDER BY dh.timestamp DESC");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $data = [];
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $data['prevDentist'] = $row['prevDentist'] ?? "None";
        $data['lastDental'] = date("F d, Y", strtotime($row['lastDental'])) ?? "None";
        $data['hasDental'] = true;

        return $data;
    } else {
        $data['prevDentist'] = "None";
        $data['lastDental'] = "None";
        $data['hasDental'] = false;

        return $data;
    }
}

function fetchMedical($conn, $pid, $data) {
    $stmt = $conn->prepare("SELECT mh.physician_name, mh.speciality, mh.office_address, mh.office_number,
        mq.*, il.*
        FROM medical_history mh
        LEFT OUTER JOIN medical_questions mq
        ON mq.id = mh.medical_questions_id
        LEFT OUTER JOIN illness_list il
        ON il.id = mh.illness_list_id
        WHERE mh.patient_id = ?
        ORDER BY mh.timestamp DESC;");
    $stmt->bind_param('i', $pid);
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
            // $data[$value] = $row[$value] ?? null;

            if ($row[$value] === 1) {
                $data[$value] = "Yes";
            } else if ($row[$value] === 0) {
                $data[$value] = "No";
            } else {
                $data[$value] = $row[$value] ?? null;;
            }
        }

        return $data;
    }else {
        $data = [];
        
        foreach ($fields as $value) {
            $data[$value] = "No Record";
        }

        return $data;
    }
}

echo json_encode($data);