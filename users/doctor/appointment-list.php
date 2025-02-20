<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

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

    <body>
        <?php include "../../components/sidebar.php" ?>

        <!-- Modal -->
        <div class="modal fade" id="appointListModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="appointListLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>                            
                        </h6>
                        <h6 class="ms-2">Appointment Details | Status: <strong id="aptdtlsstatus" class=""></strong></h6>
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
                                    <h6><span class="aptdtlsverdict" class="fw-normal"></span> Date: <span id="aptdtlsapproveddate" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsverdict" class="fw-normal"></span> Time: <span id="aptdtlsapprovedtime" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsverdict" class="fw-normal"></span> By: <span id="aptdtlsapprovedby" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-center align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Name: 
                                        <span id="aptdtlsname" class="fw-normal"></span>
                                        <button class="btn btn-sm p-0 viewPatientDetail" data-bs-toggle="modal" data-bs-target="#patientViewModal">
                                            <svg class="" width="16" height="16" style="vertical-align: -.125em"><use xlink:href="#eye"/></svg> View Profile
                                        </button>
                                    </h6>
                                    <h6>Appointment Date: <span id="aptdtlsstartdate" class="fw-normal"></span></h6>
                                    <h6>Appointment Time: <span id="aptdtlsstarttime" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr>

                            <div class="col-12 col-lg">
                                <h6>Oral Concern: <span id="aptdtlsconcern" class="fw-normal"></span></h6>
                            </div>
                             
                            <div class="d-flex justify-content-end">
                                <button type="button" value="" id="aptCancelBtn" class="btn btn-sm btn-primary m-2 me-0" data-bs-toggle="modal" data-bs-target="#treatPatientModal">Treatment Record</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="treatPatientModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="treatPatientLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="treatPatientLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person"/></svg>                            
                        </h6>
                        <h6 class="ms-2">Treatment Record</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointListClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-center align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Patient Name: <span id="" class="fw-normal patientName"></span></h6>
                                    <h6>Age: <span id="" class="fw-normal patientAge"></span></h6>
                                    <h6>Gender: <span id="" class="fw-normal patientGender"></span></h6>
                                </div>
                                <div class="col-12 col-lg">
                                    <h6></span> Appointment ID: <span id="aptId" class="fw-normal"></span></h6>
                                    <h6></span> Date: <span id="" class="fw-normal"><?php echo date(format: 'Y-m-d', timestamp: time()); ?></span></h6>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-center align-items-start row">
                                <div class="col">
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="patientToothNo">Tooth No./s</label>
                                        <input required type="text" name="patientToothNo" placeholder="Tooth No./s" id="patientToothNo" class="form-control">
                                    </div>
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="patientProcedure">Procedure</label>
                                        <input required type="text" name="patientProcedure" placeholder="Procedure" id="patientProcedure" class="form-control">
                                    </div>
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="date">Next Appointment</label>
                                        <input required type="date" name="date" placeholder="Code"  id="date" id="date" class="form-control">
                                    </div>
                                    <!-- <h6>Dentist: <span id="" class="fw-normal">Me</span></h6> -->
                                    <!-- <h6>Tooth No./s: <span id="" class="fw-normal">Tooth Restoration</span></h6>
                                    <h6>Procedure: <span id="" class="fw-normal">Tooth Restoration</span></h6>
                                    <h6>Next Appointment: <span id="" class="fw-normal">2025-03-09</span></h6> -->
                                </div>
                                <!-- <div class="col-12 col-lg">
                                    <h6>Amount Charged: <span id="" class="fw-normal"> P1000.00</span></h6>
                                    <h6>Amount Paid: <span id="" class="fw-normal">P500.00</span></h6>
                                    <h6>Balance: <span id="" class="fw-normal">P500.00</span></h6>
                                </div> -->
                            </div>
                             
                            <div class="d-flex justify-content-end">
                                <button type="button" value="" id="aptTreatPatientBackBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#appointListModal">Save</button>
                                <button type="button" value="" id="aptTreatPatientContinueBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelRequestConfirmModal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="cancelRequestConfirmModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="cancelRequestConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelRequestConfirmLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                        </h6>
                        <h6 class="ms-2">Appointment Cancellation Form</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal"data-bs-target="#appointListModal">Yes</button>
                                <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#treatPatientModal">No</button>
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
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
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
                    <h1 class="col">Appointment Lists</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 bg-white row">
                    <div class="my-3">
                        <!-- <div class="box">
                            <p id="txtHint">Select row to Update/Delete.</p>
                        </div> -->

                        <table id="myTable" class="table-group-divider table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="col">Appointment Date</th>
                                    <th class="col">Appointment Time</th>
                                    <th class="col-3">Name</th>
                                    <th class="col">Status</th>
                                    <th class="col">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $id = fetchDentistID();

                                $stmt = $conn->prepare("SELECT DATE(ar.start_datetime) AS Date, TIME(ar.start_datetime) AS Time, st.status_name AS Status, 
                                    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name, ar.id AS ID, ar.patient_id AS PID
                                    FROM appointment_requests ar
                                    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                    LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                    WHERE ar.appoint_status_id = 1 AND ar.dentist_info_id = ?;");
                                $stmt->bind_param('i', $id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                $status;

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $time = date('h:i A', strtotime($row['Time']));

                                        if ($row['Status'] == "Approved") {
                                            $status = "text-success";
                                        } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
                                            $status = "text-danger";
                                        } else {
                                            $status = "text-warning";
                                        }
                                        echo '
                                        <tr>
                                            <td id="appointDate">' . $row['Date'] . '</td>
                                            <td id="appointTime">' .  $time . '</td>
                                            <td id="appointName">' . $row['Name'] . '</td>
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

                            <tfoot>
                                <tr>
                                    <th class="col">Date</th>
                                    <th class="col">Time</th>
                                    <th class="col-3">Name</th>
                                    <th class="col">Status</th>
                                    <th class="col">Action</th>
                                </tr>
                            </tfoot>
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

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            // console.log(today);

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
                            preDefined: {
                                criteria: [
                                    {
                                        data: 'Appointment Date',
                                        condition: '=',
                                        // value: ["2025-02-08"]
                                        value: [today]
                                    }
                                ]
                            }
                        },
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
                    if (data.Status == "Approved") {
                        $("#aptdtlsstatus").removeClass("text-danger text-success");
                        $("#aptdtlsstatus").addClass("text-success");
                    } else if (data.Status == "Denied" || data.Status == "Cancelled") {
                        $("#aptdtlsstatus").removeClass("text-success text-warning");
                        $("#aptdtlsstatus").addClass("text-danger");
                    } else {                        
                        $("#aptdtlsstatus").removeClass("text-success text-danger");
                        $("#aptdtlsstatus").addClass("text-warning");
                    }

                    $("#aptdtlsstatus").text(data.Status);
                    $(".aptdtlsverdict").text(data.Status);
                    $("#aptdtlsdentist").text(data.Dentist);
                    $("#aptdtlsname").text(data.Name);
                    $("#aptdtlsstartdate").text(data.Start_Date);
                    $("#aptdtlsstarttime").text(data.Start_Time);
                    $("#aptdtlsrequestdate").text(data.Request_Date);
                    $("#aptdtlsrequesttime").text(data.Request_Time);
                    $("#aptdtlsapproveddate").text(data.Approved_Date);
                    $("#aptdtlsapprovedtime").text(data.Approved_Time);
                    $("#aptdtlsapprovedby").text(data.Approved_By);
                    $("#aptdtlsconcern").text(data.Concern);

                    if (data.length != 0) {
                        $('#appointListModal').modal('show');
                    }

                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
            }
            // $('#treatPatientModal').modal('show');

            // $('body').on('click', '.viewPatientDetail', function(){
            //     fetchPatientDetails(patient_id);
            // });

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
                    $('#aptId').text(patient_id);
                    $(".patientName").text(data.Name);
                    $("#patientUsername").text(data.username);
                    $(".patientAge").text(data.age);
                    $("#patientBdate").text(data.bdate);
                    $(".patientGender").text(data.gender);
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