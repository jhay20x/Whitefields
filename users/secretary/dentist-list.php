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
    <link rel="stylesheet" href="../../resources/css/loader.css">
    <link rel="stylesheet" href="../../resources/css/jquery-ui.css">
    <link rel="stylesheet" href="../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../resources/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="../../resources/css/buttons.bootstrap5.css">
    <link rel="stylesheet" href="../../resources/css/searchPanes.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/select.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/buttons.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/searchBuilder.dataTables.css" />
    <link rel="stylesheet" href="../../resources/css/dataTables.dateTime.css" />
    
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

        .invalidPassword {
            color: red;
        }  
        
        .validPassword {
            color: green;
        }
    </style>
</head>
<body class="bg-body-secondary">
    <?php include "../../components/sidebar.php" ?>

    <div id="overlay" style="display:none;">
        <div id="loader"></div>		
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dentistViewModal" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="dentistViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-person-vcard"></i> Dentist Information
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="dentistViewClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                        
                        <div class="row">
                            <div class="row">
                                <h5 class="col-auto my-auto">Account Status: <span id="dentistStatusText"></span></h5>
                                <div class="col-3" id="dentistStatusDiv" style="display: none;">                                    
                                    <select disabled required class="form-select" id="dentistStatus" name="dentistStatus">
                                        <option class="text-success h6" value="1">Active</option>
                                        <option class="text-danger h6" value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2 col-12 col-sm">
                                <h6>Name: <span id="dentistName" class="fw-normal"></span></h6>
                                <h6>Specialist: <span id="dentistSpecialist" class="fw-normal"></h6>
                                <h6>Birth Date: <span id="dentistBdate" class="fw-normal"></span></h6>
                                <h6>Age: <span id="dentistAge" class="fw-normal"></span></h6>
                                <h6>Gender: <span id="dentistGender" class="fw-normal"></span></h6>
                                <h6>Religion: <span id="dentistReligion" class="fw-normal"></span></h6>
                            </div>
                            <div class="col-12 col-sm">
                                <h6>Username: <span id="dentistUsername" class="fw-normal"></span></h6>
                                <h6>Email Address: <span id="dentistEmail" class="fw-normal"></span></h6>
                                <h6>Contact Number: <span id="dentistContact" class="fw-normal"></span></h6>
                                <h6>Nationality: <span id="dentistNationality" class="fw-normal"></span></h6>
                                <h6>Address: <span id="dentistAddress" class="fw-normal"></span></h6>
                                <h6>About Me: <span id="dentistAboutMe" class="fw-normal"></span></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="changeStatusBtn" class="btn btn-sm btn-outline-success">Change Status</button>
                    <button type="button" id="changeStatusBackBtn" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">Back</button>
                    <button type="button" style="display: none;" id="changeStatusSaveBtn" class="btn btn-sm btn-outline-success">Save</button>
                    <button type="button" style="display: none;" id="changeStatusCancelBtn" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#changeStatusCancelConfirmModal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="changeStatusCancelConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changeStatusCancelConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="changeStatusCancelConfirmLabel">
                        <i class="bi bi-person"></i> Change Status Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" id="changeStatusConfirmYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#dentistViewModal">Yes</button>
                            <button type="button" id="changeStatusConfirmNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#dentistViewModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDentistModal" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="addDentistLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="addDentistLabel">
                        <i class="bi bi-person-vcard"></i> Profile Information
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="addDentistClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/add-dentist.php" method="POST" class="col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="addDentistMessage" class="col-12" role="alert"></div>

                            <div class="col-lg-12">
                                <h5>Login Details</h5>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="form-floating mb-3">
                                        <input maxlength="35" autocomplete="off" required name="username" placeholder="Username"  id="username" class="form-control onlyLettersNoSpace">
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                                    
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="form-floating">
                                            <input maxlength="35" required autocomplete="off" type="email" name="email" placeholder="Email" id="email" class="form-control onlyEmail">
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" id="noemail" name="noemail" type="checkbox" data-bs-toggle="modal"data-bs-target="#noEmailConfirmModal">
                                            <label class="ms-1" for="noemail">N/A</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div class="input-group">
                                            <div class="form-floating">
                                                <input required autocomplete="new-password" type="password" minlength="6" maxlength="20" id="userPasswordCheck" class="form-control" name="userPasswordCheck" placeholder="Confirm Password">
                                                <label for="userPasswordCheck">Password</label>
                                            </div>
                                            <button class="btn btn-outline-secondary disableInputs input-group-text" type="button" id="togglePassword">
                                                <i id="eyeicon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div id="userPasswordCheckFeedback" class="mt-3" style="display: none;">
                                            <p id="userPassLower" class="invalidPassword">• Must use atleast one lower case letter.</p>
                                            <p id="userPassUpper" class="invalidPassword">• Must use atleast one upper case letter.</p>
                                            <p id="userPassNumber" class="invalidPassword">• Must use atleast one number.</p>
                                            <p id="userPassSymbol" class="validPassword">• Must not include any symbols except _.</p>
                                            <p id="userPassLength" class="invalidPassword">• Minimum of 6 characters. Max 20.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg">
                                    <div class="input-group mb-3">
                                        <div id="confirmPass" class="input-group is-invalid">
                                            <div class="form-floating">
                                                <input disabled required autocomplete="new-password" type="password" minlength="6" maxlength="20" id="confirmUserPasswordCheck" class="form-control" name="confirmUserPasswordCheck" placeholder="Confirm Password">
                                                <label for="confirmUserPasswordCheck">Confirm Password</label>
                                            </div>
                                            <button disabled class="btn btn-outline-secondary disableInputs" type="button" id="toggleConfirmPassword">
                                                <i id="eyeicon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div id="confirmUserPasswordCheckFeedback" class="mt-3" style="display: none;">
                                            <p id="confirmPassCompare" class="invalidPassword">• Passwords do not match.</p>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <h5>Personal Details</h5>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-lg">
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="35" type="text" required name="fname" placeholder="First Name"  id="fname" class="form-control onlyLetters">
                                            <label for="fname">First Name</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <div class="form-floating">
                                                <input maxlength="35" type="text" required name="mname" placeholder="Middle Name" id="mname"  class="form-control onlyLetters">
                                                <label for="mname">M. Name</label>
                                            </div>
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nomname" name="nomname" type="checkbox">
                                                <label class="ms-1" for="nomname">N/A</label>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="35" type="text" required name="lname" placeholder="Last Name"  id="lname" class="form-control onlyLetters">
                                            <label for="lname">Last Name</label>
                                        </div>
                                    </div>
                                        
                                    <div class="col-lg">
                                        <div class="input-group mb-3">
                                            <div class="form-floating">
                                                <input maxlength="10" type="text" required name="suffix" placeholder="Middle Name" id="suffix" class="form-control onlyLetters">
                                                <label for="suffix">Suffix</label>
                                            </div>
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="nosuffix" name="nosuffix" type="checkbox">
                                                <label class="ms-1" for="nosuffix">N/A</label>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="25" type="text" required name="specialist" placeholder="Age"  id="specialist" class="form-control onlyLetters">
                                            <label for="specialist">Specialist</label>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="date" required name="bdate" placeholder="Birth Date"  id="bdate" class="form-control">
                                            <label for="bdate">Birth Date</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg">    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="100" type="text" required name="address" placeholder="Address"  id="address" class="form-control onlyAddress">
                                            <label for="address">Address</label>
                                        </div>                                             
                                    </div>

                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <select required class="form-select" name="gender" id="gender">
                                                <option disabled selected value="">Select...</option>
                                                <option value="Female">Female</option>
                                                <option value="Male">Male</option>
                                                <option value="Nonbinary">Nonbinary</option>
                                                <!-- <option value="Other">Other</option> -->
                                                <option value="Decline to state">Decline to state</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>
        
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="25" type="text" required name="religion" placeholder="Religion"  id="religion" class="form-control onlyLetters">
                                            <label for="religion">Religion</label>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="25" type="text" required name="nationality" placeholder="Nationality"  id="nationality" class="form-control onlyLetters">
                                            <label for="nationality">Nationality</label>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input maxlength="11" type="text" required name="contnumber" placeholder="Contact Number" value="09" id="contnumber" class="form-control onlyNumbers">
                                            <label for="contnumber">Contact No.</label>
                                        </div>
                                    </div>
    
                                    <div class="col-lg">
                                        <div class="form-floating mb-3">
                                            <input type="text" required maxlength="100" name="aboutme" placeholder="Occupation" id="aboutme" class="form-control onlyLettersNumbers">
                                            <label for="aboutme">About Me</label>
                                        </div>                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>               
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success" id="addDentistSubmitBtn" name="addDentistSubmitBtn">Submit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"data-bs-target="#cancelAddDentistConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelAddDentistConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelAddDentistConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelAddDentistConfirmLabel">
                        <i class="bi bi-person"></i> Add Dentist Form
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" id="aptCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addDentistModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="noEmailConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="noEmailConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="noEmailConfirmLabel">
                        <i class="bi bi-person"></i> No Email Confirmation
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Checking this box will allow the system to create a dentist account without an email. Moreover, password recovery via email will not be possible until the dentist registers an email themselves. Do you want to proceed?</h6>
                            <button type="button" id="addDentistYesBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addDentistModal">Yes</button>
                            <button type="button" id="addDentistNoBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal" data-bs-target="#addDentistModal">No</button>
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
                    <i class="bi bi-list"></i>
                </button>
                <h1><i class="bi bi-person-vcard"></i></h1>
                <h1 class="col ms-3">Dentist</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div class="col-md-9 my-3 rounded shadow bg-white row">                
                <div class="my-3">
                    <div class="col">
                        <h3>Dentist Lists</h3>                        
                        <span>View all related information about the clinic's dentists.</span>
                    </div>
                    
                    <div id="errorMessage" class="col-12" role="alert"></div>

                    <table id="myTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">Dentist ID</th>
                                <th class="col">Full Name</th>
                                <th class="col">Contact Number</th>
                                <th class="col">Specialist</th>
                                <th class="col">Status</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
                                CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Name, 
                                di.id AS ID, di.contactno AS Contact, di.specialist AS Specialist, ac.status as Status
                                FROM dentist_info di
                                LEFT OUTER JOIN accounts ac
                                ON di.accounts_id = ac.id;");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td>' . $row['ID'] . '</td>
                                        <td>' . $row['Name'] . '</td>
                                        <td>' .  $row['Contact'] . '</td>
                                        <td>' . $row['Specialist'] . '</td>
                                        <td class="fw-bold ' . ($row['Status'] === 1 ? "text-success" : "text-danger") . '">' . ($row['Status'] === 1 ? "Active" : "Inactive") . '</td>
                                        <td class="appointID">
                                        <button type="button" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#dentistViewModal">View
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
            </div>
        </div>
    </div>
