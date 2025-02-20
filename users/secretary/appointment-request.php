<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

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
<body>
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
                        <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>                        
                    </h6>
                    <h6 class="ms-2">Appointment Details | Status: <strong id="aptdtlsstatus" class=""></strong></h6>
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
                                    <button class="btn btn-sm p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                        <svg class="" width="16" height="16" style="vertical-align: -.125em"><use xlink:href="#eye"/></svg> View Profile
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

                        <div class="col-md-5">
                            <div class="input-group my-3">
                                <label class="input-group-text" for="setStatus">Status</label>
                                <select class="form-select" name="setStatus" id="setStatus">
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
                            <button type="button" value="" id="aptApplyBtn" class="btn btn-sm btn-primary m-2 me-0">Apply</button>
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
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#appointRequestModal">Back</button>
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
                        <h4 id="txtHint">Filters:</h4>
                    </div> -->
                        
                    <div class="col">
                        <h3>Appointment Requests</h3>
                        <span>View and manage all incoming appointment requests.</span>
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

                        <tbody id="tableBody">
                            <?php
                            $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
                            CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name, ar.id AS ID, ar.patient_id AS PID
                            FROM appointment_requests ar
                            LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                            LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                            WHERE ar.appoint_status_id = 4;");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $requesttime = date('Y-m-d h:i A', strtotime($row['RequestDateTime']));
                                    $approvedtime = date('Y-m-d h:i A', strtotime($row['ApprovedDateTime']));
                                    echo '
                                        <tr>
                                            <td id="appointRequestDate">' . $requesttime . '</td>
                                            <td id="appointName">' . $row['Name'] . '</td>
                                            <td id="appointApprovedDate">' . $approvedtime . '</td>
                                            <td id="appointStatus" class="text-warning fw-bold">' . $row['Status'] . '</td>
                                            <td class="appointID">
                                            <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointRequestModal">View
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

        $('#myTable thead th').eq(3).attr('width', '0%');

        function loadTable (){
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
                        targets: [0,1,2,3,4],
                        className: 'dt-body-center dt-head-center'
                    }
                ],
                autoWidth: false,
                paging: true,
                scrollCollapse: true,
                scrollY: '50vh',
                order: [[0, "desc"]]
            });

        }

        $('body').on('click', '.viewAptDetail', function(){

            let id = $(this).attr('value');
            patient_id = $(this).attr("data-p-id");

			$("#errorMessage").empty();
            $("#aptdtlsstatus").removeClass("text-success text-danger");
            $("#aptdtlsstatus").addClass("text-warning");
            $("#aptApplyBtn").attr("value", id);
            $("#setStatus option").prop('selected', function() {
                return this.defaultSelected;
            });
            $("#reasonDiv").hide();
            $("#reasonDiv option").prop('selected', function() {
                return this.defaultSelected;
            });
            $("#reasonOtherDiv").hide();
            $("#reasonOther").val("");
            $("#aptApplyBtn, #reason, #reasonOther, #setStatus").prop("disabled", false);

            fetchRequestDetails(id);
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

        $("#aptApplyBtn").click(function() {
			$("#errorMessage").empty();
            let id = $("#aptApplyBtn").attr("value");
            let pid = patient_id;
            let setStatus = $("#setStatus").val();
            let setStatusText = $( "#setStatus option:selected" ).text();
            let datetime = $("#aptdtlsstartdate").text() + " at " + $("#aptdtlsstarttime").text();

            if (setStatus == 1) {
                updateRequestStatus(id, pid, setStatus, setStatusText, datetime, null, null);
            } else if (setStatus == 2) {
                let reason = $("#reason").val();
                
                if (reason == 6) {
                    let reasonOther = $("#reasonOther").val();

                    updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, reasonOther);
                } else {
                    updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, null);
                }
            }

        });

        function updateRequestStatus(id, pid, setStatus, setStatusText, datetime, reason, reasonOther) {
			showLoader();
            var formData = {
                id: id,
                pid: pid,
                setStatus: setStatus,
                setStatusText: setStatusText,
                datetime: datetime,
                reason: reason,
                reasonOther: reasonOther
            };
            
            $.ajax({
                type: "POST",
                url: "php/update-request-status.php",
                data: formData,
                dataType: 'json'
            }).done(function (data) {
				hideLoader();
                $("#errorMessage").append('<div class="alert alert-success mt-3">' + data.message +  '</div>');

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
                console.log(data.responseText);
            });
        }

        function refreshList() {
            $.ajax({
                url: 'php/refresh-list.php',
                dataType: 'json'
            }).done(function (data) {
                $('#myTable').DataTable().destroy().clear();
                $('#tableBody').html(data);
                loadTable();
                // console.log(data);
            }).fail(function(data) {
                // console.log(data);
            });
        }

        $('body').on('click', '.viewPatientDetail', function(){
            fetchPatientDetails(patient_id);
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
                console.log(data.responseText);
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