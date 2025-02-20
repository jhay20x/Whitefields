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
	<link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp"/>
    <title>Dentists - Whitefields Dental Clinic</title>
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
    </style>
</head>
<body>
    <?php include "../../components/sidebar.php" ?>

    <!-- Modal -->
    <div class="modal fade" id="dentistViewModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dentistViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <svg class="" width="20" height="20" style="vertical-align: -.125em"><use xlink:href="#person-vcard"/></svg>                        
                    </h6>
                    <h6 class="ms-2">Dentist Information</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentistViewClose" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                        
                        <div class="row">
                            <div class="col-12 col-sm">
                                <h6>Name: <span id="dentistName" class="fw-normal"></span></h6>
                                <h6>Specialist: <span id="dentistSpecialist" class="fw-normal"></h6>
                                <h6>Birth Date: <span id="dentistBdate" class="fw-normal"></span></h6>
                                <h6>Gender: <span id="dentistGender" class="fw-normal"></span></h6>
                                <h6>Religion: <span id="dentistReligion" class="fw-normal"></span></h6>
                                <h6>About Me: <span id="dentistAboutMe" class="fw-normal"></span></h6>
                            </div>
                            <div class="col-12 col-sm">
                                <h6>Username: <span id="dentistUsername" class="fw-normal"></span></h6>
                                <h6>Email Address: <span id="dentistEmail" class="fw-normal"></span></h6>
                                <h6>Contact Number: <span id="dentistContact" class="fw-normal"></span></h6>
                                <h6>Nationality: <span id="dentistNationality" class="fw-normal"></span></h6>
                                <h6>Address: <span id="dentistAddress" class="fw-normal"></span></h6>
                            </div>
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
                <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#person-vcard"/></svg>
                <h1 class="col">Dentist</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <main class="col-md-9 my-3 bg-white row">
                <div class="my-3">
                    <!-- <div class="box">
                        <p id="txtHint">Select row to Update/Delete.</p>
                    </div> -->

                    <table id="myTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">Dentist ID</th>
                                <th class="col">Full Name</th>
                                <th class="col">Contact Number</th>
                                <th class="col">Specialist</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname) AS Name, 
                                di.id AS ID, di.contactno AS Contact, di.specialist AS Specialist
                                FROM dentist_info di;");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td id="dentistID">' . $row['ID'] . '</td>
                                        <td id="dentistName">' . $row['Name'] . '</td>
                                        <td id="dentistContact">' .  $row['Contact'] . '</td>
                                        <td id="dentistAge">' . $row['Specialist'] . '</td>
                                        <td class="appointID">
                                        <button type="button" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#dentistViewModal">View
                                        </button>
                                        </td>
                                    </tr>
                                ';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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
    $(document).ready(function () {
        $('#myTable thead th').eq(3).attr('width', '0%');
        
        DataTable.Buttons.defaults.dom.button.className = 'btn btn-primary text-white';

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
                            text: 'Add Dentist',
                            action: function (e, dt, node, config) {
                                $("#errorMessage").empty();
                                $('#appointRequestModal').modal('show');
                            }
                        }
                    ]
                },
                top1: {
                    // searchPanes: {
                    //     initCollapsed: true,
                    // },
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
                    targets: [0,1,2,3,4],
                    className: 'dt-body-center dt-head-center'
                }
            ],
            autoWidth: false,
            paging: true,
            scrollCollapse: true,
            scrollY: '50vh',
            order: [
                [0, "asc"]
            ]
        });

        // $('#dentistViewModal').modal('show');        

        $('body').on('click', '.viewAptDetail', function(){
            let id = $(this).attr('value');
            fetchDentistDetails(id);
        });

        function fetchDentistDetails(id) {            
            var formData = {
                id: id
            };

            $.ajax({
                type: "POST",
                url: "php/fetch-dentist-info.php",
                data: formData,
                dataType: 'json'
            }).done(function (data) {
                $("#dentistName").text(data.Name);
                $("#dentistUsername").text(data.username);
                $("#dentistBdate").text(data.bdate);
                $("#dentistContact").text(data.contactno);
                $("#dentistGender").text(data.gender);
                $("#dentistAddress").text(data.address);
                $("#dentistAboutMe").text(data.about_me);
                $("#dentistReligion").text(data.religion);
                $("#dentistNationality").text(data.nationality);
                $("#dentistEmail").text(data.email_address);                
                $("#dentistSpecialist").text(data.specialist);

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