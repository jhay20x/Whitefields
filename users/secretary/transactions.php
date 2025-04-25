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
        <title>Payment and Transactions - Whitefields Dental Clinic</title>
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
        <div class="modal fade" id="transactionViewModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="transactionViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-clock-history"></i> Current Pending and Completed Transactions
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionViewClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">                            
                            <table id="transactionsTable" class="table-group-divider table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th class="col">Transaction ID</th> -->
                                        <th class="col">Appointment ID</th>
                                        <th class="col">Status</th>
                                        <th class="col-3">Appointment Date & Time</th>
                                        <th class="col">Action</th>
                                    </tr>
                                </thead>
    
                                <tbody id="transactionsTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-outline-primary" id="transactionViewBackBtn" data-bs-dismiss="modal">Back</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="transactionDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="transactionDetailsLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-clock-history"></i> Payments and Transactions
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionDetailsClose" aria-label="Close"></button> -->
                    </div>
                    <form autocomplete="off" action="php/insert-transaction.php" method="POST" id="myForm">
                        <div class="modal-body">
                            <div class="container">
                                <h5>Payment Details</h5>

                                <div id="transactionMessage" class="" role="alert"></div>

                                <div class="mt-3 row">
                                    <div class="col-12 col-lg-6 align-items-center">
                                        <h6 class="h6">Patient Name: <span id="transDetailPatientName" class="fw-normal"></span><input id="transactionDetailsPid" type="hidden" name="patient_id" value=""></h6>
                                        <h6 class="h6">Appointment ID: <span id="transDetailAptID" class="fw-normal"></span><input id="transactionDetailsAptId" type="hidden" name="appointment_requests_id" value=""></h6>
                                        <h6 class="h6">Processed By: <span id="transDetailProcessed" class="fw-normal"></span></h6>
                                    </div>
    
                                    <div class="col-12 col-lg-6">
                                        <!-- <h6 class="h6">Date & Time: <span id="transDetailDateTime" class="fw-normal"></span></h6> -->
                                        
                                        <div id="paymentTypeDiv" class="col-12 col-xl-8 mb-3">
                                            <div class="input-group col">
                                                <label class="input-group-text" for="paymentType">Payment Type</label>
                                                <select required class="form-select" name="paymentType" id="paymentType">
                                                    <option disabled selected value="">Select Payment Type...</option>
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT * FROM `payment_types`;");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $stmt->close();
        
                                                        if ($result->num_rows > 0) {
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                echo '
                                                                    <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                                                                ';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>                                    
        
                                        <div id="paymentRefDiv" class="col-12 col-xl-8 d-none">
                                            <div class="input-group">
                                                <label class="input-group-text" for="paymentRefNo">Payment Ref. No.</label>
                                                <input autocomplete="off" disabled required type="text" name="paymentRefNo" placeholder="(If applicable)"  id="paymentRefNo" class="form-control onlyNumbers">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-12 mt-3 table-responsive">
                                        <table class="table-group-divider table table-hover table-striped text-center">
                                            <thead>
                                                <tr>
                                                    <th class="col">Procedure</th>
                                                    <th class="col">Total Amount</th>
                                                    <th class="col">Amount Paid</th>
                                                    <th class="col">Remaining Balance</th>
                                                    <th class="col">Payment</th>
                                                </tr>
                                            </thead>
                                            <tbody id="transactionDetailsTableBody">
                                            </tbody>
                                        </table>
                                    </div>
    
                                    <div class="col-12 mt-3">
                                        <div id="transactionHistoryView" class="accordion">
                                            <div id="transactionHistoryViewItem" class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" id="transactionHistoryViewBtn" type="button" data-bs-toggle="collapse" data-bs-target="#transactionHistory" aria-expanded="false" aria-controls="transactionHistory">
                                                        <span class="fw-semibold">Transaction History</span>
                                                    </button>
                                                </h2>
                                                <div id="transactionHistory" class="accordion-collapse collapse" data-bs-parent="#transactionHistoryView">
                                                    <div class="accordion-body">
                                                        <table id="transactionHistoryTable" class="table-group-divider table table-hover table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Procedure</th>
                                                                    <th>Amount Paid</th>
                                                                    <th>Remaining Balance</th>
                                                                    <th>Timestamp</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="transactionHistoryTableBody">
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
                        <div class="modal-footer">
                            <button type="submit" id="transactionDetailsSaveBtn" class="btn btn-sm btn-outline-success">Save</button>
                            <button type="button" id="transactionDetailsCancelBtn" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelTransactionModal">Cancel</button>
                            <button type="button" id="transactionDetailsBackBtn" class="btn btn-sm btn-outline-primary d-none" data-bs-toggle="modal" data-bs-target="#transactionViewModal">Back</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="transactionHistoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="transactionHistoryLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="appointListLabel">
                            <i class="bi bi-clock-history"></i> Transactions History
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionHistoryClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <h5>Transaction Details</h5>
                            
                            <div class="mt-3 row">
                                <div class="col-12 col-lg-6">
                                    <h6 class="h6">Patient Name: <span id="transHistoryName" class="fw-normal"></span></h6>
                                    <h6 class="h6">Transaction ID: <span id="transHistoryTransId" class="fw-normal"></span></h6>
                                    <h6 class="h6">Appointment ID: <span id="transHistoryAptId" class="fw-normal"></span></h6>
                                    <h6 class="h6">Procedure: <span id="transHistoryProcedure" class="fw-normal"></span></h6>
                                    <h6 class="h6">Amount Paid: <span id="transHistoryAmountPaid" class="fw-normal"></span></h6>
                                    <h6 class="h6">Remaining Balance: <span id="transHistoryRemaining" class="fw-normal"></span></h6>
                                </div>
                                
                                <div class="col-12 col-lg-6">
                                    <h6 class="h6">Date & Time: <span id="transHistoryDatetime" class="fw-normal"></span></h6>
                                    <h6 class="h6">Payment Type: <span id="transHistoryPaymentType" class="fw-normal"></span></h6>
                                    <h6 class="h6">Payment Reference No: <span id="transHistoryPaymentRef" class="fw-normal"></span></h6>
                                    <h6 class="h6">Processed By: <span id="transHistoryProcessed" class="fw-normal"></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transactionDetailsModal">Back</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="cancelTransactionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelTransactionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h6 class="modal-title" id="cancelTransactionLabel">
                            <i class="bi bi-calendar3"></i> Update Appointment Form
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="cancelTransactionClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center">
                                <h6>Are you sure to cancel editing this form?</h6>
                                <button type="button" value="" id="transactionCancelYesBtn" class="btn btn-sm btn-outline-danger m-2 me-0" data-bs-toggle="modal"data-bs-target="#transactionViewModal">Yes</button>
                                <button type="button" value="" id="transactionCancelNoBtn" class="btn btn-sm btn-outline-success m-2 me-0" data-bs-toggle="modal" data-bs-target="#transactionDetailsModal">No</button>
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
                    <h1><i class="bi bi-clock-history"></i></h1>
                    <h1 class="col ms-3">Payment and Transactions</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="my-3">
                        <div class="col">
                            <h3>Payment and Transactions Lists</h3>
                            <span>View and manage all of the client's pending and completed transactions.</span>
                        </div>

                        <div id="errorMessage" class="" role="alert"></div>

                        <table id="patientListTable" class="table-group-divider table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="col">Patient ID</th>
                                    <th class="col">Full Name</th>
                                    <th class="col">Contact Number</th>
                                    <th class="col">Action</th>
                                </tr>
                            </thead>

                            <tbody id="patientListTableBody">
                                <?php
                                $stmt = $conn->prepare("SELECT CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, 
                                pi.id AS ID, pi.contactno AS Contact, ar.id AS AppointmentID
                                FROM patient_info pi
                                LEFT OUTER JOIN appointment_requests ar
                                ON pi.id = ar.patient_id
                                GROUP BY pi.id, pi.fname, pi.mname, pi.lname, pi.contactno;");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $stmt->close();

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '
                                        <tr>
                                            <td id="patientID">' . $row['ID'] . '</td>
                                            <td id="patientName">' . $row['Name'] . '</td>
                                            <td id="patientContact">' .  $row['Contact'] . '</td>
                                            <td class="appointID">
                                            <button type="button" data-p-id="' . $row['ID'] . '" value="' . $row['AppointmentID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#transactionViewModal">View
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
    <script src="../../resources/js/functions.js" defer></script>

    <script>
        $(document).ready(function() {
            inputFilters();
            
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
            // var today = new Date();
            // var dd = String(today.getDate()).padStart(2, '0');
            // var mm = String(today.getMonth() + 1).padStart(2, '0');
            // var yyyy = today.getFullYear();

            // today = yyyy + '-' + mm + '-' + dd;

            loadTable();
            loadTransactionTable();            
            loadTransactionHistoryTable();

            $('body').on('click', '.viewAptDetail', function(){
                let patient_id = $(this).attr("data-p-id")
                loadTransactionList(patient_id);
            });

            $('body').on('click', '.viewTransDetail', function(){
                let aptId = $(this).attr("data-apt-id");
                loadTransactionDetails(aptId);
                loadTransactionHistory(aptId);
            });

            $('body').on('click', '.viewTransHistory', function(){
                let transId = $(this).attr("data-transaction-id");
                loadTransactionHistoryDetails(transId);
            });

            $('body').on('click', '#transactionCancelYesBtn, #transactionDetailsBackBtn', function(){
                $("#paymentType, #paymentRefNo").val("");
                $("#paymentRefDiv").addClass("d-none");
                $("#paymentRefDiv input").prop("disabled", true);
                resetAccordion();
            });            

            $('body').on('change', '#paymentType', function(){
                let paymentRefDiv = $("#paymentRefDiv");

                if ($(this).val() == 2) {
                    paymentRefDiv.removeClass("d-none");
                    paymentRefDiv.find("input").prop("disabled", false);
                } else {
                    paymentRefDiv.addClass("d-none");
                    paymentRefDiv.find("input").prop("disabled", true);
                }
            });            

            function resetAccordion() {
                $('.accordion-collapse.show').each(function () {
                    let collapseInstance = bootstrap.Collapse.getInstance(this) || new bootstrap.Collapse(this);
                    collapseInstance.hide();
                });
            }

            function loadTransactionHistoryDetails(transId) {
                var formData = {
                    transId: transId
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-transactions-history-details.php",
                    data: formData,
                    dataType: 'json'
                }).done(function(data) {
                    let transDetailFields = [
                        "#transHistoryName", "#transHistoryTransId", "#transHistoryAptId",
                        "#transHistoryProcedure", "#transHistoryAmountPaid", "#transHistoryRemaining",
                        "#transHistoryDatetime", "#transHistoryPaymentType", "#transHistoryPaymentRef", "#transHistoryProcessed"
                    ];

                    let transDetailValues = [
                        data.PatientName, data.TransactionID, data.AppointmentID, 
                        data.ProcedureName, data.AmountPaid, data.RemainingBalance, 
                        data.Timestamp, data.PaymentType, data.PaymentRef, data.SecretaryName
                    ];
                    
                    for (let index = 0; index < transDetailValues.length; index++) {
                        $(transDetailFields[index]).text(transDetailValues[index]);
                    }                    
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
            } 

            $("#myForm").submit(function(e){
                showLoader();
                e.preventDefault();
                $("#errorMessage, #addProcedureMessage, #viewProcedureMessage").empty();

                var url = $("#myForm").attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $("#myForm").serialize(),
                    dataType: "json"
                }).done(function (data) {
                    if (!data.success) {
                        hideLoader();
                        $("#transactionMessage").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $("#").append('<div class="mt-3 alert alert-danger alert-dismissible fade show">' + data.error +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        localStorage.setItem("errorMessage", data.message);
                        location.reload();
                    }
                    console.log(data);
                }).fail(function(data) {
                    console.log(data);
                });
            }); 
        
            if (localStorage.getItem("errorMessage")){
                let message = localStorage.getItem("errorMessage");

                $("#errorMessage").append('<div class="mt-3 alert alert-success  alert-dismissible fade show">' + message +  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                localStorage.removeItem("errorMessage")
            }; 

            $('#transactionHistoryViewItem').on('click', function () {
                $('#transactionHistoryTable').DataTable().columns.adjust();
            });    

            $('#transactionViewModal').on('shown.bs.modal', function () {
                $('#transactionsTable').DataTable().columns.adjust();
            });
    
            $('body').on("blur", ".onlyNumbersDots", function () {                
                let val = parseFloat(this.value);
                if (!isNaN(val)) {
                    this.value = val.toFixed(2);
                }
            });

            function loadTransactionDetails(aptId){
                var formData = {
                    aptId: aptId
                };

                $.ajax({
                    type: "POST",
                    url: "php/fetch-transactions-details.php",
                    data: formData,
                    dataType: 'json'
                }).done(function(data) {
                    let transDetailFields = [
                        "#transDetailPatientName", "#transDetailTreatmentID", "#transDetailAptID",
                        "#transDetailProcedure", "#transDetailProcessed", "#transDetailDateTime",
                        "#transDetailPaymentType", "#transDetailPaymentRef", "#transDetailAmountPaid"
                    ];

                    let transDetailValues = [
                        data.PatientName, data.TreatmentID, data.AppointmentID, data.ProcedureName, 
                        data.SecretaryName, data.Timestamp, data.PaymentType, data.PaymentRef, data.AmountPaid
                    ];
                    
                    for (let index = 0; index < transDetailValues.length; index++) {
                        $(transDetailFields[index]).text(transDetailValues[index]);
                    }

                    $("#transactionDetailsPid").val(data.PatientID);
                    $("#transactionDetailsAptId").val(data.AppointmentID);
                    
                    let tbodyHtml = "";

                    data.Procedures.forEach(proc => {
                        let prop;

                        if (proc.RemainingBalance == 0) {
                            prop = "disabled";
                        } else {
                            prop = "";
                        };

                        tbodyHtml += `
                            <tr>
                                <td>${proc.ProcedureName}<input ${prop} type="hidden" name="procedures_id[]" value="${proc.ProcedureID}"></td>
                                <td>${proc.TotalAmount}</td>
                                <td>${proc.AmountPaid}</td>
                                <td>${proc.RemainingBalance}<input ${prop} type="hidden" name="remaining_balance[]" value="${proc.RemainingBalance}"></td>
                                <td><input ${prop} required class="form-control text-center onlyNumbersDots" type="text" name="amount_paid[]"></td>
                            </tr>
                        `;
                    });

                    if (data.AppointStatus == 5) {                        
                        $("#paymentRefDiv, #paymentTypeDiv, #transactionDetailsSaveBtn, #transactionDetailsCancelBtn").addClass("d-none");
                        $("#transactionDetailsBackBtn").removeClass("d-none");
                        $("#paymentRefDiv, #paymentTypeDiv, #transactionDetailsSaveBtn").find("input").prop("disabled", true);
                    } else {
                        $("#paymentTypeDiv, #transactionDetailsSaveBtn, #transactionDetailsCancelBtn").removeClass("d-none");
                        $("#transactionDetailsBackBtn").addClass("d-none");
                        $("#paymentRefDiv, #paymentTypeDiv, #transactionDetailsSaveBtn").find("input").prop("disabled", false);
                    }

                    $('#transactionDetailsTableBody').html(tbodyHtml);
                    inputFilters();
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            function loadTransactionList(pid) {
                var formData = {
                    pid: pid
                };

                $.ajax({
                    type: "POST",
                    url: 'php/fetch-transactions.php',
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    $('#transactionsTable').DataTable().destroy().clear();
                    $('#transactionsTableBody').html(data);
                    loadTransactionTable(); 
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            function loadTransactionHistory(aptId) { 
                var formData = {
                    aptId: aptId
                };

                $.ajax({
                    type: "POST",
                    url: 'php/fetch-transaction-history.php',
                    data: formData,
                    dataType: 'json'
                }).done(function (data) {
                    $("#transactionHistoryTable").DataTable().destroy().clear();
                    $('#transactionHistoryTableBody').html(data);
                    loadTransactionHistoryTable();
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });
            }

            function loadTransactionHistoryTable() {                            
                let table = new DataTable('#transactionHistoryTable', {
                    select: false,
                    lengthMenu: [
                        [5, 10, 15, -1],
                        [5, 10, 15, 'All'],
                    ],
                    layout: {
                        top1: {
                        },
                        topStart: {
    
                        },
                        bottomStart: {
                            pageLength: true
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0,1,2,3,4,5],
                            className: 'dt-body-center dt-head-center'
                        }
                    ],
                    scrollY: '25vh',
                    scrollX: true,
                    scrollCollapse: true,
                    paging: true,                
                    autoWidth: false,
                    order: [[0, "desc"]]
                });
            }

            function loadTable() {
                let table = new DataTable('#patientListTable', {
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
                        top1: {
                            // searchPanes: {
                            //     initCollapsed: true,
                            // },
                            searchBuilder: {
                                // preDefined: {
                                //     criteria: [
                                //         {
                                //             data: 'Appointment Date',
                                //             condition: '=',
                                //             value: [today]
                                //             // value: ["2025-02-08"]
                                //         }
                                //     ]
                                // }
                            },
                        },
                        topStart: {
    
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
                    order: [[1, "asc"]],

                });
            }

            function loadTransactionTable() {                
                let table = new DataTable('#transactionsTable', {
                    select: false,
                    lengthMenu: [
                        [5, 10, 15, -1],
                        [5, 10, 15, 'All'],
                    ],
                    layout: {
                        top1: {
                        },
                        topStart: {
    
                        },
                        bottomStart: {
                            pageLength: true
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0,1,2,3],
                            className: 'dt-body-center dt-head-center'
                        }
                    ],
                    scrollY: '25vh',
                    scrollCollapse: true,
                    paging: true,                
                    autoWidth: false,
                    order: [[1, "desc"],[0, "desc"]],
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