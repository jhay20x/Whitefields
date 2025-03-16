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
                                <h3>Dentist Schedule</h3>
                                <span>Set the schedules of the dentists by day.</span>
                            </div>
                        </div>

                        <div class="mt-3 ms-3">
                            <div class="row">
                                <?php
                                    $count = 0;
                                    $checked1;
                                    $checked2;
                                    $checked3;
                                    $checked4;
                                    $checked5;
                                    $checked6;
                                    $checked7;

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
                                            $count += 1;

                                            $row['Sun'] ? $checked1 = "checked" : $checked1 = "";
                                            $row['Mon'] ? $checked2 = "checked" : $checked2 = "";
                                            $row['Tue'] ? $checked3 = "checked" : $checked3 = "";
                                            $row['Wed'] ? $checked4 = "checked" : $checked4 = "";
                                            $row['Thu'] ? $checked5 = "checked" : $checked5 = "";
                                            $row['Fri'] ? $checked6 = "checked" : $checked6 = "";
                                            $row['Sat'] ? $checked7 = "checked" : $checked7 = "";

                                            echo '
                                        <div class="row mb-3">
                                            <div class="col-5 col-lg-2 d-flex align-items-center ms-1">
                                                <span>'. $row['Name'] .'</span>
                                            </div>
                                            
                                            <div class="col-6 row">
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioSun" disabled id="radioSun' . $count . '" ' . $checked1 . '>
                                                        <label class="form-check-label" for="radioSun' . $count . '">
                                                            Sun
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioMon" disabled id="radioMon' . $count . '" ' . $checked2 . '>
                                                        <label class="form-check-label" for="radioMon' . $count . '">
                                                            Mon
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioTue" disabled id="radioTue' . $count . '" ' . $checked3 . '>
                                                        <label class="form-check-label" for="radioTue' . $count . '">
                                                            Tue
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioWed" disabled id="radioWed' . $count . '" ' . $checked4 . '>
                                                        <label class="form-check-label" for="radioWed' . $count . '">
                                                            Wed
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioThur" disabled id="radioThur' . $count . '" ' . $checked5 . '>
                                                        <label class="form-check-label" for="radioThur' . $count . '">
                                                            Thu
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioFri" disabled id="radioFri' . $count . '" ' . $checked6 . '>
                                                        <label class="form-check-label" for="radioFri' . $count . '">
                                                            Fri
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col d-flex justify-content-start">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioSat" disabled id="radioSat' . $count . '" ' . $checked7 . '>
                                                        <label class="form-check-label" for="radioSat' . $count . '">
                                                            Sat
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
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

    <script>
        $(document).ready(function() {

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