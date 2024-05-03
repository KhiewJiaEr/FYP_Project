<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "User") {
        header("Location: Login.php");
        exit;
    }

    $transaction_id = $_GET['id'];
    $user_id = $_SESSION["userid"];
    $select_user_sql = "SELECT * FROM user_tbl WHERE UserID = $user_id";
    $result_User = mysqli_query($dbconn, $select_user_sql);
    $row_User = mysqli_fetch_assoc($result_User);

    $error = "";

    if (isset($_POST['submit'])) {
        $category_id = $_POST['CategoryID'];
        $amount = floatval($_POST['Amount']);
        $note = mysqli_real_escape_string($dbconn, $_POST["Note"]);
        $date_time = $_POST['DateTime'];

        if (empty($category_id) || empty($amount) || empty($date_time)) {
            $error = "*Please fill in all the required fields.";
        } else {
            $sql = "INSERT INTO `transaction_tbl`(`UserID`, `CategoryID`, `Amount`, `Note`, `DateTime`) 
                    VALUES ('$user_id','$category_id','$amount','$note','$date_time')";

            $result = mysqli_query($dbconn, $sql);

            if ($result) {
                header("Location: HomeUser.php");
                exit;
            } else {
                $error = "Error inserting data into the database: " . mysqli_error($dbconn);
            }
        }
    }

    if (isset($_POST['submit_edit'])) {
        $category_id = $_POST['CategoryID'];
        $amount = floatval($_POST['Amount']);
        $note = mysqli_real_escape_string($dbconn, $_POST["Note"]);
        $date_time = $_POST['DateTime'];

        if (empty($category_id) || empty($amount) || empty($date_time)) {
            $error = "*Please fill in all the required fields.";
        } else {
            $sql = "UPDATE `transaction_tbl` SET `CategoryID`='$category_id',`Amount`='$amount',`Note`='$note',`DateTime`='$date_time'
            WHERE TransactionID = $transaction_id";
            $result = mysqli_query($dbconn, $sql);

            if ($result) {
                header("Location: HomeUser.php");
                exit;
            } else {
                $error = "Error updating data into the database: " . mysqli_error($dbconn);
            }
        }
    }

    $get_transaction_sql = "SELECT transaction_tbl.TransactionID, category_tbl.CategoryID, transaction_tbl.Amount, transaction_tbl.Note, transaction_tbl.DateTime from transaction_tbl
                            INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
                            WHERE TransactionID = $transaction_id LIMIT 1";
    $transaction_result = mysqli_query($dbconn, $get_transaction_sql);
    $transaction_row = mysqli_fetch_assoc($transaction_result);

    // Determine the initial transaction type based on $transaction_row['CategoryID']
    $initialTransactionType = ($transaction_row['CategoryID'] >= 1 && $transaction_row['CategoryID'] <= 3) ? 'income' : 'expense';
?>

