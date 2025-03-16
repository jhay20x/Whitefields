
                <!-- Notification -->
                <div class="dropdown me-3">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications">
                        <svg class="bi pe-none me-2" width="32" height="32"><use xlink:href="#bell"/></svg>

                        <?php                        
                        $host = 'localhost';
                        $username = 'root';
                        $password = '';
                        $database = 'u319950454_jj_pos';

                        $conn = new mysqli($host, $username, $password, $database);

                        // Query to fetch unread notifications count
                        $notifQuery = "SELECT * FROM `order_transaction` WHERE `notification_status` = 'unread' ORDER BY `create_at` DESC";
                        $notifStmt = $conn->prepare($notifQuery);
                        $notifStmt->execute();
                        $notifResult = $notifStmt->get_result();
                        $unreadCount = $notifResult->num_rows; // Count unread notifications
                        
                        if ($unreadCount > 0) {
                        ?>
                        <!-- Notification count badge -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $unreadCount; ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                        <?php 
                        }
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end overflow-auto" style="max-height: 300px;">
                    <li class="dropdown-header text-center text-uppercase" style="font-size: 12px; margin-bottom: -15px;margin-top: -15px;">Notifications</li>
                        <?php 
                        // Query to fetch all notifications, both read and unread
                        $allNotifQuery = "SELECT * FROM `order_transaction` ORDER BY `create_at` DESC";
                        $allNotifStmt = $conn->prepare($allNotifQuery);
                        $allNotifStmt->execute();
                        $allNotifResult = $allNotifStmt->get_result();

                        if ($allNotifResult->num_rows > 0) {
                            while ($row = $allNotifResult->fetch_assoc()) {
                                // Assuming create_at is formatted correctly
                                $formattedDate = htmlspecialchars(date('F j, Y, g:i a', strtotime($row['create_at'])));
                                $isUnread = $row['notification_status'] == 'unread';

                                ?>
                                <li><hr class="dropdown-divider"></li>
                                <a style="font-weight: <?= $isUnread ? 'bold' : 'normal'; ?>" 
                                class="dropdown-item" 
                                href="order-transaction.php?order_id=<?= htmlspecialchars($row['order_id']); ?>">
                                    <small><i><?= $formattedDate; ?></i></small><br/>
                                    <small><i><?= htmlspecialchars($row['order_type']); ?></i></small><br/>
                                    <?= htmlspecialchars($row['customer_name']); ?>
                                </a>
                                <?php 
                            }

                        } else {
                            echo "<li class='dropdown-item'>No Record yet.</li>";
                        }
                        ?>
                    </ul>
                </div>