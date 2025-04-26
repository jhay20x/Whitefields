<?php
session_start();

global $conn;
require_once '../../database/config.php';
require_once 'php/fetch-id.php';

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
    <title>Procedures List - Whitefields Dental Clinic</title>
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
        
        .invalidPassword {
            color: red;
        }  
        
        .validPassword {
            color: green;
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
<body class="bg-body-secondary">
    <?php include "../../components/sidebar.php" ?>

    <div id="overlay" style="display:none;">
        <div id="loader"></div>		
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addProcedureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addProcedureLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-journal-medical"></i> Add New Procedure
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="addProcedureClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/add-procedure.php" method="POST" class="col" id="myForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div id="addProcedureMessage" class="col-12" role="alert"></div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input maxlength="25" autocomplete="off" required name="procedureName" placeholder="Procedure Name"  id="procedureName" class="form-control onlyLetters">
                                        <label for="procedureName">Procedure Name</label>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input maxlength="255" autocomplete="off" name="procedureDesc" placeholder="Description"  id="procedureDesc" class="form-control onlyLetters">
                                        <label for="procedureDesc">Description</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12">
                                    <div class="input-group-text mb-3">
                                        <input type="checkbox" name="procedureInstallment" id="procedureInstallment" class="form-check">
                                        <label for="procedureInstallment" class="ms-3">Allow Installment?</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input maxlength="9" autocomplete="off" required name="procedurePriceMin" placeholder="Price (Min)"  id="procedurePriceMin" class="form-control onlyNumbersDots">
                                        <label for="procedurePriceMin">Price (Min)</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input maxlength="9" autocomplete="off" required name="procedurePriceMax" placeholder="Price (Max)"  id="procedurePriceMax" class="form-control onlyNumbersDots">
                                        <label for="procedurePriceMax">Price (Max)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success" id="procedureSaveBtn">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"data-bs-target="#cancelAddProcedureConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewProcedureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewProcedureLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="appointListLabel">
                        <i class="bi bi-journal-medical"></i> Procedure Details
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="viewProcedureClose" aria-label="Close"></button> -->
                </div>
                <form autocomplete="off" action="php/update-procedure.php" method="POST" class="col" id="viewProcedureForm">
                    <div class="modal-body">
                        <div class="container-fluid" id="viewProcedure">
                            <div id="viewProcedureMessage" class="col-12" role="alert"></div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input disabled maxlength="25" autocomplete="off" required name="viewProcedureName" placeholder="Procedure Name"  id="viewProcedureName" class="form-control onlyLetters">
                                        <label for="viewProcedureName">Procedure Name</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-floating">
                                        <select required class="form-control" name="viewProcedureStatus" id="viewProcedureStatus">
                                            <option value="1" class="text-success fw-bold">Active</option>
                                            <option value="0" class="text-danger fw-bold">Inactive</option>
                                        </select>
                                        <label class="form-label" for="viewProcedureStatus">Status</label>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input disabled maxlength="255" autocomplete="off" required name="viewProcedureDesc" placeholder="Description"  id="viewProcedureDesc" class="form-control onlyLetters">
                                        <label for="viewProcedureDesc">Description</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12">
                                    <div class="input-group-text mb-3">
                                        <input disabled type="checkbox" value="1" name="viewProcedureInstallment" id="viewProcedureInstallment" class="form-check">
                                        <label for="viewProcedureInstallment" class="ms-3">Allow Installment?</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input disabled maxlength="9" autocomplete="off" required name="viewProcedurePriceMin" placeholder="Price (Min)"  id="viewProcedurePriceMin" class="form-control onlyNumbersDots">
                                        <label for="viewProcedurePriceMin">Price (Min)</label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input disabled maxlength="9" autocomplete="off" required name="viewProcedurePriceMax" placeholder="Price (Max)"  id="viewProcedurePriceMax" class="form-control onlyNumbersDots">
                                        <label for="viewProcedurePriceMax">Price (Max)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="viewProcedureUpdateBtn">Update</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="viewProcedureBackBtn" data-bs-dismiss="modal" aria-label="Close">Back</button>
                        <button type="submit" class="btn btn-sm btn-outline-success d-none" disabled id="viewProcedureSaveBtn">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-danger d-none" disabled id="viewProcedureCancelBtn" data-bs-toggle="modal"data-bs-target="#cancelUpdatePatientConfirmModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelUpdatePatientConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelUpdatePatientConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelUpdatePatientConfirmLabel">
                        <i class="bi bi-journal-medical"></i> Add New Procedure
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="procedureCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#viewProcedureModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="cancelAddProcedureConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelAddProcedureConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="cancelAddProcedureConfirmLabel">
                        <i class="bi bi-journal-medical"></i> Add New Procedure
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to cancel editing this form?</h6>
                            <button type="button" value="" id="procedureCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="procedureCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#addProcedureModal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="deleteProcedureConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteProcedureConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h6 class="modal-title" id="deleteProcedureConfirmLabel">
                        <i class="bi bi-journal-medical"></i> Delete Procedure
                    </h6>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="text-center">
                            <h6>Are you sure to delete this procedure?</h6>
                            <button type="button" value="" id="deleteProcedureCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                            <button type="button" value="" id="deleteProcedureCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-dismiss="modal" aria-label="Close">No</button>
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
                <h1><i class="bi bi-journal-medical"></i></h1>
                <h1 class="col ms-3">Procedures</h1>

                <?php include "../../components/notification.php" ?>
            </div>

            <div class="col-md-9 my-3 rounded shadow bg-white row">
                <div class="my-3">
                    <div class="col">
                        <h3>Procedures Lists</h3>                        
                        <span>View and manage all related information about the clinic's procedures.</span>
                    </div>

                    <div id="errorMessage" class="col-12" role="alert"></div>

                    <table id="myTable" class="table-group-divider table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col">ID</th>
                                <th class="col">Procedure Name</th>
                                <th class="col">Installment</th>
                                <th class="col">Price (Low)</th>
                                <th class="col">Price (High)</th>
                                <th class="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM procedures");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();

                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td id="tableProcedureID">' . $row['id'] . '</td>
                                        <td id="tableProcedureName">' . $row['name'] . '</td>
                                        <td id="tableProcedureInstallment">' . ($row['allow_installment'] == 0 ? "No" : "Yes") . '</td>
                                        <td id="tableProcedurePriceMin">' . $row['price_min'] . '</td>
                                        <td id="tableProcedurePriceMax">' . $row['price_max'] . '</td>
                                        <td class="appointID">
                                            <button type="button" data-procedure-id="' . $row['id'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#viewProcedureModal">View</button>
                                            <button type="button" data-procedure-id="' . $row['id'] . '" class="btn btn-sm btn-outline-danger deleteProcedure mt-1 mt-lg-0" data-bs-toggle="modal" data-bs-target="#deleteProcedureConfirmModal"><i class="bi bi-x-lg"></i></button>
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
        LoadTable();

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));             
    
        $('body').on("blur", "#procedurePriceMin, #procedurePriceMax, #viewProcedurePriceMin, #viewProcedurePriceMax", function () {            
            let val = parseFloat(this.value);
            if (!isNaN(val)) {
                this.value = val.toFixed(2);
            }
        });   

        $('body').on("click", ".viewAptDetail", function () {
            let procedure_id = $(this).data("procedure-id");
            $("#viewProcedureSaveBtn").attr("data-procedure-id", procedure_id);
            LoadProcedureDetails(procedure_id);
            $("#viewProcedureUpdateBtn, #viewProcedureBackBtn").removeClass("d-none").prop("disabled", false);
            $("#viewProcedureSaveBtn, #viewProcedureCancelBtn").addClass("d-none").prop("disabled", true);
        });

        $('body').on("click", "#viewProcedureUpdateBtn", function () {
            $("#viewProcedureUpdateBtn, #viewProcedureBackBtn").addClass("d-none").prop("disabled", true);
            $("#viewProcedureSaveBtn, #viewProcedureCancelBtn").removeClass("d-none").prop("disabled", false);
            $("#viewProcedure").find("input, select").prop("disabled", false);
        });

        $('body').on("click", ".deleteProcedure", function () {
            let procedure_id = $(this).data("procedure-id");
            
            $("#deleteProcedureCancelYesBtn").attr("value", procedure_id);
        });

        $('body').on("click", "#deleteProcedureCancelYesBtn", function () {
            showLoader();
            $("#errorMessage, #addProcedureMessage, #viewProcedureMessage").empty();
            let procedure_id = $(this).val();

            var formData = {
					procedure_id: procedure_id
				};

			var url = "php/remove-procedure.php";

			$.ajax({
				type: "POST",
				url: url,
				data: formData,
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    hideLoader();
                    $("#errorMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
        });

        $('body').on("click", "#procedureCancelYesBtn", function () {
            $("#myForm")[0].reset();
            $("#errorMessage, #addProcedureMessage, #viewProcedureMessage").empty();
        });

        function LoadProcedureDetails (pid){
            $("#errorMessage, #viewProcedureMessage").empty();

            var formData = {
                pid: pid
            };

			var url = "php/fetch-procedure.php";

			$.ajax({
				type: "POST",
				url: url,
				data: formData,
                dataType: "json"
			}).done(function (data) {
                if (data.status) {
                    $("#viewProcedureStatus").addClass("text-success fw-bold").removeClass("text-danger");
                } else {
                    $("#viewProcedureStatus").addClass("text-danger fw-bold").removeClass("text-success");
                }

                let fields = ["#viewProcedureName", "#viewProcedureDesc", "#viewProcedureStatus", "#viewProcedurePriceMin", "#viewProcedurePriceMax"];
                let fieldsValues = [data.name, data.description, data.status, data.price_min, data.price_max];
                
                for (let index = 0; index < fieldsValues.length; index++) {
                    $(fields[index]).val(fieldsValues[index]).prop("disabled", true);
                }

                if (data.allow_installment) {
                    $("#viewProcedureInstallment").prop("checked", true);
                } else {
                    $("#viewProcedureInstallment").prop("checked", false);
                }

				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
        }

        $("#viewProcedureStatus").on("change", function () {         
            let status = $(this).val();

            if (status == 1) {
                $("#viewProcedureStatus").addClass("text-success").removeClass("text-danger");
            } else {
                $("#viewProcedureStatus").addClass("text-danger").removeClass("text-success");
            }
        });
        
        function LoadTable() {
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
                                text: 'Add Procedure',
                                action: function (e, dt, node, config) {
                                    $("#errorMessage").empty();
                                    $('#addProcedureModal').modal('show');
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
        };        

        // $('#patientViewModal').modal('show');    

		$("#myForm").submit(function(e){
            showLoader();
            $("#errorMessage, #addProcedureMessage, #viewProcedureMessage").empty();
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
                    $("#addProcedureMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		}); 

		$("#viewProcedureForm").submit(function(e){
            showLoader();
            $("#errorMessage, #addProcedureMessage, #viewProcedureMessage").empty();
			e.preventDefault();

			var url = $("#viewProcedureForm").attr('action');
            let pid = $("#viewProcedureSaveBtn").data("procedure-id");
            let formData = $("#viewProcedureForm").serialize();

			$.ajax({
				type: "POST",
				url: url,
				data: formData += '&pid=' + encodeURIComponent(pid),
                dataType: "json"
			}).done(function (data) {
                if (!data.success) {
                    hideLoader();
                    $("#viewProcedureMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    localStorage.setItem("errorMessage", data.message);
                    location.reload();
                }
				// console.log(data);
			}).fail(function(data) {
				// console.log(data);
			});
		});
        
        if (localStorage.getItem("errorMessage")){
            let message = localStorage.getItem("errorMessage");

            $("#errorMessage").append('<div class="mt-3 alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

            localStorage.removeItem("errorMessage");
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