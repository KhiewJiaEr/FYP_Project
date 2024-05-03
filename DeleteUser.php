<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];

    $delete_user_id = $_GET['id'];

    $sql = "DELETE FROM `user_tbl` WHERE UserID =$delete_user_id";
    $result =mysqli_query($dbconn, $sql);

    if($result){
        header("Location: ManageUser.php?msg=User deleted successfully");
    }
    else{
        echo "Failed: " .mysqli_error($dbconn);
    }
?>  