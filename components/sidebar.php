<?php

$profilePath = "";

$stmt = $conn->prepare("SELECT * FROM `accounts` WHERE `username` = ?");
$stmt->bind_param("s", $_SESSION['user_username']);
$stmt->execute();
$result = $stmt->get_result();

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

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="file-earmark-text" viewBox="0 0 16 16">
        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
    </symbol>
    <symbol id="clock-history" viewBox="0 0 16 16">
        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
    </symbol>
    <symbol id="columns-gap" viewBox="0 0 16 16">
        <path d="M6 1v3H1V1zM1 0a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm14 12v3h-5v-3zm-5-1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zM6 8v7H1V8zM1 7a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zm14-6v7h-5V1zm-5-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/>
    </symbol>
    <symbol id="table" viewBox="0 0 16 16">
        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/>
    </symbol>
    <symbol id="box-arrow-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
    </symbol>
    <symbol id="grid" viewBox="0 0 16 16">
        <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
    </symbol>
    <symbol id="calendar3" viewBox="0 0 16 16">
        <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
        <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
    </symbol>
    <symbol id="person" viewBox="0 0 16 16">
        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
    </symbol>
    <symbol id="person-vcard" viewBox="0 0 16 16">
        <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
        <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
    </symbol>
    <symbol id="list" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
    </symbol>
    <symbol id="pencil-square" viewBox="0 0 16 16">
      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
    </symbol>
    <symbol id="building-gear" viewBox="0 0 16 16">
      <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
      <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
    </symbol>
    <symbol id="plus" viewBox="0 0 16 16">
      <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
    </symbol>
    <symbol id="eye" viewBox="0 0 16 16">
      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
    </symbol>
    <symbol id="bell" viewBox="0 0 16 16">
      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
    </symbol>
    <symbol id="clipboard-data" viewBox="0 0 16 16">
      <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0z"/>
      <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
      <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
    </symbol>
    <symbol id="file-medical" viewBox="0 0 16 16">
      <path d="M8.5 4.5a.5.5 0 0 0-1 0v.634l-.549-.317a.5.5 0 1 0-.5.866L7 6l-.549.317a.5.5 0 1 0 .5.866l.549-.317V7.5a.5.5 0 1 0 1 0v-.634l.549.317a.5.5 0 1 0 .5-.866L9 6l.549-.317a.5.5 0 1 0-.5-.866l-.549.317zM5.5 9a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/>
      <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1"/>
    </symbol>
    <symbol id="exclamation-circle" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
      <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
    </symbol>
</svg>
    
<div id="sidebar">
  <div class="d-flex flex-column flex-shrink-0 p-3" style="width: 350px; height: 100vh; background-color: rgba(166,143,98,255);">
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
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#columns-gap"/></svg>
                Dashboard
            </a>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#appointment-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#calendar3"/></svg>
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
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person"/></svg>
                Patients
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="dentist-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person-vcard"/></svg>
                Dentists
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="schedule.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                Schedule
            </a>
          </li>
          <!-- <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#schedule-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                Schedules
            </button>
            <div class="collapse" id="schedule-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Add Schedule</a></li>
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Schedule List</a></li>
              </ul>
            </div>
          </li> -->
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#consent-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#file-earmark-text"/></svg>
                Consent Forms
            </button>
            <div class="collapse" id="consent-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#transaction-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#clock-history"/></svg>
                Transactions
            </button>
            <div class="collapse" id="transaction-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#management-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#building-gear"/></svg>
                Management
            </button>
            <div class="collapse" id="management-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="clinic-availability.php" class="d-inline-flex text-decoration-none rounded text-white">Clinic Availabilty</a></li>
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
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#columns-gap"/></svg>
                Dashboard
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="appointment-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#calendar3"/></svg>
                My Appointments
            </a>
          </li>
          <!-- <li class="mb-1 ms-3">
            <a href="appointment-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#clipboard-data"/></svg>
                Treatment Record
            </a>
          </li> -->
          <!-- <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#appointment-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#calendar3"/></svg>
                Appointments
            </button>
            <div class="collapse" id="appointment-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="appointment-request.php" class="d-inline-flex text-decoration-none rounded text-white">Appointment Request</a></li>
                <li><a href="appointment-list.php" class="d-inline-flex text-decoration-none rounded text-white">Appointment List</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#patient-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person"/></svg>
                Patients
            </button>
            <div class="collapse" id="patient-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="patient-list.php" class="d-inline-flex text-decoration-none rounded text-white">Patient List</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#dentist-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person-vcard"/></svg>
                Dentists
            </button>
            <div class="collapse" id="dentist-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Add Doctor</a></li>
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Dentist List</a></li>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#schedule-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                Schedules
            </button>
            <div class="collapse" id="schedule-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Add Schedule</a></li>
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Schedule List</a></li>
              </ul>
            </div>
          </li>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#consent-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#file-earmark-text"/></svg>
                Consent Forms
            </button>
            <div class="collapse" id="consent-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#transaction-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#clock-history"/></svg>
                Transactions & History
            </button>
            <div class="collapse" id="transaction-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li> -->
        </ul>      
      <?php

      }
      
      if ($account_type === 3) {
      
      ?>
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="dashboard.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#columns-gap"/></svg>
                Dashboard
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="appointment-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#calendar3"/></svg>
                My Appointments
            </a>
          </li>
          <li class="mb-1 ms-3">
            <a href="patient-list.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person"/></svg>
                Patients
            </a>
          </li>
          <!-- <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#patient-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person"/></svg>
                Patients
            </button>
            <div class="collapse" id="patient-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="patient-list.php" class="d-inline-flex text-decoration-none rounded text-white">Patient List</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#dentist-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person-vcard"/></svg>
                Dentists
            </button>
            <div class="collapse" id="dentist-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Add Doctor</a></li>
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Dentist List</a></li>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#schedule-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                Schedules
            </button>
            <div class="collapse" id="schedule-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Add Schedule</a></li>
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Schedule List</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#consent-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#file-earmark-text"/></svg>
                Consent Forms
            </button>
            <div class="collapse" id="consent-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li>
          <li class="mb-1">
            <button class="btn btn-toggle d-inline-flex align-items-center rounded text-white border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#transaction-collapse" aria-expanded="false">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#clock-history"/></svg>
                Transactions & History
            </button>
            <div class="collapse" id="transaction-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="#" class="d-inline-flex text-decoration-none rounded text-white">Placeholder</a></li>
              </ul>
            </div>
          </li> -->
        </ul>      
      <?php

      }
    } 
      ?>    
    
    <hr>
  
      <!-- <div class="dropdown mb-5 mb-sm-0">   -->
      <div class="dropdown">  
        <ul class="list-unstyled ps-0 mb-auto">
          <li class="mb-1 ms-3">
            <a href="profile.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white position-relative">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#person"/></svg>
                <span class="position-absolute <?php echo ($hasId) ? 'visually-hidden' : ''; ?> top-50 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                My Profile
            </a>
          </li>
          
          <!-- <hr> -->

          <li class="mb-1 ms-3">
            <a href="../../auth/logout.php" class="btn btn-dash d-inline-flex align-items-center rounded border-0 text-white">
                <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#box-arrow-right"/></svg>
                Log Out
            </a>
          </li>
        </ul>
      </div>
  </div>
</div>

<div class="overlay"></div>