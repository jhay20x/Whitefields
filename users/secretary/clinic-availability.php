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

        $sun = $mon = $tue = $wed = $thu = $fri = $sat = "";
        $sun_id = $mon_id = $tue_id = $wed_id = $thu_id = $fri_id = $sat_id = "";
        
        $stmt = $conn->prepare("SELECT * FROM store_availability");
        // $stmt->bind_param('i',$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();        

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                switch ($row['day']) {
                    case 'Sunday':
                        $sun = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $sun_id = $row['id'];
                        break;
                    case 'Monday':
                        $mon = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $mon_id = $row['id'];
                        break;
                    case 'Tuesday':
                        $tue = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $tue_id = $row['id'];
                        break;
                    case 'Wednesday':
                        $wed = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $wed_id = $row['id'];
                        break;
                    case 'Thursday':
                        $thu = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $thu_id = $row['id'];
                        break;
                    case 'Friday':
                        $fri = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $fri_id = $row['id'];
                        break;
                    case 'Saturday':
                        $sat = ($row['availability'] !== NULL) ? "Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) : "Closed";
                        $sat_id = $row['id'];
                        break;
                }            
            }
        }
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

        <!-- Modal -->
        <div class="modal fade" id="availabilityModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="availabilityLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="availabilityLabel">
                            <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#calendar3"/></svg>
                        </h6>
                        <h6 class="ms-2">Set Store Availability: <span id="day"></span></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="availabilityClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <form autocomplete="off" action="php/update-availability.php" method="POST" id="myForm" class="row col text-center">
                                <div class="row">
                                    <div id="errorMessageModal" class="" role="alert"></div>
                                    <div class="input-group my-3 d-flex align-items-center">
                                        <label class="input-group-text" for="availability">Availability</label>
                                        <select required class="form-select" name="availability" id="availability">
                                            <option disabled selected value="">--</option>
                                            <option value="1">Open</option>
                                            <option value="2">Close</option>
                                        </select>                                            
                                    </div>
                                </div>
                                        
                                <div class="row">
                                    <div class="input-group col-6 col-lg my-3 d-flex align-items-center">
                                        <label class="input-group-text" for="timeFrom">Time From</label>
                                        <select required class="form-control" name="timeFrom" id="timeFrom">
                                            <option disabled selected value="">--</option>
                                            <option value="01:00">01:00</option>
                                            <option value="02:00">02:00</option>
                                            <option value="03:00">03:00</option>
                                            <option value="04:00">04:00</option>
                                            <option value="05:00">05:00</option>
                                            <option value="06:00">06:00</option>
                                            <option value="07:00">07:00</option>
                                            <option value="08:00">08:00</option>
                                            <option value="09:00">09:00</option>
                                            <option value="10:00">10:00</option>
                                            <option value="11:00">11:00</option>
                                            <option value="12:00">12:00</option>
                                        </select>
                                        <select required class="form-control" name="timeFromAMPM" id="timeFromAMPM">
                                            <option disabled selected value="">--</option>
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                    
                                    <div class="input-group col-6 col-lg my-3 d-flex align-items-center">
                                        <label class="input-group-text" for="timeTo">Time To</label>
                                        <select required class="form-control" name="timeTo" id="timeTo">
                                            <option disabled selected value="">--</option>
                                            <option value="01:00">01:00</option>
                                            <option value="02:00">02:00</option>
                                            <option value="03:00">03:00</option>
                                            <option value="04:00">04:00</option>
                                            <option value="05:00">05:00</option>
                                            <option value="06:00">06:00</option>
                                            <option value="07:00">07:00</option>
                                            <option value="08:00">08:00</option>
                                            <option value="09:00">09:00</option>
                                            <option value="10:00">10:00</option>
                                            <option value="11:00">11:00</option>
                                            <option value="12:00">12:00</option>
                                        </select>
                                        <select required class="form-control" name="timeToAMPM" id="timeToAMPM">
                                            <option disabled selected value="">--</option>
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <input type="submit" class="btn btn-outline-primary btn-sm mt-1" value="Save" name="saveBtn">
                                </div>
                            </form>
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
                    <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#building-gear"/></svg>
                    <h1 class="col">Management</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="mt-3 row">
                        <div class="col-12 col-lg mb-3">
                            <div class="col">
                                <h3>Clinic Availability</h3>
                                <span>Set the operating time and day of the clinic that will reflect when requesting an appointment.</span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="ms-3">
                                    <div id="errorMessage" class="" role="alert"></div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Sunday</h5>
                                            <p class="mb-0"><?php echo $sun ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $sun_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Monday</h5>
                                            <p class="mb-0"><?php echo $mon ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $mon_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Tuesday</h5>
                                            <p class="mb-0"><?php echo $tue ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $tue_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Wednesday</h5>
                                            <p class="mb-0"><?php echo $wed ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $wed_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Thursday</h5>
                                            <p class="mb-0"><?php echo $thu ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $thu_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Friday</h5>
                                            <p class="mb-0"><?php echo $fri ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $fri_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-10 col-md-8 col-lg-6 col-xl-4">
                                            <h5 class="">Saturday</h5>
                                            <p class="mb-0"><?php echo $sat ?></p>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-outline-secondary availabilityBtn" value="<?php echo $sat_id ?>" data-bs-toggle="modal" data-bs-target="#availabilityModal"><svg class="bi pe-none" width="16" height="16"><use xlink:href="#pencil-square"/></svg></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-12 col-lg">
                            <div class="col">
                                <h3>Holidays</h3>
                                <span>Set the regular holidays that will reflect when requesting an appointment.</span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="row">
                                    <div class="row mb-3">
                                        <div class="ms-3">
                                            <div id="errorMessage" class="" role="alert"></div>
                                            <div class="row">
                                                <span>January 1, 2025 - New Year's Day</span>
                                                <span>April 9, 2025 - Day of Valor</span>
                                                <span>April 17, 2025 - Maundy Thursday</span>
                                                <span>April 18, 2025 - Good Friday</span>
                                                <span>May 1, 2025 - Labor Day</span>
                                                <span>June 12, 2025 - Independence Day</span>
                                                <span>August 25, 2025 - National Heroes Day</span>
                                                <span>November 30, 2025 - Bonifacio Day</span>
                                                <span>December 25, 2025 - Christmas Day</span>
                                                <span>December 30, 2025 - Rizal Day</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script src="../../resources/js/jquery-3.7.1.js"></script>
    <script src="../../resources/js/bootstrap.bundle.min.js"></script>
    <script src='../../resources/js/sidebar.js'></script>

    <script>
        $(document).ready(function() {
            let dayId;

            $("#myForm").submit(function(e){
                $("#errorMessage").empty();
                $("#errorMessageModal").empty();
                e.preventDefault();
                
                let availability = $('#availability').val();
                let timeFrom = $('#timeFrom').val() + " " + $('#timeFromAMPM').val();
                let timeTo = $('#timeTo').val() + " " + $('#timeToAMPM').val();

                var url = $("#myForm").attr('action');

                var formData = {
                    id: dayId,
                    timeFrom: timeFrom,
                    timeTo: timeTo,
                    availability: availability,
                    dayTxt: $("#day").text()
                };

                // console.log(formData);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: 'json'                
                }).done(function (data) {
                    if (!data.success) {
                        $("#errorMessageModal").append('<div class="alert alert-danger">' + data.error +  '</div>');
                    } else {
                        localStorage.setItem("errordiv", data.message);
                        location.reload();
                    }
                    // console.log(formData);
                    // console.log("Done");
                    //console.log(data);
                }).fail(function(data) {
                    // console.log(formData);
                    // console.log("Failed");
                    //console.log(data);
                });
            });

            $('#availability').on('change', function() {
                let avail = $('#availability').val();

                if (avail == 1) {
                    $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('disabled', false);
                } else {
                    $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('disabled', true);
                    $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('selectedIndex', 0);
                }
            });
            
            $('body').on('click', '.availabilityBtn', function(){
                $("#errorMessageModal").empty();
                let id = $(this).attr('value');
                loadDetails(id);
            });   
        
            if (localStorage.getItem("errordiv")) {
                let message = localStorage.getItem("errordiv");

                $("#errorMessage").append('<div class="alert alert-success">' + message +  '</div>');

                localStorage.removeItem("errordiv")
            }         

            function loadDetails(id) {
                dayId = id;

                var formData = {
                    id: id
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-availability.php",
                    data: formData,
                    dataType: 'json'
                }).done(function(data) {
                    let timeFrom = data.timeFrom.slice(0, -3);
                    let timeFromAMPM = data.timeFrom.slice(-2);
                    let timeTo = data.timeTo.slice(0, -3);
                    let timeToAMPM = data.timeTo.slice(-2);

                    $("#timeFrom").val(timeFrom);
                    $("#timeFromAMPM").val(timeFromAMPM);
                    $("#timeTo").val(timeTo);
                    $("#timeToAMPM").val(timeToAMPM);
                    $("#day").text(data.day);

                    if(data.availability == 1) {
                        $("#availability").val(1);
                        $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('disabled', false);
                    } else {
                        $("#availability").val(2);
                        $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('disabled', true);
                        $('#timeFrom, #timeTo, #timeFromAMPM, #timeToAMPM').prop('selectedIndex', 0);
                    }

                    if (data.length != 0) {
                        $('#appointListModal').modal('show');
                    }

                    //console.log(data);
                }).fail(function(data) {
                    //console.log(data);
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