<?php include 'MasterPage.php'; ?>
<main id="content" role="main" class="main">
    <div>
        <?php
            if ($error){
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                '.$error.'
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>';
            }

            $sql_check_id = "SELECT * FROM transaction_tbl WHERE TransactionID = $transaction_id LIMIT 1";
            $result_check_id = mysqli_query($dbconn, $sql_check_id);  
            $row_check_id = mysqli_fetch_assoc($result_check_id);

            if ($user_id != $row_check_id['UserID']){
                echo "<script>alert('Invalid user access!'); window.location.href='HomeUser.php';</script>";
            }
        ?>
    </div>
    
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Edit Transaction</h1>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <div class="row">
                            <input type="hidden" name="TransactionType" id="TransactionType" value="income">
                            <div class="d-grid gap-2 col-6 mx-auto">
                                <button type="button" class="btn btn-outline-primary <?php if ($initialTransactionType === 'income') echo 'active'; ?>" id="IncomeButton">Income</button>
                            </div>
                            <div class="d-grid gap-2 col-6 mx-auto">
                                <button type="button" class="btn btn-outline-primary <?php if ($initialTransactionType === 'expense') echo 'active'; ?>" id="ExpenseButton">Expense</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="CategoryID" class="form-label">Category Type*</label>
                            <select name="CategoryID" id="CategoryID" class="form-control" required>
                                <!-- Options will be generated by JavaScript -->
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Amount*</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <?php
                                        $currencyId = $row_User['CurrencyID'];
                                        switch ($currencyId) {
                                            case '1':
                                                echo '$'; // US Dollar
                                                break;
                                            case '2':
                                                echo '€'; // Euro
                                                break;
                                            case '3':
                                                echo '£'; // British Pound
                                                break;
                                            case '4':
                                                echo '¥'; // Japanese Yen
                                                break;
                                            case '5':
                                                echo 'RM'; // Malaysian Ringgit
                                                break;
                                            default:
                                                echo ''; // Default value if the CurrencyID is not recognized
                                                break;
                                        }
                                    ?>
                                </span>
                                <input type="number" step="0.01" name="Amount" class="form-control" placeholder="0.00" value="<?php echo htmlentities($transaction_row['Amount']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <input type="text" name="Note" class="form-control" placeholder="Anything to Note?" value="<?php echo htmlentities($transaction_row['Note']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Time*</label>
                        <input type="text" class="form-control" id="datetimePicker" name="DateTime" placeholder="Select date and time" value="<?php echo htmlentities($transaction_row['DateTime']) ?>" required>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="d-grid gap-2 col-3 mx-auto">
                            <button type="submit" class="btn btn-primary" name="submit">Save</button>
                        </div>
                        <div class="d-grid gap-2 col-3 mx-auto">
                            <button type="submit" class="btn btn-success" name="submit_edit">Edit</button>
                        </div>
                        <div class="d-grid gap-2 col-3 mx-auto">
                            <a href="HomeUser.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Flatpickr
        flatpickr("#datetimePicker", {
            enableTime: true, // Enable time selection
            dateFormat: "Y-m-d H:i", // Format for date and time
            time_24hr: true, // Use 24-hour time format
            placeholder: "Select date and time", // Placeholder text
        });
    </script>

    <script>
        // Get references to form elements
        const categorySelect = document.getElementById('CategoryID'); // Dropdown box id
        const incomeButton = document.getElementById('IncomeButton');
        const expenseButton = document.getElementById('ExpenseButton');
        const transactionTypeInput = document.getElementById('TransactionType'); // A hidden input to change value (income/expenses)

        // Function to update category options based on transaction type
        function updateCategoryOptions(transactionType) {
            // Clear existing options
            categorySelect.innerHTML = '';

            // Create new options based on transaction type
            const options = {
                income: [
                    { value: '', label: '== Please Select ==' },
                    { value: '1', label: 'Allowance' },
                    { value: '2', label: 'Salary' },
                    { value: '3', label: 'Other Income' }
                ],
                expense: [
                    { value: '', label: '== Please Select ==' },
                    { value: '4', label: 'Food' },
                    { value: '5', label: 'Daily Necessities' },
                    { value: '6', label: 'Health' },
                    { value: '7', label: 'Other Expense' }
                ]
            };

            options[transactionType].forEach(option => {
                const newOption = document.createElement('option');
                newOption.value = option.value; // Dropdown box value change
                newOption.text = option.label; // Dropdown box label change
                categorySelect.appendChild(newOption);
            });

            categorySelect.value = "<?php echo $transaction_row['CategoryID']; ?>";
        }

        // Determine the initial state of the buttons and select box
        if ("<?php echo $initialTransactionType; ?>" === 'income') {
            incomeButton.classList.add('active');
            expenseButton.classList.remove('active');
            transactionTypeInput.value = 'income';
            updateCategoryOptions('income');
        } else {
            incomeButton.classList.remove('active');
            expenseButton.classList.add('active');
            transactionTypeInput.value = 'expense';
            updateCategoryOptions('expense');
        }

        // Event listener for income button
        incomeButton.addEventListener('click', function () {
            transactionTypeInput.value = 'income';
            updateCategoryOptions('income');
            incomeButton.classList.add('active');
            expenseButton.classList.remove('active');
        });

        // Event listener for expense button
        expenseButton.addEventListener('click', function () {
            transactionTypeInput.value = 'expense';
            updateCategoryOptions('expense');
            expenseButton.classList.add('active');
            incomeButton.classList.remove('active');
        });
    </script>
</main>