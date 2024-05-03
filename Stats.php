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

    // Get the current date
    $currentDate = date('Y-m-d');

    // Calculate the start and end of the current month
    $firstDayOfMonth = date('Y-m-01', strtotime($currentDate));
    $lastDayOfMonth = date('Y-m-t', strtotime($currentDate));

    // Initialize selectedStartDate and selectedEndDate
    $selectedStartDate = $firstDayOfMonth;
    $selectedEndDate = $lastDayOfMonth;

    // Check if a date range has been selected (calendar)
    if (isset($_POST['daterange'])) {
        list($selectedStartDate, $selectedEndDate) = explode(' - ', $_POST['daterange']);

        // Format the dates in 'Y-m-d' format
        $selectedStartDate = date_create_from_format('Y-m-d', $selectedStartDate)->format('Y-m-d');
        $selectedEndDate = date_create_from_format('Y-m-d', $selectedEndDate)->format('Y-m-d');
    }

    // hidden input box to get selected date
    if (isset($_POST['selectedDateRange'])) {
        list($selectedStartDate, $selectedEndDate) = explode(' - ', $_POST['selectedDateRange']);
    
        // Format the dates in 'Y-m-d' format
        $selectedStartDate = date_create_from_format('Y-m-d', $selectedStartDate)->format('Y-m-d');
        $selectedEndDate = date_create_from_format('Y-m-d', $selectedEndDate)->format('Y-m-d');
    }

    // SQL query to fetch transaction data for income
    $sql_select_income = "SELECT category_tbl.CategoryType, SUM(transaction_tbl.Amount) AS TotalAmount
        FROM transaction_tbl
        INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
        WHERE transaction_tbl.UserID = $user_id
        AND DATE(transaction_tbl.DateTime) BETWEEN '$selectedStartDate' AND '$selectedEndDate'
        AND category_tbl.CategoryGroup = 'Income'
        GROUP BY category_tbl.CategoryType";

    // SQL query to fetch transaction data for expenses
    $sql_select_expense = "SELECT category_tbl.CategoryType, SUM(transaction_tbl.Amount) AS TotalAmount
        FROM transaction_tbl
        INNER JOIN category_tbl ON transaction_tbl.CategoryID = category_tbl.CategoryID
        WHERE transaction_tbl.UserID = $user_id
        AND DATE(transaction_tbl.DateTime) BETWEEN '$selectedStartDate' AND '$selectedEndDate'
        AND category_tbl.CategoryGroup = 'Expense'
        GROUP BY category_tbl.CategoryType";

    // Execute the SQL queries
    $result_select_income = mysqli_query($dbconn, $sql_select_income);
    $result_select_expense = mysqli_query($dbconn, $sql_select_expense);

    // Fetch the data for income and prepare it for the chart
    $income_data = array();
    while ($row = mysqli_fetch_assoc($result_select_income)) {
        $income_data[] = array('label' => $row['CategoryType'], 'value' => $row['TotalAmount']);
    }

    // Fetch the data for expenses and prepare it for the chart
    $expense_data = array();
    while ($row = mysqli_fetch_assoc($result_select_expense)) {
        $expense_data[] = array('label' => $row['CategoryType'], 'value' => $row['TotalAmount']);
    }
?>

<?php include 'MasterPage.php'; ?>

