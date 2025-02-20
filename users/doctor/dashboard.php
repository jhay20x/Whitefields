<?php
session_start();

include '../../database/config.php';
include 'php/fetch-id.php';

// echo $_SESSION['account_type'];

// echo $_SESSION["email_address"];
// echo $_SESSION["passwordResetOTP"];
// echo $_SESSION['user_username'];
// echo $_SESSION['user_id'];

// $tablename = "qwert12345asdfg6789";
// // mkdir("../../files/".$tablename);
// $errors= array();
// $file_name = $_FILES['fileToUpload']['name'];
// $file_tmp =$_FILES['fileToUpload']['tmp_name'];
// $extensions= array("jpeg","jpg","png");
// move_uploaded_file($file_tmp,"../../files/".$tablename."/".$file_name);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 3) {
        $id = fetchDentistID();

        $AppointToday;
        $AppointAll;
        $TotalDentist;
        $TotalPatient;

        if (is_int($id)) {
            $stmt = $conn->prepare("SELECT ar.id, 
                COUNT(CASE WHEN DATE(ar.start_datetime) = CURDATE() AND ar.appoint_status_id = 1
                    THEN 1 
                    END) AppointToday, 
                COUNT(CASE WHEN ar.appoint_status_id = 1
                    THEN 1
                    END) AppointAll,         
                COUNT(CASE WHEN ar.dentist_info_id = ? AND ar.appoint_status_id != 4
                    THEN 1
                    END) TotalPatient
                FROM appointment_requests ar
                WHERE ar.dentist_info_id = ?;");

            $stmt->bind_param('ii', $id,$id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                $AppointToday = $row['AppointToday'];
                $AppointAll = $row['AppointAll'];
                // $TotalDentist = $row['TotalDentist'];
                $TotalPatient = $row['TotalPatient'];
            }
        } else {
            $AppointToday = 0;
            $AppointAll = 0;
            // $TotalDentist = $row['TotalDentist'];
            $TotalPatient = 0;
        }        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp"/>
    <title>Dashboard - Whitefields Dental Clinic</title>
    <link rel="stylesheet" href="../../resources/css/bootstrap.css">
    <link rel="stylesheet" href="../../resources/css/sidebar.css">
    
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

        #calendar {
            max-width: 1100px;
        }

        /* .fc-button {
            font-size: .5em !important;
        }

        .fc-toolbar-title {
            font-size: 1rem !important;
        } */        

        @media only screen and (min-width: 2501px) {            
            .card-text {
                font-size: 1.75rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 2.5rem;
            }
        }

        @media only screen and (min-width: 1201px) and (max-width: 2500px) {
            .card-text {
                font-size: 1.35rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 2.5rem;
            }
        }

        @media only screen and (min-width: 992px) and (max-width: 1200px) {
            .card-text {
                font-size: 1.5rem;
            }

            .card-title {
                font-size: 2.5rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.5rem;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 900px) {
            .card-text {
                font-size: 1.25rem;
            }

            .card-title {
                font-size: 2.25rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.5rem;
            }
        }

        @media only screen and (max-width: 600px) {
            .fc-button {
                font-size: .4rem !important;
            }

            .fc-toolbar-title {
                font-size: 1rem !important;
            }

            .title h1{
                font-size: 1.5rem !important;
            }

            .title svg{
                width: 1.25rem !important;
            }
            
            .card-text {
                font-size: 1rem;
            }

            .card-title {
                font-size: 2rem;
                /* font-weight: bold; */
            }

            .card-icon svg {
                height: auto;
                width: 1.25rem;
            }
        }
    </style>
</head>
<body>    
    <?php include "../../components/sidebar.php" ?>

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
                <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#columns-gap"/></svg>
                <h1 class="col">Dashboard: Dentist</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <!-- <div>
                <img src="../../resources/images/logo-icon-trans.webp" alt="Logo" name="fileToUpload" id="fileToUpload" width="50" height="50">
                
                <form action="" method="post" enctype="multipart/form-data">
                    Select image to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload Image" name="submit">
                </form>
            </div> -->

            <div id="cards">
                <div class="d-flex justify-content-center">
                    <div class="col col-sm-9">
                        <div class="row bg-white mt-3 p-3 d-flex justify-content-center row">
                            <div class="row">                                    
                                <h4 class="row">Announcements:</h4>
                                <span class="row">No new announcements.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex row mx-3 my-2 justify-content-center">
                    <div class="card h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">
                            <div class="d-flex column card-icon">
                                <svg class="bi pe-none me-2" width="35" height="35"><use xlink:href="#calendar3"/></svg>
                                <h5 class="card-text">Appointment Today</h5>
                            </div>
                            <h1 class="card-title"><?php echo $AppointToday?></h1>
                        </div>
                    </div>

                    <div class="card h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">
                            <div class="d-flex column card-icon">
                                <svg class="bi pe-none me-2" width="35" height="35"><use xlink:href="#calendar3"/></svg>
                                <h5 class="card-text">Total Appointment</h5>
                            </div>
                            <h1 class="card-title"><?php echo $AppointAll?></h1>
                        </div>
                    </div>

                    <div class="card h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">                                
                            <div class="d-flex column card-icon">
                                <svg class="bi pe-none me-2" width="35" height="35"><use xlink:href="#person"/></svg>
                                <h5 class="card-text">Total Patient</h5>
                            </div>
                            <h1 class="card-title"><?php echo $TotalPatient?></h1>
                        </div>
                    </div>

                    <!-- <div class="card h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">                                
                            <div class="d-flex column card-icon">
                                <svg class="bi pe-none me-2" width="35" height="35"><use xlink:href="#person-vcard"/></svg>
                                <h5 class="card-text">Total Dentist</h5>
                            </div>
                            <h1 class="card-title"><?php //echo $TotalDentist?></h1>
                        </div>
                    </div> -->
                </div>
                
                <div id="calendarSection" class="d-flex justify-content-center">
                    <div class="col col-sm-9">
                        <div class="row bg-white mb-3 p-3 row d-flex justify-content-center">
                            <div id="errorMessage" class="col-10" role="alert">
                                <?php echo $hasId ? '' : '<div class="alert alert-danger">Please complete your profile first.</div>' ?>
                            </div>
            
                            <div class="row d-flex justify-content-start">
                                <div class="col" id="calendar">

                                </div>
                                
                                <div class="col-auto mt-3 mt-sm-0">
                                    <h5>Clinic Hours:</h5>
                                    <h6>• Sunday - Closed</h6>
                                    <h6>• Monday - Open, 9:00 AM - 5:00PM</h6>
                                    <h6>• Tuesday - Open, 9:00 AM - 5:00PM</h6>
                                    <h6>• Wednesday - Open, 9:00 AM - 5:00PM</h6>
                                    <h6>• Thursday - Open, 9:00 AM - 5:00PM</h6>
                                    <h6>• Friday - Open, 9:00 AM - 5:00PM</h6>
                                    <h6>• Saturday - Open, 9:00 AM - 5:00PM</h6>
                                    <hr>
                                    <h5>Upcoming Holidays:</h5>
                                    <h6>• January 1, 2025 - New Year's Day</h6>
                                    <h6>• April 9, 2025 - Day of Valor</h6>
                                    <h6>• April 17, 2025 - Maundy Thursday</h6>
                                    <h6>• April 18, 2025 - Good Friday</h6>
                                    <h6>• May 1, 2025 - Labor Day</h6>
                                    <h6>• June 12, 2025 - Independence Day</h6>
                                    <h6>• August 25, 2025 - National Heroes Day</h6>
                                    <h6>• November 30, 2025 - Bonifacio Day</h6>
                                    <h6>• December 25, 2025 - Christmas Day</h6>
                                    <h6>• December 30, 2025 - Rizal Day</h6>                                    
                                </div>
                            </div>                    
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</body>

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/bootstrap.bundle.min.js"></script>
<script src='../../resources/js/index.global.js'></script>
<script src='../../resources/js/sidebar.js'></script>

<script>
    $(document).ready(function () {

        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prevYear,prev,next,nextYear today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        dayMaxEvents: false, // allow "more" link when too many events
        events: 'php/fetch-requests.php'
        });

        calendar.render();
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