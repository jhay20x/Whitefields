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
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionViewClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- <div class="ms-3">
                                <h6 class="fw-normal">No Pending Transactions</h6>                                
                            </div> -->
                            
                            <table id="transactionsTable" class="table-group-divider table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th class="col">Transaction ID</th> -->
                                        <th class="col">Appointment ID</th>
                                        <th class="col">Status</th>
                                        <th class="col-3">Date & Time</th>
                                        <th class="col">Action</th>
                                    </tr>
                                </thead>
    
                                <tbody id="transactionsTableBody">
                                </tbody>
                            </table>
                        </div>

                        <!-- <div class="accordion mt-3" id="transactionView">                            
                            <div id="transactionItem" class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" id="transactionViewBtn" type="button" data-bs-toggle="collapse" data-bs-target="#transactionInfo" aria-expanded="false" aria-controls="transactionInfo">
                                        <span class="h6">Transaction History</span>
                                    </button>
                                </h2>
                                <div id="transactionInfo" class="accordion-collapse collapse" data-bs-parent="#transactionView">
                                    <div class="accordion-body">
                                        <div class="col-12 col-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="submit" class="btn btn-sm btn-outline-success" name="profileSubmitBtn">Submit</button> -->
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
                            <i class="bi bi-clock-history"></i> Transactions
                        </h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionDetailsClose" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <h5>Transaction Details</h5>
                            
                            <div class="mt-3 row">
                                <div class="col-12 col-lg-6">
                                    <h6 class="h6">Patient Name: <span id="transDetailPatientName" class="fw-normal"></span></h6>
                                    <h6 class="h6">Transaction ID: <span id="transDetailTransID" class="fw-normal"></span></h6>
                                    <h6 class="h6">Appointment ID: <span id="transDetailAptID" class="fw-normal"></span></h6>
                                    <h6 class="h6">Processed By: <span id="transDetailProcessed" class="fw-normal"></span></h6>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <h6 class="h6">Date & Time: <span id="transDetailDateTime" class="fw-normal"></span></h6>
                                    <h6 class="h6">Payment Type: <span id="transDetailPaymentType" class="fw-normal"></span></h6>
                                    <h6 class="h6">Payment Reference No: <span id="transDetailPaymentRef" class="fw-normal"></span></h6>
                                </div>

                                <div class="col mt-3 table-responsive">
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
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-outline-success">Save</button>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transactionViewModal">Back</button>
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
        
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            loadTable();
            loadTransactionTable();            

            $('body').on('click', '.viewAptDetail', function(){
                let patient_id = $(this).attr("data-p-id")
                loadTransactionList(patient_id);
            });

            $('body').on('click', '.viewTransDetail', function(){
                let aptId = $(this).attr("data-apt-id");
                loadTransactionDetails(aptId);
            });

            // $("#transactionViewBackBtn").on("click", function() {
            //     resetAccordion();
            // });   

            // function resetAccordion() {
            //     $('.accordion-collapse.show').each(function () {
            //         let collapseInstance = bootstrap.Collapse.getInstance(this) || new bootstrap.Collapse(this);
            //         collapseInstance.hide();
            //     });
            // }        

            // $('#transactionItem').on('click', function () {
            //     $('#transactionsTable').DataTable().columns.adjust();
            // });    

            $('#transactionViewModal').on('shown.bs.modal', function () {
                $('#transactionsTable').DataTable().columns.adjust();
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
                        "#transDetailPatientName", "#transDetailTransID", "#transDetailAptID",
                        "#transDetailProcedure", "#transDetailProcessed", "#transDetailDateTime",
                        "#transDetailPaymentType", "#transDetailPaymentRef", "#transDetailAmountPaid"
                    ];

                    let transDetailValues = [
                        data.PatientName, data.TransactionID, data.AppointmentID, data.ProcedureName, 
                        data.SecretaryName, data.Timestamp, data.PaymentType, data.PaymentRef, data.AmountPaid
                    ];
                    
                    for (let index = 0; index < transDetailValues.length; index++) {
                        $(transDetailFields[index]).text(transDetailValues[index]);
                    }
                    
                    let tbodyHtml = "";

                    data.Procedures.forEach(proc => {
                        tbodyHtml += `
                            <tr>
                            <td>${proc.ProcedureName}</td>
                            <td>${proc.AmountPaid}</td>
                            <td>${proc.AmountPaid}</td>
                            <td>0.00</td>
                            <td><input type="text" name="" id=""></td>
                            </tr>
                        `;
                    });

                    $('#transactionDetailsTableBody').html(tbodyHtml);
                    // console.log(data);
                }).fail(function(data) {
                    // console.log(data);
                });

            }
            
            $('#transactionViewModal').modal('show');
            loadTransactionList(7);

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
                    order: [[0, "desc"]],
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