<main id="content" role="main" class="main">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Stats</h1>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <form action="" method="post">

            <div class="mb-3 mb-lg-5">
                <div class="row align-items-center">

                    <input type="hidden" id="selectedDateRange" name="selectedDateRange">

                    <div class="col">
                        <label class="form-label">Daily</label>
                        <div class="col">
                            <i id="left_caret_day" class="bi bi-caret-left" style="cursor: pointer;"></i>
                            <h4 class="page-header-title" style="display: inline;" id="day"></h4>
                            <i id="right_caret_day" class="bi bi-caret-right" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    <!-- End Col -->

                    <div class="col">
                        <label class="form-label">Weekly</label>
                        <div class="col">
                            <i id="left_caret_week" class="bi bi-caret-left" style="cursor: pointer;"></i>
                            <h4 class="page-header-title" style="display: inline;" id="week"></h4>
                            <i id="right_caret_week" class="bi bi-caret-right" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    <!-- End Col -->

                    <div class="col">
                        <label class="form-label">Monthly</label>
                        <div class="col">
                            <i id="left_caret_monthYear" class="bi bi-caret-left" style="cursor: pointer;"></i>
                            <h4 class="page-header-title" style="display: inline;" id="monthYear"></h4>
                            <i id="right_caret_monthYear" class="bi bi-caret-right" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    <!-- End Col -->

                    <div class="col">
                        <label class="form-label">Annual</label>
                        <div class="col">
                            <i id="left_caret_year" class="bi bi-caret-left" style="cursor: pointer;"></i>
                            <h4 class="page-header-title" style="display: inline;" id="year"></h4>
                            <i id="right_caret_year" class="bi bi-caret-right" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>

            <div class="mb-3 mb-lg-5">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <!-- Date Range Picker -->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="bi-calendar-week"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="js-daterangepicker-predefined" name="daterange" value="<?php echo htmlentities($selectedStartDate . ' - ' . $selectedEndDate, ENT_QUOTES); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 mb-lg-5">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Bar Chart for Income -->
                        <div class="card h-100">
                            <div class="card-body pt-0">
                                <!-- Header -->
                                <div class="row">
                                    <!-- Header -->
                                    <div class="card-header card-header-content-sm-between">
                                        <h4 class="card-header-title mb-2 mb-sm-0">Income</h4>
                                    </div>
                                    <!-- End Header -->
                                </div>
                                <!-- End Header -->

                                <!-- Create a canvas element for the income chart -->
                                <canvas id="IncomeBarChart"></canvas>
                            </div>
                        </div>
                        <!-- End Bar Chart for Income -->
                    </div>

                    <div class="col-lg-6">
                        <!-- Bar Chart for Expenses -->
                        <div class="card h-100">
                            <div class="card-body pt-0">
                                <!-- Header -->
                                <div class="row">
                                    <!-- Header -->
                                    <div class="card-header card-header-content-sm-between">
                                        <h4 class="card-header-title mb-2 mb-sm-0">Expenses</h4>
                                    </div>
                                    <!-- End Header -->
                                </div>
                                <!-- End Header -->

                                <!-- Create a canvas element for the expenses chart -->
                                <canvas id="ExpenseBarChart"></canvas>
                            </div>
                        </div>
                        <!-- End Bar Chart for Expenses -->
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- JS Plugins Init. -->
    <script>
        $(document).ready(function () {
            // Initialize the date range picker with a default date range for income and expense
            var selectedStartDate = moment(); // Initialize with the current date
            var selectedEndDate = moment();   // Initialize with the current date

            var dateRangePicker = $('#js-daterangepicker-predefined');
            dateRangePicker.daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'This Week': [moment().startOf('week'), moment().endOf('week')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')]
                }
            }, function (start, end) {
                // Update the selected start and end date variables for income and expense
                selectedStartDate = start;
                selectedEndDate = end;
            });

            // Trigger the event initially to set the input field with the default date range for income and expense
            dateRangePicker.trigger('apply.daterangepicker');


            // Update dateRangePicker when the dropdown selection changes
            $('#js-daterangepicker-predefined').on('apply.daterangepicker', function (ev, picker) {
                // Update the selected start and end date variables for income and expense
                selectedStartDate = picker.startDate;
                selectedEndDate = picker.endDate;

                // Update the input field with the selected date range
                dateRangePicker.val(selectedStartDate.format('YYYY-MM-DD') + ' - ' + selectedEndDate.format('YYYY-MM-DD'));

                // Set the hidden input field's value
                $('#selectedDateRange').val(dateRangePicker.val());

                // Submit the form to apply the date range for income and expense
                dateRangePicker.closest('form').submit();
            });

            // Get day, month and year by left and right caret icons
            const leftCaretDay = document.getElementById('left_caret_day');
            const rightCaretDay = document.getElementById('right_caret_day');
            const day = document.getElementById('day');

            // Get week by left and right caret icons
            const leftCaretWeek = document.getElementById('left_caret_week');
            const rightCaretWeek = document.getElementById('right_caret_week');
            const week = document.getElementById('week');

            // Get month and year by left and right caret icons
            const leftCaretMonthYear = document.getElementById('left_caret_monthYear');
            const rightCaretMonthYear = document.getElementById('right_caret_monthYear');
            const monthYear = document.getElementById('monthYear');

            // Get year by left and right caret icons
            const leftCaretYear = document.getElementById('left_caret_year');
            const rightCaretYear = document.getElementById('right_caret_year');
            const year = document.getElementById('year');

            
            // Function to display the current day, week, month and year in all the element above
            function displayCurrentDayWeekMonthYear(currentDate) {
                const day = currentDate.format('DD');
                const month = currentDate.format('MMM');
                const year = currentDate.format('YYYY');

                // Get the first day of the current week (assuming Sunday is the first day)
                const firstDayOfWeek = currentDate.clone().startOf('week').format('DD');
                
                // Get the last day of the current week (assuming Sunday is the first day)
                const lastDayOfWeek = currentDate.clone().endOf('week').format('DD');

                document.getElementById('day').textContent = day + ' ' + month + ' ' + year;
                document.getElementById('week').textContent = firstDayOfWeek + ' ' + month + ' ' + year + ' - ' + lastDayOfWeek + ' ' + month + ' ' + year;
                document.getElementById('monthYear').textContent = month + ' ' + year;
                document.getElementById('year').textContent = year;
            }


            // Call the function to display the current month and year initially
            displayCurrentDayWeekMonthYear(moment(selectedStartDate, 'YYYY-MM-DD'));


            // Event listener for the left caret icon
            leftCaretDay.addEventListener('click', function () {
                // Update the selected date by subtracting 1 day
                const startDate = moment(selectedStartDate, 'YYYY-MM-DD').subtract(1, 'days');
                const endDate = startDate.clone().endOf('day');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the day text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });

            // Event listener for the left caret icon
            leftCaretWeek.addEventListener('click', function () {
                // Update the selected date by subtracting 1 week
                const startDate = moment(selectedStartDate, 'YYYY-MM-DD').subtract(1, 'weeks').startOf('week');
                const endDate = startDate.clone().endOf('week');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the week text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });

            // Event listener for the left caret icon
            leftCaretMonthYear.addEventListener('click', function () {
                // Update the selected start and end dates to the previous month
                const startDate = moment(selectedStartDate, 'YYYY-MM-DD').subtract(1, 'months');
                const endDate = startDate.clone().endOf('month');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the month and year text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });

            // Event listener for the left caret icon
            leftCaretYear.addEventListener('click', function () {
                // Update the selected start and end dates to the previous year
                const startDate = moment(selectedStartDate, 'YYYY-MM-DD').subtract(1, 'years');
                const endDate = startDate.clone().endOf('years');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the year text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });


            // Event listener for the right caret icon
            rightCaretDay.addEventListener('click', function () {
                // Update the selected next day
                const startDate = moment(selectedEndDate, 'YYYY-MM-DD').add(1, 'days');
                const endDate = startDate.clone().endOf('day');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the day text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });

            // Event listener for the right caret icon
            rightCaretWeek.addEventListener('click', function () {
                // Update the selected next week
                const startDate = moment(selectedEndDate, 'YYYY-MM-DD').add(1, 'weeks').startOf('week');
                const endDate = startDate.clone().endOf('week');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the week text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });


            // Event listener for the right caret icon
            rightCaretMonthYear.addEventListener('click', function () {
                // Update the selected start and end dates to the next month
                const startDate = moment(selectedEndDate, 'YYYY-MM-DD').add(1, 'days');
                const endDate = startDate.clone().endOf('month');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the month and year text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });

            // Event listener for the right caret icon
            rightCaretYear.addEventListener('click', function () {
                // Update the selected start and end dates to the next year
                const startDate = moment(selectedEndDate, 'YYYY-MM-DD').add(1, 'days');
                const endDate = startDate.clone().endOf('year');

                selectedStartDate = startDate.format('YYYY-MM-DD');
                selectedEndDate = endDate.format('YYYY-MM-DD');

                // Update the year text
                displayCurrentDayWeekMonthYear(startDate);

                // Update the date range picker
                dateRangePicker.data('daterangepicker').setStartDate(selectedStartDate);
                dateRangePicker.data('daterangepicker').setEndDate(selectedEndDate);
                dateRangePicker.val(selectedStartDate + ' - ' + selectedEndDate);

                // Update the income chart
                updateIncomeChart(incomeChart);

                // Update the expenses chart
                updateExpenseChart(expenseChart);
            });
            

            // Function to update the income chart with new data
            function updateIncomeChart(chart) {
                $.ajax({
                    url: 'GetChartData.php', // Create a separate PHP file to fetch chart data
                    method: 'POST',
                    data: {
                        type: 'income',
                        selectedStartDate: selectedStartDate,
                        selectedEndDate: selectedEndDate
                    },
                    success: function (data) {
                        var newData = JSON.parse(data);
                        chart.data.labels = newData.map(data => data.label);
                        chart.data.datasets[0].data = newData.map(data => data.value);
                        chart.data.datasets[0].backgroundColor = newData.map(data => '#B6FFFA');
                        chart.data.datasets[0].borderColor = newData.map(data => '#687EFF');
                        chart.data.datasets[0].hoverBackgroundColor = newData.map(data => '#687EFF');
                        chart.update();
                    }
                });
            }

            // Function to update the expenses chart with new data
            function updateExpenseChart(chart) {
                $.ajax({
                    url: 'GetChartData.php', // Create a separate PHP file to fetch chart data
                    method: 'POST',
                    data: {
                        type: 'expense',
                        selectedStartDate: selectedStartDate,
                        selectedEndDate: selectedEndDate
                    },
                    success: function (data) {
                        var newData = JSON.parse(data);
                        chart.data.labels = newData.map(data => data.label);
                        chart.data.datasets[0].data = newData.map(data => data.value);
                        chart.data.datasets[0].backgroundColor = newData.map(data => '#FFB6B6');
                        chart.data.datasets[0].borderColor = newData.map(data => '#FF6666');
                        chart.data.datasets[0].hoverBackgroundColor = newData.map(data => '#FF6666');
                        chart.update();
                    }
                });
            }


            // Get the data from PHP for income 2D chart (2d)
            var ctxIncome = document.getElementById('IncomeBarChart').getContext('2d');
            var incomeChartData = <?php echo json_encode($income_data); ?>;

            // Initialize the income chart with data
            var incomeChart = new Chart(ctxIncome, {
                type: 'bar',
                data: {
                    labels: incomeChartData.map(data => data.label),
                    datasets: [{
                        label: 'Amount',
                        data: incomeChartData.map(data => data.value),
                        backgroundColor: incomeChartData.map(data => '#B6FFFA'),
                        borderColor: incomeChartData.map(data => '#687EFF'),
                        hoverBackgroundColor: incomeChartData.map(data => '#687EFF'),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            gridLines: {
                                color: '#e7eaf3',
                                drawBorder: false,
                                zeroLineColor: '#e7eaf3'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }]
                    }
                }
            });

            // Get the data from PHP for expenses 2D chart (2d)
            var ctxExpense = document.getElementById('ExpenseBarChart').getContext('2d');
            var expenseChartData = <?php echo json_encode($expense_data); ?>;

            // Initialize the expenses chart with data
            var expenseChart = new Chart(ctxExpense, {
                type: 'bar',
                data: {
                    labels: expenseChartData.map(data => data.label),
                    datasets: [{
                        label: 'Amount',
                        data: expenseChartData.map(data => data.value),
                        backgroundColor: expenseChartData.map(data => '#FFB6B6'),
                        borderColor: expenseChartData.map(data => '#FF6666'),
                        hoverBackgroundColor: expenseChartData.map(data => '#FF6666'),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            gridLines: {
                                color: '#e7eaf3',
                                drawBorder: false,
                                zeroLineColor: '#e7eaf3'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }]
                    }
                }
            });
        });
    </script>
</main>