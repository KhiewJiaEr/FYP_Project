<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];
    $select_user_sql = "SELECT * FROM user_tbl WHERE UserID = $user_id";
    $result_User = mysqli_query($dbconn, $select_user_sql);
    $row_User = mysqli_fetch_assoc($result_User);

    $select_user_num_sql = "SELECT COALESCE(COUNT(*), 0) as user_count FROM user_tbl WHERE UserRole = 'User'";
    $result_User_Num = mysqli_query($dbconn, $select_user_num_sql);
    if ($result_User_Num) {
        $row_User_Num = mysqli_fetch_assoc($result_User_Num);
        $user_count = $row_User_Num['user_count'];
    } else {
        echo "Error: " . mysqli_error($dbconn);
    }

    $sql_Added_Transaction = "SELECT COALESCE(COUNT(DISTINCT user_tbl.UserID), 0) as user_added_transaction_count 
        FROM transaction_tbl
        INNER JOIN user_tbl ON transaction_tbl.UserID = user_tbl.UserID
        WHERE UserRole = 'User'";
        $result_Added_Transaction = mysqli_query($dbconn, $sql_Added_Transaction);

    if ($result_Added_Transaction) {
        $row_Added_Transaction_Count = mysqli_fetch_assoc($result_Added_Transaction);
        $user_Added_Transaction_Count = $row_Added_Transaction_Count['user_added_transaction_count'];
    } else {
        echo "Error: " . mysqli_error($dbconn);
    }

    // Initialize default values for each category
    $defaultCategoryValues = array(
        'Food' => 0.00,
        'Daily Necessities' => 0.00,
        'Health' => 0.00,
        'Other Expense' => 0.00
    );

    // SQL query to fetch transaction data for specific categories
    $sql_select = "SELECT 
        CASE
            WHEN category_tbl.CategoryType = 'Food' THEN 'Food'
            WHEN category_tbl.CategoryType = 'Daily Necessities' THEN 'Daily Necessities'
            WHEN category_tbl.CategoryType = 'Health' THEN 'Health'
            WHEN category_tbl.CategoryType = 'Other Expense' THEN 'Other Expense'
        END AS Category,
        SUM(transaction_tbl.Amount) AS TotalAmount
        FROM transaction_tbl
        INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
        WHERE category_tbl.CategoryGroup = 'Expense'
        AND category_tbl.CategoryType IN ('Food', 'Daily Necessities', 'Health', 'Other Expense')
        GROUP BY Category";

    // Execute the SQL query
    $result_select = mysqli_query($dbconn, $sql_select);

    // Create an array to store the results
    $categoryTotals = $defaultCategoryValues; // Initialize with default values

    $totalAllExpenses = 0; // Initialize the totalAllExpenses variable

    // Process the results and store them in the array
    while ($row = mysqli_fetch_assoc($result_select)) {
        $category = $row['Category'];
        $totalAmount = $row['TotalAmount'];

        // Store the results in the array
        $categoryTotals[$category] = $totalAmount;

        // Calculate the total of all expenses
        $totalAllExpenses += $totalAmount;
    }

    $currencyId = $row_User['CurrencyID'];
    switch ($currencyId) {
        case '1':
            $currencySymbol = '$'; // US Dollar
            break;
        case '2':
            $currencySymbol = '€'; // Euro
            break;
        case '3':
            $currencySymbol = '£'; // British Pound
            break;
        case '4':
            $currencySymbol = '¥'; // Japanese Yen
            break;
        case '5':
            $currencySymbol = 'RM'; // Malaysian Ringgit
            break;
        default:
            $currencySymbol = ''; // Default value if the CurrencyID is not recognized
            break;
    }
?>

<?php include 'MasterPage.php';?>

<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Welcome back, Admin <?php echo htmlentities($row_User['Username']) ?></h1>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <form action="" method="post">
             <!-- Expenses Category -->
             <div class="row">
                <div class="col-sm-6 col-lg-6 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">Total No. Of User</h2>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <h2 class="card-title text-inherit"><i class="bi bi-person"></i></h2>
                                </div>
                                <div class="ps-3">
                                    <h2 class="card-title text-inherit"><?php echo $user_count ?></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>

                <div class="col-sm-6 col-lg-6 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">No. Of User Added Transaction</h2>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <h2 class="card-title text-inherit"><i class="bi bi-person-check"></i></h2>
                                </div>
                                <div class="ps-3">
                                    <h2 class="card-title text-inherit"><?php echo $user_Added_Transaction_Count ?></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>
            </div>
            <!-- End Expenses Category -->

            <!-- Expenses Category -->
            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">Food</h2>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <h2 class="card-title text-inherit"><?php echo $currencySymbol ?></h2>
                                </div>
                                <div class="ps-3">
                                    <h2 class="card-title text-inherit"><?php echo number_format($categoryTotals['Food'], 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>

                <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">Daily Necessities</h2>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <h2 class="card-title text-inherit"><?php echo $currencySymbol ?></h2>
                                </div>
                                <div class="ps-3">
                                    <h2 class="card-title text-inherit"><?php echo number_format($categoryTotals['Daily Necessities'], 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>

                <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">Health</h2>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <h2 class="card-title text-inherit"><?php echo $currencySymbol ?></h2>
                                </div>
                                <div class="ps-3">
                                    <h2 class="card-title text-inherit"><?php echo number_format($categoryTotals['Health'], 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>

                <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                    <!-- Card -->
                    <a class="card card-hover-shadow h-100" href="#">
                        <div class="card-body">
                            <h2 class="card-title">Other Expense</h2>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <h2 class="card-title text-inherit"><?php echo $currencySymbol ?></h2>
                                    </div>
                                    <div class="ps-3">
                                        <h2 class="card-title text-inherit"><?php echo number_format($categoryTotals['Other Expense'], 2); ?></h2>
                                    </div>
                                </div>
                        </div>
                    </a>
                    <!-- End Card -->
                </div>
            </div>
            <!-- End Expenses Category -->

            <!-- Card -->
            <div class="mb-3 mb-lg-5">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header card-header-content-between">
                        <h4 class="card-header-title">Total Expenses</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <div class="h1 d-block mb-4">
                            <?php
                                echo $currencySymbol . number_format($categoryTotals['Food'], 2) . ' + ' . $currencySymbol . number_format($categoryTotals['Daily Necessities'], 2) . ' + ' . $currencySymbol . number_format($categoryTotals['Health'], 2) . ' + ' . $currencySymbol . number_format($categoryTotals['Other Expense'], 2) . ' = ' . $currencySymbol . number_format($totalAllExpenses, 2); // Formatting with 2 decimal places
                            ?>
                        </div>

                        <p>Food + Daily Necessities + Health + Other Expense = Total Expenses</p>
                    </div>
                    <!-- End Body -->
                </div>
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="mb-3 mb-lg-5">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header card-header-content-between">
                        <h4 class="card-header-title">Expenses Category</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    
                        <!-- Create a pie chart -->
                        <div class="chartjs-custom mx-auto">
                            <div id="piechart"></div>
                        </div>
                    
                    <!-- End Body -->
                </div>
            </div>
            <!-- End Card -->
        </form>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'TotalAmount');
            data.addRows([
                <?php
                    // Prepare data for the chart
                    $data = array();
                    foreach ($categoryTotals as $category => $totalAmount) {
                        $data[] = "['" . $category . "', " . $totalAmount . "]";
                    }
                    echo implode(", ", $data);
                ?>
            ]);

            var options = {
                width: 500,
                height: 500
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>

</main>