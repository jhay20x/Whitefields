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
    if ($_SESSION['account_type'] == 1) {
        $id = fetchSecretaryID();

        $AppointToday;
        $AppointAll;
        $TotalDentist;
        $TotalPatient;

        $stmt = $conn->prepare("SELECT ar.id, 
            COUNT(CASE WHEN DATE(ar.start_datetime) = CURDATE() AND ar.appoint_status_id = 1
                THEN 1 
                END) AppointToday, 
			COUNT(CASE WHEN ar.appoint_status_id = 4
                THEN 1
               	END) AppointAll,
			(SELECT COUNT(di.id) FROM dentist_info di
            LEFT OUTER JOIN accounts ac
            ON di.accounts_id = ac.id
            WHERE ac.status != 0) TotalDentist,            
			(SELECT COUNT(pi.id) FROM patient_info pi) TotalPatient
            FROM appointment_requests ar;");

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $AppointToday = $row['AppointToday'];
            $AppointAll = $row['AppointAll'];
            $TotalDentist = $row['TotalDentist'];
            $TotalPatient = $row['TotalPatient'];
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
    <link rel="stylesheet" href="../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        .bi {
            fill: currentColor;
        }

        body {
            /* background-color: lightgrey; */
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
            .fc-button {
                font-size: .8rem !important;
            }

            .fc-toolbar-title {
                font-size: 1rem !important;
            }
            
            .card-text {
                font-size: 1.15rem;
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
                font-size: .5rem !important;
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
<body class="bg-body-secondary">    
    <?php include "../../components/sidebar.php" ?>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center position-relative">
            <div class="title position-sticky top-0 start-0 z-3 bg-white d-flex flex-row shadow align-items-center p-3">
                <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                    <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    <i class="bi bi-list"></i>
                </button>
                <h1><i class="bi bi-columns-gap"></i></h1>
                <h1 class="col ms-3">Dashboard: Secretary</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div id="cards">
                <div class="d-flex justify-content-center">
                    <div class="col col-sm-9">
                        <div class="row bg-white rounded shadow mt-3 p-3 d-flex justify-content-center row">
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
                                <i class="bi bi-calendar3"></i>
                                <h5 class="card-text">Appointment Today</h5>
                            </div>
                            <h1 class="card-title"><?php echo $AppointToday?></h1>
                        </div>
                    </div>

                    <div class="card rounded shadow h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">
                            <div class="d-flex column card-icon">
                                <i class="bi bi-calendar3"></i>
                                <h5 class="card-text">Pending Appointments</h5>
                            </div>
                            <h1 class="card-title"><?php echo $AppointAll?></h1>
                        </div>
                    </div>

                    <div class="card rounded shadow h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">                                
                            <div class="d-flex column card-icon">
                                <i class="bi bi-person"></i>
                                <h5 class="card-text">Total Patient</h5>
                            </div>
                            <h1 class="card-title"><?php echo $TotalPatient?></h1>
                        </div>
                    </div>

                    <div class="card rounded shadow h-50 text-end col-12 mx-2 my-2 col-sm-5 mx-sm-2 my-sm-2 col-md-5 mx-md-2 my-md-2 col-lg-5 mx-lg-2 my-lg-2 col-xl-auto mx-xl-2 my-xl-2 col-xxl-auto mx-xxl-2 my-xxl-2">
                        <div class="card-body">                                
                            <div class="d-flex column card-icon">
                                <i class="bi bi-person-vcard"></i>
                                <h5 class="card-text">Total Dentist</h5>
                            </div>
                            <h1 class="card-title"><?php echo $TotalDentist?></h1>
                        </div>
                    </div>
                </div>
            </div>
                
            <div id="calendarSection" class="d-flex justify-content-center">
                <div class="col col-sm-9">
                    <div class="row rounded shadow bg-white mb-3 py-3 py-xl-3 row d-flex justify-content-center">
                        <div id="errorMessage" class="col-10" role="alert">
                            <?php echo $hasId ? '' : '<div class="alert alert-danger">Please complete your profile first.</div>' ?>
                        </div>
        
                        <div class="row justify-content-start">
                            <div class="col" id="calendar"></div>
                            
                            
                            <div class="col-12 col-xl-auto mt-3 mt-xl-0">
                                <h5>Clinic Hours:</h5>

                                <div class="row col">                                
                                    <div class="col-auto">
                                        <h6>Sunday</h6>
                                        <h6>Monday</h6>
                                        <h6>Tuesday</h6>
                                        <h6>Wednesday</h6>
                                        <h6>Thursday</h6>
                                        <h6>Friday</h6>
                                        <h6>Saturday</h6>
                                    </div>
    
                                    <div class="col-auto">
                                        <?php 
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
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Monday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Tuesday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Wednesday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Thursday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Friday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                        case 'Saturday':
                                                            echo ($row['availability'] !== NULL) ? "<h6>Open, " . date('h:i A', strtotime($row['time_from'])) . ' - ' . date('h:i A', strtotime($row['time_to'])) . "</h6>" : "<h6>Closed</h6>";
                                                            break;
                                                    }            
                                                }
                                            }
    
                                        ?>
                                    </div>
                                </div>
                                <hr>
                                <h5>Dentist's Schedule:</h5>
                                <table class="table text-center table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>S</th>
                                            <th>M</th>
                                            <th>T</th>
                                            <th>W</th>
                                            <th>TH</th>
                                            <th>F</th>
                                            <th>S</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $stmt = $conn->prepare("SELECT di.lname, sc.Sun, sc.Mon, sc.Tue, sc.Wed, sc.Thu, sc.Fri, sc.Sat
                                                FROM dentist_info di
                                                LEFT OUTER JOIN schedules sc ON sc.dentist_id = di.id
                                                LEFT OUTER JOIN accounts ac ON di.accounts_id = ac.id
                                                WHERE ac.status != 0;");
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            $stmt->close();
        
                                            if ($result->num_rows > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                                <tr>
                                                    <td><?= $row['lname'];?></td>
                                                    <td><?= $row['Sun'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Mon'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Tue'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Wed'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Thu'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Fri'] ? "✔️" : "❌";?></td>
                                                    <td><?= $row['Sat'] ? "✔️" : "❌";?></td>
                                                </tr>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
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