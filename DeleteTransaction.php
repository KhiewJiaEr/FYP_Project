<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "User") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];

    $transaction_id = $_GET['id'];

    $sql = "DELETE FROM `transaction_tbl` WHERE TransactionID =$transaction_id";
    $result =mysqli_query($dbconn, $sql);

    if($result){
        header("Location: HomeUser.php?msg=Transaction deleted successfully");
    }
    else{
        echo "Failed: " .mysqli_error($dbconn);
    }
?>  