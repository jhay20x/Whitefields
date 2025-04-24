<?php

$profilePath = "";

$stmt = $conn->prepare("SELECT * FROM `accounts` WHERE `username` = ?");
$stmt->bind_param("s", $_SESSION['user_username']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    $profilePath = $user['profile_path'];
}

$id = "";

if ($_SESSION['account_type'] == 2) {
  $id = fetchPatientID();
} else if ($_SESSION['account_type'] == 3) {
  $id = fetchDentistID();  
} else {
  $id = fetchSecretaryID();  
}

if (is_int($id)) {
    $hasId = true;
} else {
    $hasId = false;
}

?>

<!-- Modal -->
<div class="modal fade" id="logoutConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h6 class="modal-title" id="logoutConfirmLabel"><span><i class="bi bi-person"></i> Confirmation</span></h6>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="text-center">
                        <h6>Are you sure to logout?</h6>
                        <a href="../../auth/logout.php" id="logoutYesBtn" class="btn btn-sm btn-outline-success m-2 me-0">Yes</a>
                        <button type="button" value="" id="logoutNoBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<div id="sidebar">
  <div class="d-flex flex-column flex-shrink-0 p-3 overflow-y-auto" style="width: 350px; height: 100vh; background-color: rgba(166,143,98,255);">
    <a href="../../home.php" rel="nofollow" target="_blank" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <img src="../../resources/images/logo-icon-trans.webp" alt="Logo" width="50" height="50">
        <span class="fs-4">Whitefields Dental Clinic</span>
    </a>

    <div class="d-flex flex-column align-items-center">
        <img src="<?php echo $profilePath ? "../../files/{$profilePath}/profile.jpg" : '../../resources/images/blank-profile.webp';?>" alt="" width="150" height="150" class="me-2 rounded-circle mb-2 border border-5">
        <strong class="text-white">Welcome <?php echo $_SESSION['user_username']; ?>!</strong>
    </div>

    <hr>

    <?php 
    
    if (isset($_SESSION['account_type'])) {
      $account_type = $_SESSION['account_type'];

      if ($account_type === 1) { 
    
      ?>
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="dashboard.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-columns-gap me-1"></i>
                Dashboard
            </a>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#appointment-collapse" aria-expanded="false">
                <i class="bi bi-calendar3 me-1"></i>
                Appointments
            </button>
            <div class="collapse" id="appointment-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="appointment-request.php" class="d-inline-flex text-decoration-none rounded text-white">Appointment Request</a></li>
                <li><a href="appointment-list.php" class="d-inline-flex text-decoration-none rounded text-white">Appointment List</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1 ms-3">
            <a href="patient-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-person me-1"></i>
                Patients
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="dentist-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-person-vcard me-1"></i>
                Dentists
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="schedule.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-table me-1"></i>
                Schedule
            </a>
          </li>
          </li>
          <!-- <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#consent-collapse" aria-expanded="false">
                <i class="bi bi-file-earmark-text me-1"></i>
                Consent Forms
            </button>
            <div class="collapse" id="consent-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li> -->
          <li class="mb-1 ms-3">
            <a href="transactions.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-clock-history me-1"></i>
                Payment and Transactions
            </a>
          </li>
          <!-- <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#transaction-collapse" aria-expanded="false">
                <i class="bi bi-clock-history me-1"></i>
                Transactions
            </button>
            <div class="collapse" id="transaction-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li> -->
          <li class="mb-1 ms-3">
            <a href="reports.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                Reports
            </a>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#management-collapse" aria-expanded="false">
                <i class="bi bi-building-gear me-1"></i>
                Management
            </button>
            <div class="collapse" id="management-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="clinic-availability.php" class="d-inline-flex text-decoration-none rounded text-white">Clinic Availabilty</a></li>
                <li><a href="procedures-list.php" class="d-inline-flex text-decoration-none rounded text-white">Procedures</a></li>
              </ul>
            </div>
          </li>
        </ul>
      <?php 

      }
      
      if ($account_type === 2) {
      
      ?>
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="dashboard.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-columns-gap me-1"></i>
                Dashboard
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="appointment-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-calendar3 me-1"></i>
                My Appointments
            </a>
          </li>
        </ul>      
      <?php

      }
      
      if ($account_type === 3) {
      
      ?>
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="dashboard.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-columns-gap me-1"></i>
                Dashboard
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="appointment-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-calendar3 me-1"></i>
                My Appointments
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="patient-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <i class="bi bi-person me-1"></i>
                Patients
            </a>
          </li>
        </ul>      
      <?php

      }
    } 
      ?>    
    
    <hr>
  
      <div class="dropdown mb-5 mb-sm-0">  
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="profile.php" class="btn btn-dash d-inline-flex border rounded border-0 text-white position-relative">
                <i class="bi bi-person me-1"></i>
                <span class="position-absolute <?php echo ($hasId) ? 'visually-hidden' : ''; ?> top-50 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                My Profile
            </a>
          </li>
          
          <li class="mb-1 ms-3">
            <a class="btn btn-dash d-inline-flex border rounded border-0 text-white" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="bi bi-box-arrow-right me-1"></i>Log Out
            </a>
          </li>
        </ul>
      </div>
  </div>
</div>

<div class="overlay"></div>