<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "User") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];
    $select_user_sql = "SELECT * FROM user_tbl WHERE UserID = $user_id";
    $result_User = mysqli_query($dbconn, $select_user_sql);
    $row_User = mysqli_fetch_assoc($result_User);

    $errorAlert = "";

    if (isset($_POST['submit'])) {
        $investing_process = mysqli_real_escape_string($dbconn, $_POST["expression"]);
        $investing_output = mysqli_real_escape_string($dbconn, $_POST["SaveInvesting"]);

        if (empty($investing_process) || empty($investing_output) || $investing_output == "Invalid Input" || $investing_output == "No Input") {
            $errorAlert .= '<h5>*Please fill in a valid calculation.</h5>
                            <br/>';
        } else {
            $investing_sql = "INSERT INTO `investing_tbl`(`UserID`, `InvestingProcess`, `InvestingResult`) 
                                    VALUES ('$user_id','$investing_process','$investing_output')";

            $investing_result = mysqli_query($dbconn, $investing_sql);
        }
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

<?php include 'MasterPage.php'; ?>
<main id="content" role="main" class="main">
    <div class="content container-fluid">
         <!-- Page Header -->
         <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Account Investing</h1>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <form action="" method="post">
            <div class="mb-3 mb-lg-5">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Card -->
                        <div class="card h-100">
                            <!-- Header -->
                            <div class="card-header card-header-content-between">
                                <h4 class="card-header-title">Total Assets</h4>
                            </div>
                            <!-- End Header -->

                            <!-- Body -->
                            <div class="card-body">
                                <div class="h1 d-block mb-4">
                                    <?php
                                        $sql_select_total = "SELECT 
                                            SUM(CASE WHEN category_tbl.CategoryGroup = 'Income' THEN transaction_tbl.Amount ELSE 0 END) AS TotalIncome,
                                            SUM(CASE WHEN category_tbl.CategoryGroup = 'Expense' THEN transaction_tbl.Amount ELSE 0 END) AS TotalExpense,
                                            SUM(CASE WHEN category_tbl.CategoryGroup = 'Income' THEN transaction_tbl.Amount ELSE 0 END) -
                                            SUM(CASE WHEN category_tbl.CategoryGroup = 'Expense' THEN transaction_tbl.Amount ELSE 0 END) AS IncomeMinusExpense
                                        FROM transaction_tbl
                                        INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
                                        WHERE transaction_tbl.UserID = $user_id";

                                        $result_total = mysqli_query($dbconn, $sql_select_total);
                                        $row_total = mysqli_fetch_assoc($result_total);

                                        $TotalIncome = $row_total['TotalIncome'];
                                        $TotalExpense = $row_total['TotalExpense'];
                                        $incomeMinusExpense = $row_total['IncomeMinusExpense'];

                                        echo $currencySymbol . number_format($TotalIncome, 2) . ' - ' . $currencySymbol . number_format($TotalExpense, 2) . ' = ' . $currencySymbol . number_format($incomeMinusExpense, 2);
                                    ?>
                                </div>

                                <p>Total Income - Total Expense = Total Assets</p>

                                <!-- Table -->
                                <div class="table-responsive datatable-custom">
                                    <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                                    "columnDefs": [{"targets": [0,2],"orderable": false}],
                                    "order": [],
                                    "pageLength": 20,
                                    "isResponsive": false,
                                    "isShowPaging": false
                                    }'
                                    >

                                        <thead class="thead-light">
                                            <tr>
                                                <th>Category Type</th>
                                                <th>Category Group</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $sql_select = "SELECT category_tbl.CategoryType, category_tbl.CategoryGroup, SUM(transaction_tbl.Amount) AS TotalAmount
                                                FROM transaction_tbl
                                                INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
                                                WHERE transaction_tbl.UserID = $user_id
                                                GROUP BY category_tbl.CategoryType, category_tbl.CategoryGroup
                                                ORDER BY category_tbl.CategoryGroup DESC";

                                                $transaction_no = mysqli_query($dbconn,$sql_select);  
                                                if(mysqli_num_rows($transaction_no) >0){
                                                    foreach($transaction_no as $row){
                                            ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($row['CategoryType'])?></td>
                                                            <td><?php echo $row['CategoryGroup']?></td>
                                                            <td><?php echo htmlentities($row['TotalAmount'])?></td>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End Table -->
                            </div>
                            <!-- End Body -->
                        </div>
                        <!-- End Card -->
                    </div>    

                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <!-- Card -->
                        <div class="card h-100">
                            <!-- Header -->
                            <div class="card-header card-header-content-sm-between">
                                <h4 class="card-header-title mb-2 mb-sm-0">Calculate Investing</h4>
                            </div>
                            <!-- End Header -->

                            <!-- Body -->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Calculator</label>
                                    <input type="text" name="expression" class="form-control" id="expressionInput" placeholder="e.g., 100*10%" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Result</label>
                                    <input type="text" name="SaveInvesting" class="form-control" id="investingCalculator" value="No Input" readonly>
                                </div>

                                <h><?= $errorAlert ?></h>

                                <div class="mb-3">
                                    <div class="row">
                                        <div class="d-grid gap-2 col-3 mx-auto">
                                            <button type="submit" class="btn btn-primary" name="submit">Save</button>
                                        </div>
                                        <div class="d-grid gap-2 col-3 mx-auto">
                                            <button type="button" class="btn btn-danger" onclick="clearInput()">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Body -->
                        </div>
                        <!-- End Card -->
                    </div>
                </div>
            </div>
            <div class="mb-3 mb-lg-5">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header card-header-content-between">
                        <h4 class="card-header-title">Calculator History</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <div class="mb-3">
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModalToggle"><i class="bi bi-trash"></i> Delete All</button>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <?php
                                    $sql_select_investing = "SELECT * FROM investing_tbl WHERE UserID = $user_id";
                                    $investing_no = mysqli_query($dbconn, $sql_select_investing);
                                    if (mysqli_num_rows($investing_no) > 0) {
                                        foreach ($investing_no as $row) {
                                ?>
                                    <div class="col-10">
                                        <a class="list-group-item list-group-item-action list-item" value="<?php echo $row['InvestingProcess'] ?>" onmouseover="this.style.backgroundColor='#B6FFFA';" onmouseout="this.style.backgroundColor='';"><?php echo $row['InvestingResult'] ?></a>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModalToggle<?php echo $row['InvestingID'] ?>"><i class="bi bi-trash"></i> Delete</button>
                                    </div>
                                <?php
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </form>
    </div>

    <!-- Delete Modal -->
    <?php
        $sql_select_investing = "SELECT * FROM investing_tbl WHERE UserID = $user_id";
        $investing_no = mysqli_query($dbconn, $sql_select_investing);
        if (mysqli_num_rows($investing_no) > 0) {
            foreach ($investing_no as $row) {
    ?>
            <div class="modal fade" id="deleteModalToggle<?php echo $row['InvestingID'] ?>" aria-hidden="true" aria-labelledby="deleteModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deleteModalToggleLabel">Delete Calculator History</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this?</p>
                            <p><?php echo $row['InvestingResult'] ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger" onclick="confirmDelete(<?php echo $row['InvestingID'] ?>)">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
    <?php
            }
        }
    ?>

    <!-- Delete All Modal -->
    <div class="modal fade" id="deleteAllModalToggle" aria-hidden="true" aria-labelledby="deleteAllModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteAllModalToggleLabel">Delete All Calculator History</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete All of the Calculator History?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="confirmDeleteAll()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Plugins Init. -->
    <script>
        // INITIALIZATION OF DATATABLES
        // =======================================================
        HSCore.components.HSDatatables.init($("#datatable"), {
            select: {
                style: "multi",
                selector: 'td:first-child input[type="checkbox"]',
                classMap: {
                    checkAll: "#datatableCheckAll"
                },
            },
            language: {
                zeroRecords: `<div class="text-center p-4">
              <img class="mb-3" src="./assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
              <img class="mb-3" src="./assets/svg/illustrations/oc-error-light.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
            <p class="mb-0">No data to show</p>
            </div>`,
            },
        });

        const datatable = HSCore.components.HSDatatables.getItem(0);

        document.querySelectorAll(".js-datatable-filter").forEach(function(item) {
            item.addEventListener("change", function(e) {
                const elVal = e.target.value,
                    targetColumnIndex = e.target.getAttribute("data-target-column-index"),
                    targetTable = e.target.getAttribute("data-target-table");

                HSCore.components.HSDatatables.getItem(targetTable)
                    .column(targetColumnIndex)
                    .search(elVal !== "null" ? elVal : "")
                    .draw();
            });
        });

    </script>

    <script>
        var inputField = document.getElementById("expressionInput");
        var investingCalculator = document.getElementById("investingCalculator");

        inputField.addEventListener("input", function () {
            var calculatorInput = this.value;
            if (calculatorInput.trim() === "") {
                // No input provided
                investingCalculator.value = "No Input";
                return;
            }

            var sanitizedCalculatorInput = calculatorInput.replace(/[^0-9+\-*/%.()]/g, '');

            try {
                sanitizedCalculatorInput = sanitizedCalculatorInput.replace(/(\d+)%/, "($1/100)");
                var result = evaluateCalculatorInput(sanitizedCalculatorInput);
                if (isFinite(result)) {
                    result = result.toFixed(2); // Limit result to 2 decimal places
                    investingCalculator.value = calculatorInput + " = " + result;
                } else {
                    investingCalculator.value = "Invalid Input";
                }
            } catch (error) {
                investingCalculator.value = "Invalid Input";
            }
        });

        function evaluateCalculatorInput(calculatorInput) {
            try {
                return eval(calculatorInput);
            } catch (error) {
                return NaN; // Handle invalid Input
            }
        }
    </script>

    <script>
        function clearInput() {
            var expressionInput = document.getElementById("expressionInput");
            expressionInput.value = ""; // Clear the input field
            var investingCalculator = document.getElementById("investingCalculator");
            investingCalculator.value = "No Input"; // Clear the result field
        }

        function confirmDelete(InvestingID) {
            window.location.href = "DeleteInvesting.php?id=" + InvestingID;
        }

        function confirmDeleteAll() {
            window.location.href = "DeleteAllInvesting.php";
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const listItems = document.querySelectorAll(".list-group-item");
            const expressionInput = document.getElementById("expressionInput");
            const SaveInvestingInput = document.getElementById("investingCalculator"); //get id to show result

            listItems.forEach(function (item) {
                item.addEventListener("click", function () {
                    const expression = this.getAttribute("value"); //Only the process of the Calculation (No result)
                    const result = this.textContent; // Use textContent to get the text of the element (Process and result)
                    try {
                        expressionInput.value = expression;
                        SaveInvestingInput.value = result;
                    } catch (error) {
                        expressionInput.value = "Invalid Input";
                        SaveInvestingInput.value = "Invalid Input";
                    }
                });
            });
        });
    </script>

</main>