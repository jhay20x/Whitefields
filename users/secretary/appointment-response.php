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
        <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/logo-icon-67459a47526b9.webp" />
        <title>Appointment List - Whitefields Dental Clinic</title>
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
        <script src="../../resources/js/jquery-3.7.1.js"></script>

        <style>
            .bi {
                fill: currentColor;
            }

            /* body {
                background-color: lightgrey;
            } */

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
        <div class="modal fade" id="deleteDeniedReasonConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteDeniedReasonConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="deleteDeniedReasonConfirmLabel">
                            <i class="bi bi-building-gear"></i> Delete Reason
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to delete this deny reason?</h6>
                                <button type="button" value="" id="deleteDeniedReasonCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                                <button type="button" value="" id="deleteDeniedReasonCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-dismiss="modal" aria-label="Close">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="deleteCancelReasonConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteCancelReasonConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="deleteCancelReasonConfirmLabel">
                            <i class="bi bi-building-gear"></i> Delete Reason
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to delete this cancel reason?</h6>
                                <button type="button" value="" id="deleteCancelReasonCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                                <button type="button" value="" id="deleteCancelReasonCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-dismiss="modal" aria-label="Close">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="cancelUpdateReasonConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelUpdateReasonConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelUpdateReasonConfirmLabel">
                            <i class="bi bi-building-gear"></i> Add New Reason
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelRequestConfirmClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" value="" id="reasonCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-dismiss="modal" aria-label="Close">Yes</button>
                                <button type="button" value="" id="aptCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#viewReasonModal">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="viewReasonModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewReasonLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-building-gear"></i> Reason Details
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="viewReasonClose" aria-label="Close"></button> -->
                    </div>
                    <form autocomplete="off" action="php/update-reason.php" method="POST" class="col" id="viewReasonForm">
                        <div class="modal-body">
                            <div class="container-fluid" id="viewReason">
                                <div id="viewReasonMessage" class="col-12" role="alert"></div>

                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input disabled maxlength="25" autocomplete="off" required name="viewReasonName" placeholder="Reason Name"  id="viewReasonName" class="form-control onlyLettersNumbers">
                                        <label for="viewReasonName">Reason Name</label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-floating">
                                        <select required class="form-control" name="viewReasonStatus" id="viewReasonStatus">
                                            <option value="1" class="text-success fw-bold">Active</option>
                                            <option value="0" class="text-danger fw-bold">Inactive</option>
                                        </select>
                                        <label class="form-label" for="viewReasonStatus">Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="viewReasonUpdateBtn">Update</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="viewReasonBackBtn" data-bs-dismiss="modal" aria-label="Close">Back</button>
                            <button type="submit" class="btn btn-sm btn-outline-success d-none" disabled id="viewReasonSaveBtn">Save</button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-none" disabled id="viewReasonCancelBtn" data-bs-toggle="modal"data-bs-target="#cancelUpdateReasonConfirmModal">Cancel</button>
                        </div>
                    </form>
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
                    <h1><i class="bi bi-building-gear"></i></h1>
                    <h1 class="col ms-3">Management</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="mt-3 row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="col">
                                <h3>Deny Reasons</h3>
                                <span>Set the pre-defined reasons when denying a client appointment.</span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="row mb-3">
                                    <div class="ms-3">
                                        <div id="deniedReasonMessage" class="" role="alert"></div>
                                        <form autocomplete="off" action="php/add-reason.php" method="POST" id="deniedReasonForm" class="row col text-center">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="form-floating">
                                                        <input type="text" required name="deniedReason" placeholder="Remarks"  id="deniedReason" class="form-control onlyLettersNumbers">
                                                        <label for="deniedReason">Reason</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto text-center">
                                                    <button type="submit" id="deniedReasonSubmitBtn" class="btn btn-outline-primary">Add</button>
                                                </div>
                                            </div>
                                        </form>

                                        <table id="deniedReasonsTable" class="table-group-divider overflow-auto table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="col">ID</th>
                                                    <th class="col">Reason</th>
                                                    <th class="col">Status</th>
                                                    <th class="col">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody id="deniedReasonsTableBody">
                                                <?php
                                                $stmt = $conn->prepare("SELECT * FROM rejected_reasons;");
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $stmt->close();

                                                $status;

                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        if ($row['id'] == 6) {
                                                            $state = "disabled";
                                                        } else {
                                                            $state = "";
                                                        }
                                                        echo '
                                                        <tr>
                                                            <td id="deniedReasonID">' . $row['id'] . '</td>
                                                            <td id="deniedReasonReason">' . $row['reason'] . '</td>
                                                            <td class="fw-bold ' . ($row['status'] != 0 ? "text-success" : "text-danger") . '" id="deniedReasonStatus">' . ($row['status'] != 0 ? "Active" : "Inactive") . '</td>
                                                            <td class="deniedReasonDel">
                                                                <button '. $state . ' type="button" data-id="' . $row['id'] . '" class="btn btn-sm btn-outline-primary deleteDeniedViewReason" data-bs-toggle="modal" data-bs-target="#viewReasonModal">View</button>
                                                                <button '. $state . ' type="button" data-id="' . $row['id'] . '" class="btn btn-sm btn-outline-danger deleteDeniedReason mt-1 mt-lg-0" data-bs-toggle="modal" data-bs-target="#deleteDeniedReasonConfirmModal"><i class="bi bi-x-lg"></i></button>
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

                        <div class="col-12 col-lg-6 mt-5 mt-lg-0">
                            <div class="col">
                                <h3>Cancel Reason</h3>
                                <span>Set the pre-defined reasons when a patient cancels an appointment.</span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="row mb-3">
                                    <div class="ms-3">
                                        <div id="cancelReasonMessage" class="" role="alert"></div>
                                        <form autocomplete="off" action="php/add-reason.php" method="POST" id="cancelReasonForm" class="row col text-center">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="form-floating">
                                                        <input type="text" required name="cancelReason" placeholder="Remarks"  id="cancelReason" class="form-control onlyLettersNumbers">
                                                        <label for="cancelReason">Reason</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto text-center">
                                                    <button type="submit" id="cancelReasonSubmitBtn" class="btn btn-outline-primary">Add</button>
                                                </div>
                                            </div>
                                        </form>

                                        <table id="cancelReasonsTable" class="table-group-divider overflow-auto table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="col">ID</th>
                                                    <th class="col">Reason</th>
                                                    <th class="col">Status</th>
                                                    <th class="col">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody id="cancelReasonsTableBody">
                                                <?php
                                                $stmt = $conn->prepare("SELECT * FROM cancel_reasons;");
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $stmt->close();

                                                $status;

                                                if ($result->num_rows > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        if ($row['id'] == 6) {
                                                            $state = "disabled";
                                                        } else {
                                                            $state = "";
                                                        }
                                                        echo '
                                                        <tr>
                                                            <td id="cancelReasonID">' . $row['id'] . '</td>
                                                            <td id="cancelReasonReason">' . $row['reason'] . '</td>
                                                            <td class="fw-bold ' . ($row['status'] != 0 ? "text-success" : "text-danger") . '" id="cancelReasonStatus">' . ($row['status'] != 0 ? "Active" : "Inactive") . '</td>
                                                            <td class="cancelReasonDel">
                                                                <button '. $state . ' type="button" data-id="' . $row['id'] . '" class="btn btn-sm btn-outline-primary deleteCancelViewReason" data-bs-toggle="modal" data-bs-target="#viewReasonModal">View</button>
                                                                <button '. $state . ' type="button" data-id="' . $row['id'] . '" class="btn btn-sm btn-outline-danger deleteCancelReason mt-1 mt-lg-0" data-bs-toggle="modal" data-bs-target="#deleteCancelReasonConfirmModal"><i class="bi bi-x-lg"></i></button>
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
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script src="../../resources/js/jquery-ui.js"></script>
    <script src="../../resources/js/bootstrap.bundle.min.js"></script>
    <script src='../../resources/js/index.global.js'></script>
    <script src='../../resources/js/sidebar.js'></script>
    <script src="../../resources/js/functions.js" defer></script>
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
            inputFilters();
            
            let dayId;

            loadDeniedTable();
            loadCancelTable();

            $('body').on("click", ".deleteCancelViewReason, .deleteDeniedViewReason", function () {
                let reason_id = $(this).data("id");

                let reason_type = "";

                if ($(this).is(".deleteDeniedViewReason")) {
                    reason_type = "Denied";
                } else {
                    reason_type = "Cancel";
                }

                $("#viewReasonSaveBtn").attr("data-id", reason_id).attr("data-type", reason_type);
                LoadReasonDetails(reason_id, reason_type);
                $("#viewReasonUpdateBtn, #viewReasonBackBtn").removeClass("d-none").prop("disabled", false);
                $("#viewReasonSaveBtn, #viewReasonCancelBtn").addClass("d-none").prop("disabled", true);
            });

            $('body').on("click", "#reasonCancelYesBtn", function () {
                $("#viewReasonForm")[0].reset();
                $("#errorMessage, #addReasonMessage, #viewReasonMessage").empty();
            });

            $("#viewReasonForm").submit(function(e){
                showLoader();
                $("#errorMessage, #addReasonMessage, #viewReasonMessage").empty();
                e.preventDefault();

                let url = $("#viewReasonForm").attr('action');
                let rid = $("#viewReasonSaveBtn").attr("data-id");
                let type = $("#viewReasonSaveBtn").attr("data-type");
                let formData = $("#viewReasonForm").serialize();

                let errorMessage = "";

                if (type == "Denied") {
                    errorMessage = "deniedReasonMessage";
                } else {
                    errorMessage = "cancelReasonMessage";
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData += '&rid=' + encodeURIComponent(rid) + '&type=' + encodeURIComponent(type),
                    dataType: "json"
                }).done(function (data) {
                    if (!data.success) {
                        hideLoader();
                        $(errorMessage).append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        localStorage.setItem("errorMessage", data.message);
                        location.reload();
                    }
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            });

            $('body').on("click", "#viewReasonUpdateBtn", function () {
                $("#viewReasonUpdateBtn, #viewReasonBackBtn").addClass("d-none").prop("disabled", true);
                $("#viewReasonSaveBtn, #viewReasonCancelBtn").removeClass("d-none").prop("disabled", false);
                $("#viewReason").find("input, select").prop("disabled", false);
            });

            $("#viewReasonStatus").on("change", function () {         
                let status = $(this).val();

                if (status == 1) {
                    $("#viewReasonStatus").addClass("text-success").removeClass("text-danger");
                } else {
                    $("#viewReasonStatus").addClass("text-danger").removeClass("text-success");
                }
            });

            function LoadReasonDetails (rid, type){
                $("#errorMessage, #viewReasonMessage").empty();

                var formData = {
                    rid: rid,
                    type: type
                };

                var url = "php/fetch-reason.php";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json"
                }).done(function (data) {
                    if (data.status) {
                        $("#viewReasonStatus").addClass("text-success fw-bold").removeClass("text-danger");
                    } else {
                        $("#viewReasonStatus").addClass("text-danger fw-bold").removeClass("text-success");
                    }

                    let fields = ["#viewReasonName", "#viewReasonStatus"];
                    let fieldsValues = [data.reason, data.status];
                    
                    for (let index = 0; index < fieldsValues.length; index++) {
                        $(fields[index]).val(fieldsValues[index]).prop("disabled", true);
                    }

                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            $('body').on("click", ".deleteDeniedReason, .deleteCancelReason", function () {
                let reason_id = $(this).data("id");

                if ($(this).is(".deleteDeniedReason")) {
                    $("#deleteDeniedReasonCancelYesBtn").attr("value", reason_id);
                } else {
                    $("#deleteCancelReasonCancelYesBtn").attr("value", reason_id);
                }                
            });

            $('body').on("click", "#deleteDeniedReasonCancelYesBtn, #deleteCancelReasonCancelYesBtn", function () {
                showLoader();
                $("#errorMessage, #addReasonMessage, #viewReasonMessage").empty();
                let reason_id = $(this).val();
                let reason_type = "";
                let errorMessage = "";

                if (this.id == "deleteDeniedReasonCancelYesBtn") {
                    reason_type = "Denied";
                    errorMessage = "deniedReasonMessage";
                } else {
                    reason_type = "Cancel";
                    errorMessage = "cancelReasonMessage";
                }

                var formData = {
                        reason_id: reason_id,
                        reason_type: reason_type
                    };

                var url = "php/remove-reason.php";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json"
                }).done(function (data) {
                    if (!data.success) {
                        hideLoader();
                        $("#" + errorMessage).append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        localStorage.setItem(errorMessage, data.message);
                        location.reload();
                    }
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            });

            $("#deniedReasonForm, #cancelReasonForm").submit( function (e) {
                showLoader();
                e.preventDefault();
                $("#errorMessage, #addReasonMessage, #viewReasonMessage").empty();
                let reason_id = $(this).val();
                let reason_type = "";
                let errorMessage = "";
                var url = "";

                if (this.id == "deniedReasonForm") {
                    reason_type = "Denied";
                    errorMessage = "deniedReasonMessage";
                } else {
                    reason_type = "Cancel";
                    errorMessage = "cancelReasonMessage";
                }

                url = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize() + '&reasonType=' + encodeURIComponent(reason_type),
                    dataType: "json"
                }).done(function (data) {
                    if (!data.success) {
                        hideLoader();
                        $("#" + errorMessage).append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        localStorage.setItem(errorMessage, data.message);
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
            } else if (localStorage.getItem("deniedReasonMessage")){
                let message = localStorage.getItem("deniedReasonMessage");

                $("#deniedReasonMessage").append('<div class="mt-3 alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                localStorage.removeItem("deniedReasonMessage");
            } else if (localStorage.getItem("cancelReasonMessage")){
                let message = localStorage.getItem("cancelReasonMessage");

                $("#cancelReasonMessage").append('<div class="mt-3 alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                localStorage.removeItem("cancelReasonMessage");
            };

            function loadDeniedTable (){
                let closedDatesTable = new DataTable('#deniedReasonsTable', {
                    select: false,
                    lengthMenu: [
                        [5, 10, 15, -1],
                        [5, 10, 15, 'All'],
                    ],
                    layout: {
                        topStart:{
                        },
                        topEnd: {
                            search: true,
                        },
                        top1: {
                        },
                        bottomStart: {
                            pageLength: true
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0,1,2,3],
                            className: 'dt-body-center dt-head-center align-middle'
                        }
                    ],
                    autoWidth: false,
                    paging: true,
                    scrollX: true,
                    scrollY: "30vh"
                });
            }

            function loadCancelTable (){
                let closedDatesTable = new DataTable('#cancelReasonsTable', {
                    select: false,
                    lengthMenu: [
                        [5, 10, 15, -1],
                        [5, 10, 15, 'All'],
                    ],
                    layout: {
                        topStart:{
                        },
                        topEnd: {
                            search: true,
                        },
                        top1: {
                        },
                        bottomStart: {
                            pageLength: true
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0,1,2,3],
                            className: 'dt-body-center dt-head-center align-middle'
                        }
                    ],
                    autoWidth: false,
                    paging: true,
                    scrollX: true,
                    scrollY: "30vh"
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