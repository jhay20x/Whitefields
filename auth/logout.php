<?php 
// session_id("session-inv");
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username'])) {
    session_unset();
    session_destroy();
}

header("Location: ../login.php");