<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 2) {
        $id = fetchPatientID();

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

    <body class="bg-body-secondary">
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
                            <div class="d-flex align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Request Date: <span id="aptdtlsrequestdate" class="fw-normal"></span></h6>
                                    <h6>Request Time: <span id="aptdtlsrequesttime" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex align-items-start row">
                                <div class="col-12 col-lg">
                                    <h6>Appointment Date: <span id="aptdtlsstartdate" class="fw-normal"></span></h6>
                                    <h6>Appointment Time: <span id="aptdtlsstarttime" class="fw-normal"></span></h6>
                                </div>
                                <div class="col-12 col-lg">
                                    <h6>Scheduled Dentist: <span id="aptdtlsdentist" class="fw-normal"></span></h6>
                                    <h6>Oral Concern: <span id="aptdtlsconcern" class="fw-normal"></span></h6>
                                </div>
                            </div>

                            <hr class="aptdtlsverdictdiv">

                            <div class="d-flex align-items-start row">
                                <div class="col-12 col-lg aptdtlsverdictdiv">
                                    <h6><span class="aptdtlsverdict"></span> Date: <span id="aptdtlsverdictdate" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsverdict"></span> Time: <span id="aptdtlsverdicttime" class="fw-normal"></span></h6>
                                    <h6><span class="aptdtlsverdict" class="fw-normal"></span> By: <span id="aptdtlsverdictby" class="fw-normal"></span></h6>
                                </div>

                                <div class="col-12 col-lg aptdtlsreasondiv">
                                    <h6><span class="fw-normal"></span> Reason: <span id="aptdtlsreason" class="fw-normal"></span></h6>
                                    <h6 class="aptdtlsverdictOther"><span class="fw-normal"></span> Other Details: <span id="aptdtlsreasonOther" class="fw-normal"></span></h6>
                                </div>
                            </div>
                             
                            <div class="d-flex justify-content-end">
                                <button type="button" value="" id="aptCancelBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelRequestModal">Cancel Appointment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="appointRequestModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="appointRequestLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointRequestLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                        </h6>
                        <h6 class="ms-2">Appointment Request Form</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="appointRequestClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
				            <div id="errorMessage" class="" role="alert"></div>

                            <form autocomplete="off" action="php/request-appointment.php" method="POST" class="text-center" id="myForm">
                                <div class="row col">
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="date">Date</label>
                                        <input required type="date" name="date" placeholder="Code"  id="date" id="date" class="form-control">
                                    </div>
                                    
                                    <div class="input-group my-3 d-flex align-items-center">
                                        <label class="input-group-text" for="timeHour">Time</label>
                                        <select required class="form-select" name="timeHour" id="timeHour">
                                            <option disabled selected value="">--</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </select>
                                        <h3 class="mx-3">:</h3>
                                        <select required class="form-select" name="timeMinute" id="timeMinute">
                                            <option disabled selected value="">--</option>
                                            <option value="00">00</option>
                                            <option value="30">30</option>
                                        </select>
                                        <h6 id="ampmText" class="mx-3">--</h6>
                                        
                                        <!-- <label class="input-group-text ms-4" for="prodSup">Product Supplier</label>
                                        <select required class="form-select" name="prodSup" id="prodSup">
                                            <option disabled selected value="">Choose...</option>
                                        </select> -->
                                        <!-- <input required type="time" min="1" max="24" name="prodName" placeholder="Name"  id="prodName" aria-label="Product Name" id="prodName" class="form-control"> -->
                                    </div>
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="dentist">Dentist</label>
                                        <input maxlength="100" required disabled type="text" name="dentist" placeholder="Dentist"  id="dentist" class="form-control">
                                        <!-- <select required class="form-select" name="dentist" id="dentist">
                                            <?php
                                                // $stmt = $conn->prepare("SELECT di.id AS ID, CONCAT(di.fname , ' ' , di.mname , ' ' , di.lname) AS Name FROM dentist_info di;");
                                                // $stmt->execute();
                                                // $result = $stmt->get_result();

                                                // if ($result->num_rows > 0) {
                                                //     while ($row = mysqli_fetch_assoc($result)) {
                                                //         echo '
                                                //             <option value="' . $row['ID'] . '">' . $row['Name'] . '</option>
                                                //         ';
                                                //     }
                                                // }
                                            ?>
                                        </select> -->
                                    </div>
                                    
                                    <!-- <div class="input-group my-3">
                                        <label class="input-group-text" for="procedure">Procedure</label>
                                        <select required class="form-select" name="procedure" id="procedure">
                                            <option disabled selected value="">Select procedure...</option>
                                            <?php
                                                // $stmt = $conn->prepare("SELECT tr.id AS ID, tr.name AS Treatment FROM treatments tr;");
                                                // $stmt->execute();
                                                // $result = $stmt->get_result();

                                                // if ($result->num_rows > 0) {
                                                //     while ($row = mysqli_fetch_assoc($result)) {
                                                //         echo '
                                                //             <option value="' . $row['ID'] . '">' . $row['Treatment'] . '</option>
                                                //         ';
                                                //     }
                                                // }
                                            ?>
                                        </select>
                                    </div> -->
                                    
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="concern">Oral Concern</label>
                                        <input maxlength="100" required type="text" name="concern" placeholder="Oral Concern"  id="concern" class="form-control">
                                    </div>
                                </div>

                                <input type="submit" class="btn btn-primary btn-md mt-1" value="Submit" name="addbtn" <?php echo $hasId ? '' : 'disabled'; ?>>
                            </form>
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
                                <h6>Are you sure to cancel this Appointment?</h6>
                                <button type="button" value="" id="aptCancelYesBtn" class="btn btn-sm btn-danger m-2 me-0">Yes</button>
                                <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#appointListModal">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="cancelRequestModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="cancelRequestLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelRequestLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                        </h6>
                        <h6 class="ms-2">Appointment Cancellation Form</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Please select a reason for your cancellation.</h6>  
                                <div class="input-group">
                                    <label class="input-group-text" for="cancelReason">Reason</label>
                                    <select required class="form-select" name="cancelReason" id="cancelReason">
                                        <?php
                                            $stmt = $conn->prepare("SELECT cr.id AS ID, cr.reason AS Reason FROM cancel_reasons cr;");
                                            $stmt->execute();
                                            $result = $stmt->get_result();
    
                                            if ($result->num_rows > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo '
                                                        <option value="' . $row['ID'] . '">' . $row['Reason'] . '</option>
                                                    ';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div id="cancelReasonOtherDiv">
                                    <div class="input-group my-3">
                                        <label class="input-group-text" for="cancelReasonOther">Other</label>
                                        <input autocomplete="off" type="text" name="cancelReasonOther" placeholder="Reason"  id="cancelReasonOther" class="form-control">
                                    </div>
                                </div>
                                <button type="button" value="" id="aptCancelContinueBtn" class="btn btn-sm btn-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#cancelRequestConfirmModal">Continue</button>
                                <button type="button" value="" id="aptCancelBackBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#appointListModal">Back</button>
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

                <div class="title d-flex flex-row shadow align-items-center p-3">
                    <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg>
                    </button>
                    <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#calendar3"/></svg>
                    <h1 class="col">My Appointments</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <main class="col-md-9">
                    <div id="cards" class="">
                        <div class="my-3">
                            <div class="bg-white rounded shadow row">
                                <div class="my-3">
                                    <div id="cancelMessage" role="alert"></div>

                                    <div class="">
                                        <!-- <div class="d-flex justify-content-end">
                                            <button id="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">Create Appointment</button>   
                                        </div> -->
    
    
                                        <table id="myTable" class="table-group-divider table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="col">Request Timestamp</th>
                                                    <!-- <th class="col">Request Time</th> -->
                                                    <th class="col">Appointment On</th>
                                                    <th class="col">Status</th>
                                                    <!-- <th class="col">Appointment Time</th> -->
                                                    <th class="col">Action</th>
                                                </tr>
                                            </thead>
    
                                            <tbody id="tableBody">
                                                <?php
                                                $id = fetchPatientID();

                                                // $stmt = $conn->prepare("SELECT DATE(ar.request_datetime) AS RequestDate, TIME(ar.request_datetime) AS RequestTime, 
                                                // DATE(ar.start_datetime) AS ApprovedDate, TIME(ar.start_datetime) AS ApprovedTime, 
    
                                                $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, 
                                                    ar.start_datetime AS ApprovedDateTime, 
                                                    st.status_name AS Status, ar.id AS ID
                                                    FROM appointment_requests ar
                                                    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
                                                    LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
                                                    WHERE ar.patient_id = ?
                                                    ORDER BY RequestDateTime ASC;");
                                                $stmt->bind_param('i', $id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
    
                                                $status = "";
    
                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $requesttime = date('Y-m-d h:i A', strtotime($row['RequestDateTime']));
                                                        $approvedtime = date('Y-m-d h:i A', strtotime($row['ApprovedDateTime']));
    
                                                        if ($row['Status'] == "Approved") {
                                                            $status = "text-success";
                                                        } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
                                                            $status = "text-danger";
                                                        } else {
                                                            $status = "text-warning";
                                                        }
                                                        // <td id="appointRequestTime">' .  $requesttime . '</td>
                                                        // <td id="appointApprovedTime">' .  $approvedtime . '</td>
                                                        echo '
                                                        <tr>
                                                            <td id="appointRequestDate">' . $requesttime . '</td>
                                                            <td id="appointApprovedDate">' . $approvedtime . '</td>
                                                            <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                                                            <td class="appointID">
                                                            <button type="button" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
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
                                                    <th class="col">Request Date</th>
                                                    <th class="col">Request Time</th>
                                                    <th class="col">Appointment Date</th>
                                                    <th class="col">Appointment Time</th>
                                                    <th class="col">Status</th>
                                                    <th class="col">Action</th>
                                                </tr>
                                            </tfoot> -->
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
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
            let dentist_id;
            let appoint_id;
            let cancel_reason_id;
            let cancel_reason_other;

            $("#myForm").submit(function(e){
                $("#errorMessage").empty();
                e.preventDefault();

                // $("#loginUserEmail, #userPassword, #signUpUsername, #signUpEmail").blur;

                var url = $("#myForm").attr('action');

                var formData = {
                    timeHour: $('#timeHour').val(),
                    timeMinute: $('#timeMinute').val(),
                    ampmText: $('#ampmText').text(),
                    date: $('#date').val(),
                    dentist: dentist_id,
                    concern: $('#concern').val()
                };

                // console.log($("form").serialize());

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: 'json'                
                }).done(function (data) {
                    if (!data.success) {
                        $("#errorMessage").append('<div class="alert alert-danger">' + data.error +  '</div>');
                    } else {
                        refreshList();
                        $("#date, #concern").val("");
                        $("#ampmText").text("--");
                        $('#dentist, #timeHour, #timeMinute').prop('selectedIndex', 0);
                        $("#errorMessage").append('<div class="alert alert-success">' + data.message +  '</div>');
                    }
                    // console.log(formData);
                    console.log(data.responseText);
                }).fail(function(data) {
                    // console.log(formData);
                    console.log(data.responseText);
                });
            });
            
            $(window).on('hide.bs.modal', function (e) {
                $("#date, #concern, #dentist").val("");
                $("#ampmText").text("--");
                $('#timeHour, #timeMinute').prop('selectedIndex', 0);
            })

            $("#date").on('change', function() {
                var formData = {
                    date: $('#date').val()
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-dentist.php",
                    data: formData,
                    dataType: 'json'                
                }).done(function (data) {
                    if (!data.success) {
                        $("#dentist").val(data.error);
                    } else {
                        $("#dentist").val(data.dentist);
                        dentist_id = data.dentist_id;
                    }
                    // console.log(formData);
                    // console.log(data.responseText);
                }).fail(function(data) {
                    // console.log(formData);
                    // console.log(data.responseText);
                });
            });

            $("#timeHour").on('change', function() {
                let hour = $("#timeHour").val();

                if (hour >= 9 && hour <= 11) {
                    $("#ampmText").text("AM");
                }

                if (hour == 12 || hour >= 1 && hour <= 6){
                    $("#ampmText").text("PM");
                }
            });

            $('#myTable thead th').eq(3).attr('width', '0%');            
            
            DataTable.Buttons.defaults.dom.button.className = 'btn btn-primary text-white';

            loadTable();

            function loadTable() {
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
                        topStart:{
                            search: true
                        },
                        topEnd: {
                            buttons: [
                                {
                                    text: 'Create Appointment',
                                    action: function (e, dt, node, config) {
                                        $("#errorMessage").empty();
                                        $('#appointRequestModal').modal('show');
                                    }
                                }
                            ]
                        },
                        top1: {
                            searchBuilder: {

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
                            targets: [0,1,2,3],
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

            $('body').on('click', '.viewAptDetail', function(){
                let id = $(this).attr('value');
                appoint_id = id;
                loadDetails(id);
            });

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
                        $(".aptdtlsverdictdiv").show();
                        $(".aptdtlsreasondiv").hide();
                        $("#aptCancelBtn").show();
                        $("#aptdtlsstatus").removeClass("text-danger text-warning");
                        $("#aptdtlsstatus").addClass("text-success");
                    } else if (data.Status == "Denied" || data.Status == "Cancelled") {
                        $(".aptdtlsverdictdiv").show();
                        $(".aptdtlsreasondiv").show();
                        $("#aptCancelBtn").hide();
                        if (data.ReasonOther || data.CancelReasonOther) {
                            $(".aptdtlsverdictOther").show();
                        } else {
                            $(".aptdtlsverdictOther").hide();
                        }
                        $("#aptdtlsstatus").removeClass("text-success text-warning");
                        $("#aptdtlsstatus").addClass("text-danger");
                    } else {                        
                        $(".aptdtlsverdictdiv").hide();
                        $("#aptCancelBtn").show();
                        $(".aptdtlsreasondiv").hide();
                        $("#aptdtlsstatus").removeClass("text-success text-danger");
                        $("#aptdtlsstatus").addClass("text-warning");
                    }

                    $("#aptdtlsstatus").text(data.Status);
                    $(".aptdtlsverdict").text(data.Status);
                    $("#aptdtlsdentist").text(data.Dentist);
                    $("#aptdtlsstartdate").text(data.Start_Date);
                    $("#aptdtlsstarttime").text(data.Start_Time);
                    $("#aptdtlsrequestdate").text(data.Request_Date);
                    $("#aptdtlsrequesttime").text(data.Request_Time);
                    $("#aptdtlsverdictdate").text(data.Approved_Date);
                    $("#aptdtlsverdicttime").text(data.Approved_Time);
                    $("#aptdtlsverdictby").text(data.Approved_By);
                    $("#aptdtlsconcern").text(data.Concern);

                    switch (data.Status) {
                        case "Cancelled":
                            $("#aptdtlsreason").text(data.CancelReason);
                            $("#aptdtlsreasonOther").text(data.CancelReasonOther);
                            $("#aptdtlsverdictdate").text(data.Cancel_Date);
                            $("#aptdtlsverdicttime").text(data.Cancel_Time);
                            $("#aptdtlsverdictby").text("Me");
                            break;                            
                        case "Denied":
                            $("#aptdtlsreason").text(data.Reason);
                            $("#aptdtlsreasonOther").text(data.ReasonOther);
                            break;
                        default:                            
                            $("#aptdtlsreason").text("");
                            $("#aptdtlsreasonOther").text("");     
                            break;
                    }

                    if (data.length != 0) {
                        $('#appointListModal').modal('show');
                    }

                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
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

            if (localStorage.getItem("cancelMessage")){
                let message = localStorage.getItem("cancelMessage");

                $("#cancelMessage").append('<div class="alert alert-success">' + message +  '</div>');

                localStorage.removeItem("cancelMessage")
            }

            $('#aptCancelBtn').on('click', function() {
                $("#cancelReasonOtherDiv").hide();      
                $("#cancelReasonOther").val('');
                $("#cancelReason option").prop('selected', function() {
                    return this.defaultSelected;
                });          
            });

            $('#aptCancelContinueBtn').on('click', function() {
                cancel_reason_id = $('#cancelReason').val();
                cancel_reason_other = $('#cancelReasonOther').val();
            });            

            $("#cancelReason").on('change', function() {
                let cancel_reason = $("#cancelReason").val();
                
                if (cancel_reason == 6) {
                    $("#cancelReasonOtherDiv").show();
                } else {
                    $("#cancelReasonOtherDiv").hide();
                    $("#cancelReasonOther").val('');
                }
            });

            $('#aptCancelYesBtn').on('click', function() {
                // e.preventDefault();
                $("#cancelMessage").empty();
                
                var formData = {
                    appoint_id: appoint_id,
                    cancel_reason_id: cancel_reason_id,
                    cancel_reason_other: cancel_reason_other
                };

                $.ajax({
                    type: "POST",
                    url: "php/cancel-appointment.php",
                    data: formData,
                    dataType: "json"
                }).done(function (data) {
                    localStorage.setItem("cancelMessage", data.message);
                    location.reload();
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
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