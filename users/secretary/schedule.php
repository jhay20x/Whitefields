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
        <link rel="stylesheet" href="../../resources/css/loader.css">
        <link rel="stylesheet" href="../../resources/css/sidebar.css">
        <link rel="stylesheet" href="../../resources/css/jquery-ui.css">
        <link rel="stylesheet" href="../../resources/css/bootstrap-icons.min.css">

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

                .modal-body h5{
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
        <div class="modal fade" id="editScheduleCancelConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editScheduleCancelConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="editScheduleCancelConfirmLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person"/></svg>
                        </h6>
                        <h6 class="ms-2">Change Status Form</h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" id="editScheduleConfirmYesBtn" class="btn btn-sm btn-danger m-2 me-0">Yes</button>
                                <button type="button" id="editScheduleConfirmNoBtn" class="btn btn-sm btn-success m-2 me-0" data-bs-dismiss="modal" aria-label="Close">No</button>
                            </div>
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
                        <svg class="bi pe-none" width="16" height="16"><use xlink:href="#list"/></svg>
                    </button>
                    <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#table"/></svg>
                    <h1 class="col">Schedule</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="my-3">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <h3 class="col col-lg-8 col-xl-6">Dentist Schedule</h3>
                                    <div class="col-auto">
                                        <button id="editSchedule" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Dentist Schedule"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>                                
                                        <button type="button" style="display: none;" id="editScheduleSaveBtn" class="btn btn-sm btn-success">Save</button>
                                        <button type="button" style="display: none;" id="editScheduleCancelBtn" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#editScheduleCancelConfirmModal">Cancel</button>
                                    </div>
                                </div>
                                <span>Set the schedules of the dentists by day. Schedules set here will reflect on the appointment requests.</span>
                            </div>
                        </div>

                        <div id="errorMessage" class="col-12" role="alert"></div>

                        <div class="mt-3 ms-3">
                            <div class="row">
                                <?php
                                    $count = 0;
                                    $checked = $checked2 = $checked3 = $checked4 = $checked5 = $checked6 = $checked7 = "";

                                    $stmt = $conn->prepare("SELECT di.id AS ID, CONCAT(di.fname , ' ' , di.mname , ' ' , di.lname) AS Name,
                                        sc.Sun, sc.Mon, sc.Tue, sc.Wed, sc.Thu, sc.Fri, sc.Sat
                                        FROM dentist_info di
                                        LEFT OUTER JOIN schedules sc ON sc.dentist_id = di.id
                                        LEFT OUTER JOIN accounts ac ON di.accounts_id = ac.id
                                        WHERE ac.status != 0");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {                                            
                                            echo '<div class="row d-flex align-items-center mb-3">
                                                    <div class="col-12 col-lg-2 ms-1">
                                                        <span class="dentistName" value="' . $row['ID'] . '">' . $row['Name'] . '</span>
                                                    </div>
                                                    <div class="col-12 col-lg-6 row">';

                                            $days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

                                            foreach ($days as $day) {
                                                $checked = !empty($row[$day]) ? "checked" : "";
                                                    echo '<div class="col-auto scheduleList">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled name="radio' . $day . '" id="radio' . $day . $count . '" ' . $checked . '>
                                                                <label class="form-check-label" for="radio' . $day . $count . '">
                                                                    ' . $day . '
                                                                </label>
                                                            </div>
                                                        </div>';
                                            }
                                            
                                            echo '  </div>
                                                </div>';
                                                
                                            $count++;
                                        }
                                    }
                                ?>
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
        $(document).ready(function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            $("#editSchedule").click(function(e) {
                $(this).hide();
                $("#editScheduleSaveBtn, #editScheduleCancelBtn").show();
                $(".scheduleList input[type='radio']").prop("disabled", false);
            });

            $("#editScheduleConfirmYesBtn").click(function(e) {
                location.reload();
            });

            $("#editScheduleSaveBtn").click(function(e) {
                showLoader();
                $("#errorMessage").empty();

                let dentistId = [];
                let values = [];

                $(".dentistName").each(function(e) {
                    dentistId.push($(this).attr("value"));
                });

                $(".scheduleList input[type='radio']").each(function(e) {
                    if ($(this).is(":checked")) {
                        values.push(1);
                    } else {
                        values.push(null);
                    }
                });

                var formData = {
                    dentistId: dentistId,
                    values: values
                };

                $.ajax({
                    type: "POST",
                    url: "php/insert-update-schedule.php",
                    data: formData,
                    dataType: "json"
                }).done(function (data) {
                    if (!data.success) {
                        hideLoader();
                        $("#addDentistMessage").append('<div class="mt-3 alert alert-danger">' + data.error +  '</div>');
                    } else {
                        localStorage.setItem("errorMessage", data.message);
                        location.reload();
                    }
                    // console.log(data.responseText);
                }).fail(function(data) {
                    // console.log(data.responseText);
                });
            });
            
            if (localStorage.getItem("errorMessage")){
                let message = localStorage.getItem("errorMessage");

                $("#errorMessage").append('<div class="mt-3 alert alert-success">' + message +  '</div>');

                localStorage.removeItem("errorMessage")
            };
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