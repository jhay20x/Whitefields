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
        <title>Reports - Whitefields Dental Clinic</title>
        <link rel="stylesheet" href="../../resources/css/bootstrap.css">
        <link rel="stylesheet" href="../../resources/css/sidebar.css">
        <link rel="stylesheet" href="../../resources/css/loader.css">
        <link rel="stylesheet" href="../../resources/css/jquery-ui.css">
        <link rel="stylesheet" href="../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../../resources/css/dataTables.bootstrap5.css">
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

        <div class="container-fluid">
            <div class="row d-flex justify-content-center position-relative">
                <div class="title position-sticky top-0 start-0 z-3 bg-white d-flex flex-row shadow align-items-center p-3">
                    <button id="" class="sidebarCollapse btn btn-outline-secondary me-3 position-relative">
                        <span class="position-absolute <?php echo $hasId ? 'visually-hidden' : ''; ?> top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <i class="bi bi-list"></i>
                    </button>
                    <h1><i class="bi bi-file-earmark-spreadsheet"></i></h1>
                    <h1 class="col ms-3">Reports</h1>

                    <?php include "../../components/notification.php" ?>
                </div>

                <div class="col-md-9 my-3 rounded shadow bg-white row">
                    <div class="my-3">
                        <div class="col">
                            <h3>Transaction Reports</h3>
                            <span>View and manage all of the client's pending and completed transactions.</span>
                        </div>

                        <div id="errorMessage" class="" role="alert"></div>
                        
                        <table id="reportsListTable" class="table-group-divider table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient Name</th>
                                    <th>Procedure</th>
                                    <th>Amount Paid</th>
                                    <th>Date</th>
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody id="reportsListTableBody">
                            <?php
                                $stmt = $conn->prepare("SELECT tr.*, pr.name,
                                    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
                                    CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS PatientName
                                    FROM transactions tr 
                                    LEFT OUTER JOIN procedures pr ON pr.id = tr.procedures_id
                                    LEFT OUTER JOIN patient_info pi ON pi.id = tr.patient_id
                                    ORDER BY tr.id DESC;");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $stmt->close();

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $datetime = date("Y-m-d", strtotime($row['timestamp']));
                                        echo '
                                        <tr>
                                            <td id="transactionHistoryAptID">' . $row['id'] . '</td>
                                            <td id="transactionHistoryAptName">' . $row['PatientName'] . '</td>
                                            <td id="transactionHistoryProcedure">' . $row['name'] . '</td>
                                            <td id="transactionHistoryAmountPaid">' . $row['amount_paid'] . '</td>
                                            <td id="transactionHistoryTimestamp">' . $datetime . '</td>
                                        </tr>
                                        ';
                                            // <td class="transactionHistoryAction">
                                            //     <button type="button" data-transaction-id="' . $row['id'] . '" class="btn btn-sm btn-outline-primary viewTransHistory" data-bs-toggle="modal" data-bs-target="#transactionHistoryModal">View</button>
                                            // </td>
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
    <script src="../../resources/js/buttons.print.min.js"></script>
    <script src="../../resources/js/functions.js" defer></script>

    <script>
        $(document).ready(function() {
        
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            loadTable();            
            
            function loadTable() {
                let table = new DataTable('#reportsListTable', {
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
                                preDefined: {
                                    criteria: [
                                        {
                                            data: 'Date',
                                            condition: '=',
                                            value: [today]
                                        }
                                    ]
                                }
                            },
                        },
                        topStart: {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    footer: true,
                                    customize: function (win) {
                                        let total = 0;
                                        $('#reportsListTable').DataTable().column(3, { page: 'current' }).data().each(function (value) {
                                            total += parseFloat(value.toString().replace(/[^0-9.-]+/g, '')) || 0;
                                        });

                                        $(win.document.body).find('table').append(
                                            `<tfoot>
                                                <tr>
                                                    <th colspan="4" style="text-align:right">Total:</th>
                                                    <th>${total.toFixed(2)}</th>
                                                </tr>
                                            </tfoot>`
                                        );
                                    }
                                }
                            ]
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