<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

$data = "";
$items = "";
$pid;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'];

    $stmt = $conn->prepare("SELECT * FROM medical_history_logs
    WHERE patient_id = ?
    ORDER BY id DESC;");
    $stmt->bind_param('i', $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hasMedical = true;

        $counter = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $title = date('m/d/Y h:i:s A',  strtotime($row['timestamp']));
            $record = json_decode($row['remarks'], true);

            foreach ($record['items'] as $item) {
                if ($item['value'] !== "null") {
                    $items .= '
                    <tr>
                        <td>' . $item['desc'] . '</td>
                        <td>' . $item['value'] . '</td>
                    </tr>
                ';
                }
            }

            $data .=
            '<div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-' . $row["id"] . '" aria-expanded="false" aria-controls="flush-collapse-' . $row["id"] . '">
                        <span class="h6">' . $title . '</span>
                    </button>
                </h2>
                <div id="flush-collapse-' . $row["id"] . '" class="accordion-collapse collapse" data-bs-parent="#medicalHistoryLogsAcc">
                    <div class="accordion-body">
                        <div class="table-responsive" style="height: 300px;">
                            <table id="myTable" class="table">
                                <thead>
                                    <tr>
                                        <th class="col" colspan="1">Action Made: <span class="fw-normal">' . $record["type"] . '</span></th>
                                        <th class="col" colspan="2">Edit Timestamp: <span class="fw-normal">' . $title . '</span></th>
                                    </tr>
                                    <tr>
                                        <th class="col">Item</th>
                                        <th class="col">Value</th>
                                    </tr>
                                </thead>

                                <tbody id="tableBody">
                                ' . $items . '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>';
            
            $counter++;
        }
    } else {
        $data .= '
            <h5 class="fw-semibold">No Records</h5>
        ';
    }
}

echo json_encode($data);