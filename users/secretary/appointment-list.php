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
        <link rel="stylesheet" href="../../resources/css/jquery-ui.css">
        <link rel="stylesheet" href="../../resources/css/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../resources/css/dataTables.bootstrap5.css">
        <link rel="stylesheet" href="../../resources/css/buttons.bootstrap5.css">
        <link rel="stylesheet" href="../../resources/css/searchPanes.dataTables.css" />
        <link rel="stylesheet" href="../../resources/css/select.dataTables.css" />
        <link rel="stylesheet" href="../../resources/css/buttons.dataTables.css" />
        <link rel="stylesheet" href="../../resources/css/searchBuilder.dataTables.css" />
        <link rel="stylesheet" href="../../resources/css/dataTables.dateTime.css" />

        <style>
            .bi {
                vertical-align: -.125em;
                fill: currentColor;
            }

            body {
                background-color: lightgrey;
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

    <body>
        <?php include "../../components/sidebar.php" ?>

        <!-- Modal -->
        <div class="modal fade" id="patientUpdateStatusModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="patientUpdateStatusLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>                        
                        </h6>
                        <h6 class="ms-2">Appointment Details | Status: <strong id="" class="aptdtlsstatus"></strong></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="patientUpdateStatusClose" aria-label="Close"></button>
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

                            <div class="col-12 col-lg">
                                <h6>Set Status</span></h6>
                            </div>

                            <div class="col-md-5">
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="patientUpdateStatus">Status</label>
                                    <select class="form-select" name="patientUpdateStatus" id="patientUpdateStatus">
                                        <option disabled selected value="">Select...</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Denied</option>
                                    </select>
                                </div>
                            </div>

                            <div id="reasonDiv" class="col-md-5">
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="reason">Reason</label>
                                    <select class="form-select" name="reason" id="reason">
                                        <option disabled selected value="">Select...</option>
                                        <?php
                                            $stmt = $conn->prepare("SELECT * FROM `rejected_reasons`;");
                                            $stmt->execute();
                                            $result = $stmt->get_result();

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
                                </div>
                            </div>

                            <div id="reasonOtherDiv" class="col-md-5">
                                <div class="input-group my-3">
                                    <label class="input-group-text" for="reasonOther">Other</label>
                                    <input autocomplete="off" type="text" name="reasonOther" placeholder="Reason"  id="reasonOther" class="form-control">
                                </div>
                            </div>
                             
                            <div class="d-flex justify-content-end">
                                <button type="button" value="" id="aptTreatPatientBackBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#appointListModal">Save</button>
                                <button type="button" value="" id="aptTreatPatientContinueBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelSetStatusModal">Cancel</button>
                            </div>
                        </div>
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
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>                            
                        </h6>
                        <h6 class="ms-2">Appointment Details | Status: <strong id="" class="aptdtlsstatus"></strong></h6>
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
                                    <h6>Patient Name: 
                                        <span id="" class="aptdtlsname fw-normal"></span>
                                        <button class="btn btn-sm p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                            <svg class="" width="16" height="16" style="vertical-align: -.125em"><use xlink:href="#eye"/></svg>
                                            <span style="font-size: 0.8rem" class="d-none d-sm-inline">View Profile</span>
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

                            <div class="d-flex align-items-start row">
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
                            <div class="d-flex justify-content-end">
                                <button id="aptdtlsUpdateStatus" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#patientUpdateStatusModal">Update Status</button>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientViewModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="patientViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person"/></svg>                        
                        </h6>
                        <h6 class="ms-2">Patient Information</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="patientViewClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">                        
                            <div class="row">
                                <div class="col-12 col-sm">
                                    <h6>Name: <span id="patientName" class="fw-normal"></span></h6>
                                    <h6>Age: <span id="patientAge" class="fw-normal"></h6>
                                    <h6>Birth Date: <span id="patientBdate" class="fw-normal"></span></h6>
                                    <h6>Gender: <span id="patientGender" class="fw-normal"></span></h6>
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
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="cancelSetStatusModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="cancelSetStatusLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelSetStatusLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                        </h6>
                        <h6 class="ms-2">Appointment Cancellation Form</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelSetStatusClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal"data-bs-target="#appointListModal">Yes</button>
                                <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#patientUpdateStatusModal">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row d-flex justify-content-center">

                <!-- <div id="content">
                <div class="d-flex justify-content-center align-items-center border" style="min-height: 100vh;">
                    <div class="p-5 rounded shadow">
                        <p class="display-1 fw-bold">Welcome <?php echo $_SESSION['user_username']; ?></p>
                        <a href="../../auth/logout.php">
                            <button class="btn btn-primary col-12">Logout</button>
                        </a>
                    </div>
                </div>
            </div> -->

                <!-- <div class="title d-flex align-items-center p-3">
                <button id="" class="sidebarCollapse btn btn-outline-secondary me-4"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg></button>
                <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#columns-gap"/></svg>
                <h1>Appointment: Appointment Requests</h1>
            </div> -->

                <div class="title d-flex flex-row align-items-center p-3">
                    <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg>
                    </button>
                    <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#calendar3"/></svg>
                    <h1 class="col">Appointments</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 bg-white row">
                    <div class="my-3">
                        <!-- <div class="box">
                            <p id="txtHint">Select row to Update/Delete.</p>
                        </div> -->
                        
                        <div class="col">
                            <h3>Appointment Lists</h3>
                            <span>View and manage all of the appointments received by the clinic.</span>
                        </div>

                        <table id="myTable" class="table-group-divider table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="col">Request Timestamp</th>
                                    <!-- <th class="col">Request Time</th> -->
                                    <th class="col-3">Patient Name</th>
                                    <th class="col">Appointment On</th>
                                    <th class="col">Status</th>
                                    <!-- <th class="col">Appointment Time</th> -->
                                    <th class="col">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
                                    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name, ar.id AS ID, ar.patient_id AS PID
                                    FROM appointment_requests ar
                                    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                    LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                    WHERE ar.appoint_status_id != 4;");
                                $stmt->execute();
                                $result = $stmt->get_result();

                                $status;

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $requesttime = date('Y-m-d h:i A', strtotime($row['RequestDateTime']));
                                        $approvedtime = date('Y-m-d h:i A', strtotime($row['ApprovedDateTime']));

                                        if ($row['Status'] == "Approved") {
                                            $status = "text-success";
                                        } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
                                            $status = "text-danger";
                                        }
                                        echo '
                                        <tr>
                                            <td id="appointRequestDate">' . $requesttime . '</td>
                                            <td id="appointName">' . $row['Name'] . '</td>
                                            <td id="appointApprovedDate">' . $approvedtime . '</td>
                                            <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                                            <td class="appointID">
                                            <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
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

    <script>
        $(document).ready(function() {
            let patient_id;

            $('#myTable thead th').eq(3).attr('width', '0%');

            let table = new DataTable('#myTable', {
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
                        targets: [0,1,2,3,4],
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

            $('body').on('click', '.viewAptDetail', function(){
                let id = $(this).attr('value');
                patient_id = $(this).attr("data-p-id");
                fetchPatientDetails(patient_id);
                loadDetails(id);
            });

            $(window).on('load', function() {
                let url_str = document.URL;

                let url = new URL(url_str);
                let search_params = url.searchParams;

                let id = search_params.get('id');

                if (id) {
                    loadDetails(id);
                }
            });
            
            $(window).on('hide.bs.modal', function (e) {
                window.history.replaceState({}, document.title, window.location.pathname);
            })

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
                    console.log(data);
                    if (data.Status == "Approved") {
                        $(".aptdtlsVerdictDiv").show();
                        $(".aptdtlsReasonDiv").hide();
                        $("#aptdtlsUpdateStatus").show();
                        $("#aptCancelBtn").show();
                        $(".aptdtlsstatus").removeClass("text-danger text-warning");
                        $(".aptdtlsstatus").addClass("text-success");
                    } else if (data.Status == "Denied" || data.Status == "Cancelled") {
                        $(".aptdtlsVerdictDiv").show();
                        $(".aptdtlsReasonDiv").show();
                        $("#aptdtlsUpdateStatus").hide();
                        $("#aptCancelBtn").hide();
                        if (data.ReasonOther || data.CancelReasonOther) {
                            $(".aptdtlsVerdictOther").show();
                        } else {
                            $(".aptdtlsVerdictOther").hide();
                        }
                        $(".aptdtlsstatus").removeClass("text-success text-warning");
                        $(".aptdtlsstatus").addClass("text-danger");
                    } else {                        
                        $("#aptdtlsUpdateStatus").hide();
                        $(".aptdtlsVerdictDiv").hide();
                        $("#aptCancelBtn").show();
                        $(".aptdtlsReasonDiv").hide();
                        $(".aptdtlsstatus").removeClass("text-success text-danger");
                        $(".aptdtlsstatus").addClass("text-warning");
                    }

                    $(".aptdtlsname").text(data.Name);
                    $(".aptdtlsstatus").text(data.Status);
                    $(".aptdtlsVerdict").text(data.Status);
                    $("#aptdtlsDentist").text(data.Dentist);
                    $(".aptdtlsStartDate").text(data.Start_Date);
                    $(".aptdtlsStartTime").text(data.Start_Time);
                    $("#aptdtlsRequestDate").text(data.Request_Date);
                    $("#aptdtlsRequestTime").text(data.Request_Time);
                    $("#aptdtlsVerdictDate").text(data.Approved_Date);
                    $("#aptdtlsVerdictTime").text(data.Approved_Time);
                    $("#aptdtlsVerdictBy").text(data.Approved_By);
                    $("#aptdtlsConcern").text(data.Concern);

                    switch (data.Status) {
                        case "Cancelled":
                            $("#aptdtlsReason").text(data.CancelReason);
                            $("#aptdtlsReasonOther").text(data.CancelReasonOther);
                            $("#aptdtlsVerdictDate").text(data.Cancel_Date);
                            $("#aptdtlsVerdictTime").text(data.Cancel_Time);
                            break;                            
                        case "Denied":
                            $("#aptdtlsReason").text(data.Reason);
                            $("#aptdtlsReasonOther").text(data.ReasonOther);
                            break;
                        default:                            
                            $("#aptdtlsReason").text("");
                            $("#aptdtlsReasonOther").text("");     
                            break;
                    }
                    
                    if (data.length != 0) {
                        $('#appointListModal').modal('show');
                    }

                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
            }

            $('body').on('click', '#aptdtlsUpdateStatus', function(){
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

            function fetchPatientDetails(id) {            
                var formData = {
                    id: id
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-patient-info.php",
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    $("#patientName").text(data.Name);
                    $("#patientUsername").text(data.username);
                    $("#patientAge").text(data.age);
                    $("#patientBdate").text(data.bdate);
                    $("#patientGender").text(data.gender);
                    $("#patientContact").text(data.contactno);
                    $("#patientEmail").text(data.email_address);
                    $("#patientReligion").text(data.religion);
                    $("#patientNationality").text(data.nationality);
                    $("#patientAddress").text(data.address);
                    $("#patientOccupation").text(data.occupation);

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