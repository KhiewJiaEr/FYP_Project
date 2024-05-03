<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "User") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if type and selectedStartDate/selectedEndDate parameters are set
        if (isset($_POST['type'], $_POST['selectedStartDate'], $_POST['selectedEndDate'])) {
            $type = $_POST['type'];
            $selectedStartDate = $_POST['selectedStartDate'];
            $selectedEndDate = $_POST['selectedEndDate'];

            $user_id = $_SESSION["userid"];

            // SQL query to fetch transaction data based on the type (income or expense)
            $sql_select_data = "SELECT category_tbl.CategoryType, SUM(transaction_tbl.Amount) AS TotalAmount
                FROM transaction_tbl
                INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
                WHERE transaction_tbl.UserID = $user_id
                AND DATE(transaction_tbl.DateTime) BETWEEN '$selectedStartDate' AND '$selectedEndDate'
                AND category_tbl.CategoryGroup = '$type'
                GROUP BY category_tbl.CategoryType";

            $result_select_data = mysqli_query($dbconn, $sql_select_data);

            // Fetch the data and prepare it for the chart
            $chartData = array();
            while ($row = mysqli_fetch_assoc($result_select_data)) {
                $chartData[] = array('label' => $row['CategoryType'], 'value' => $row['TotalAmount']);
            }

            // Return the chart data as JSON
            echo json_encode($chartData);
        }
    }
?>  