</body>

<script src="../../resources/js/jquery-3.7.1.js"></script>
<script src="../../resources/js/jquery-ui.js"></script>
<script src="../../resources/js/functions.js"></script>
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
        inputFilters();

        $('#myTable thead th').eq(3).attr('width', '0%');
        
        DataTable.Buttons.defaults.dom.button.className = 'btn btn-sm btn-outline-primary';

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
                                $('#addDentistModal').modal('show');
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
                    targets: [0,1,2,3,4,5],
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

		$("#myForm").submit(function(e){
            showLoader();
            $("#errorMessage").empty();
			e.preventDefault();

			var url = $("#myForm").attr('action');

			$.ajax({
				type: "POST",
				url: url,
				data: $("#myForm").serialize(),
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    hideLoader();
                    $("#addDentistMessage").append('<div class="mt-3 alert alert-danger">' + data.error +  '</div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				console.log(data);
			}).fail(function(data) {
				console.log(data);
			});
		});

		$("#changeStatusSaveBtn").click(function(e){
            showLoader();
            $("#errorMessage, #addDentistMessage").empty();
			e.preventDefault();

            var formData = {
                status: $("#dentistStatus").val(),
                id: $(this).attr("value")
            };

			$.ajax({
				type: "POST",
				url: "php/update-dentist-status.php",
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
				console.log(data);
			}).fail(function(data) {
				console.log(data);
			});
		});

        $("#dentistStatus").on("change", function() {
            if ($("#dentistStatus").val() == 1) {
                $("#dentistStatus").removeClass("text-danger");
                $("#dentistStatus").addClass("text-success");
            }else {
                $("#dentistStatus").removeClass("text-success");                
                $("#dentistStatus").addClass("text-danger");
            }
        });

        $("#changeStatusBtn").on("click", function() {
            $(this).hide();
            $("#changeStatusBackBtn, #dentistStatusText").hide();
            $("#changeStatusSaveBtn, #changeStatusCancelBtn, #dentistStatusDiv").show()
            $("#dentistStatusDiv select").prop("disabled", false);
        });

        $("#aptCancelYesBtn").on("click", function() {
            $("#myForm")[0].reset();
            $("#userPasswordCheck, #confirmUserPasswordCheck").removeClass("is-invalid");
        });

        $("#changeStatusConfirmYesBtn").on("click", function() {
            let id = $(this).val();

            $("#changeStatusBtn, #changeStatusBackBtn, #dentistStatusText").show();
            $("#changeStatusSaveBtn, #changeStatusCancelBtn, #dentistStatusDiv").hide();
            $("#dentistStatusDiv select").prop("disabled", true);
            $("#dentistStatus").val(id);
            styleStatus(id);
        });

        $("#addDentistNoBtn").on("click", function() {
            $("#noemail").attr("data-bs-toggle", "modal");
            $("#noemail").attr("data-bs-target", "#noEmailConfirmModal");
            $("#noemail").prop("checked", false);
            $("#email").prop("readonly", false);
            $("#email").val("");
        });

        $("#addDentistYesBtn").on("click", function() {
            $("#noemail").removeAttr("data-bs-toggle data-bs-target");
            $("#noemail").removeAttr("data-bs-target");
        });

        $("#noemail").on("click", function() {
            let id =  "#" + $(this).attr('id').substring(2);

            if ($(this).is(":checked")) {
                $(this).prop("checked", true);
                $(id).prop("readonly", true);
                $(id).val("None");
            } else {
                $(this).prop("checked", false);
                $(id).prop("readonly", false);
                $(id).val("");
                $(this).attr("data-bs-toggle", "modal");
                $(this).attr("data-bs-target", "#noEmailConfirmModal");
            }
        });

        $("#contnumber").on("focusin keypress focusout", function() {
            if (!this.value.startsWith("09")) {
                this.value = "09";
            }
        });
        
        if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");

            $("#errorMessage").append('<div class="mt-3 alert alert-success">' + message +  '</div>');

            localStorage.removeItem("errorMessage")
        };

        $('body').on('click', '.viewAptDetail', function(){
            let id = $(this).attr('value');
            fetchDentistDetails(id);
        });

        function styleStatus(status) {
            if (status == 1) {
                $("#dentistStatusText, #dentistStatus").addClass("text-success");
                $("#dentistStatusText, #dentistStatus").removeClass("text-danger");
            } else {
                $("#dentistStatusText, #dentistStatus").addClass("text-danger");
                $("#dentistStatusText, #dentistStatus").removeClass("text-success");                    
            }
        }

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
                let status = data.status == 1 ? "Active" : "Inactive";
                let statusStyle;
                
                styleStatus(data.status);

                $("#changeStatusSaveBtn").val(data.AccountID);
                $("#dentistStatus").val(data.status);
                $("#changeStatusConfirmYesBtn").val(data.status);

                let detailsId = [
                    "#dentistStatusText", "#dentistName", "#dentistUsername",
                    "#dentistBdate", "#dentistAge", "#dentistContact", "#dentistGender", "#dentistAddress",
                    "#dentistAboutMe", "#dentistReligion", "#dentistNationality", "#dentistEmail", "#dentistSpecialist"
                ];

                let details = [
                    status, data.Name, data.username, 
                    data.bdate, data.age, data.contactno, data.gender, data.address, 
                    data.about_me, data.religion, data.nationality, data.email_address, data.specialist
                ];

                for (let index = 0; index < details.length; index++) {
                    $(detailsId[index]).text(details[index]);
                }

                //console.log(data.responseText);
            }).fail(function(data) {
                //console.log(data.responseText);
            });
        }

        $("#nomname, #nosuffix").click(function() {
            let id =  "#" + $(this).attr('id').substring(2);

            if ($(this).is(":checked")) {
                $(id).prop("readonly", true);
                $(id).val("None");
            } else {                
                $(id).prop("readonly", false);
                $(id).val("");
            }
        });

        $("#togglePassword, #toggleConfirmPassword").on("click", function() {
            let passwordInput = this.id == "togglePassword" ? "#userPasswordCheck" : "#confirmUserPasswordCheck";
            
            if ($(passwordInput).attr("type") == "password") {
                $(passwordInput).attr("type", "text");
                $("#" + this.id + " i").removeClass(['bi', 'bi-eye']);
                $("#" + this.id + " i").addClass(['bi', 'bi-eye-slash']);
            } else {
                $(passwordInput).attr("type", "password");
                $("#" + this.id + " i").removeClass(['bi', 'bi-eye-slash']);
                $("#" + this.id + " i").addClass(['bi', 'bi-eye']);
            }
        });

        $('#userPasswordCheck, #confirmUserPasswordCheck').focusin("click", function() {
            $("#" + this.id + "Feedback").show();
        });

        $('#userPasswordCheck, #confirmUserPasswordCheck').focusout("click", function() {
            $("#" + this.id + "Feedback").hide();
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