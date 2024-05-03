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
?>

<?php include 'MasterPage.php';?>

<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Manage User</h1>
                </div>
                <!-- End Col -->

                <div class="col">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a class="btn btn-primary" href="AddUser.php"><i class="bi bi-plus-circle"></i> Add User</a>
                    </div>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    <div class="row justify-content-between align-items-center flex-grow-1">
                        <div class="col-9 col-md">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-header-title">Manage User</h5>
                            </div>
                        </div>

                        <div class="col-auto">
                            <!-- Filter -->
                            <form>
                                <!-- Search -->
                                <div class="input-group input-group-merge input-group-flush">
                                    <div class="input-group-prepend input-group-text">
                                        <i class="bi-search"></i>
                                    </div>
                                    <input id="datatableWithSearchInput" type="search" class="form-control" placeholder="Search" aria-label="Search users">
                                </div>
                                <!-- End Search -->
                            </form>
                            <!-- End Filter -->
                        </div>
                    </div>
                </div>
                <!-- End Header -->

                <!-- Table -->
                <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                   "columnDefs": [{"targets": [3,4],"orderable": false}],
                   "order": [],
                   "info": {"totalQty": "#datatableWithPaginationInfoTotalQty"},
                   "search": "#datatableWithSearchInput",
                   "entries": "#datatableEntries",
                   "pageLength": 10,
                   "isResponsive": false,
                   "isShowPaging": false,
                   "pagination": "datatablePagination"
                    }'
                >

                        <thead class="thead-light">
                            <tr>
                                <th>Role</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                $sql_select = "SELECT * FROM user_tbl";

                                $user_no = mysqli_query($dbconn,$sql_select);  
                                if(mysqli_num_rows($user_no) >0){
                                    foreach($user_no as $row){
                            ?>
                                        <tr>
                                            <td><?php echo $row['UserRole']?></td> 
                                            <td><?php echo htmlentities($row['Username'])?></td>
                                            <td><?php echo htmlentities($row['UserEmail'])?></td>
                                            <td><a href="EditUser.php?id=<?php echo $row['UserID'] ?>"  class="btn btn-success"><i class="bi bi-pencil"></i> Edit</a></td>
                                            <td>
                                                <?php if ($row['UserID'] != $user_id): ?>
                                                    <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModalToggle<?php echo $row['UserID']; ?>"><i class="bi bi-trash"></i> Delete</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->

                <!-- Footer -->
                <div class="card-footer">
                    <!-- Pagination -->
                    <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                        <div class="col-sm mb-2 mb-sm-0">
                            <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                                <span class="me-2">Showing:</span>

                                <!-- Select -->
                                <div class="tom-select-custom">
                                    <select id="datatableEntries" class="js-select form-select form-select-borderless w-auto" autocomplete="off" data-hs-tom-select-options='{"searchInDropdown": false, "hideSearch": true}'>
                                        <option value="4">4</option>
                                        <option value="6">6</option>
                                        <option value="8" selected>8</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                                <!-- End Select -->

                                <span class="text-secondary me-2">of</span>

                                <!-- Pagination Quantity -->
                                <span id="datatableWithPaginationInfoTotalQty"></span>
                            </div>
                        </div>
                        <!-- End Col -->

                        <div class="col-sm-auto">
                            <div class="d-flex justify-content-center justify-content-sm-end">
                                <!-- Pagination -->
                                <nav id="datatablePagination" aria-label="Activity pagination"></nav>
                            </div>
                        </div>
                        <!-- End Col -->
                    </div>
                    <!-- End Pagination -->
                </div>
                <!-- End Footer -->

            </div>
        </div>
    </div>

    <?php
        foreach ($user_no as $row) {
    ?>
            <div class="modal fade" id="deleteModalToggle<?php echo $row['UserID']; ?>" aria-hidden="true" aria-labelledby="deleteModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deleteModalToggleLabel">Delete <?php echo $row['UserRole']; ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this <?php echo $row['UserRole']; ?>?</p>
                            <p>Username: <?php echo $row['Username']; ?></p>
                            <p>Email: <?php echo $row['UserEmail']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger" onclick="confirmDelete(<?php echo $row['UserID']; ?>)">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>  

    <!-- JS Plugins Init. -->
    <script>
        $(document).on("ready", function() {
            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $(".js-daterangepicker").daterangepicker();

            $(".js-daterangepicker-times").daterangepicker({
                timePicker: true,
                startDate: moment().startOf("hour"),
                endDate: moment().startOf("hour").add(32, "hour"),
                locale: {
                    format: "M/DD hh:mm A",
                },
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $("#js-daterangepicker-predefined .js-daterangepicker-predefined-preview").html(start.format("MMM D") + " - " + end.format("MMM D, YYYY"));
            }

            $("#js-daterangepicker-predefined").daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        Today: [moment(), moment()],
                        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                        "Last 7 Days": [moment().subtract(6, "days"), moment()],
                        "Last 30 Days": [moment().subtract(29, "days"), moment()],
                        "This Month": [moment().startOf("month"), moment().endOf("month")],
                        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                    },
                },
                cb
            );

            cb(start, end);
        });

        // INITIALIZATION OF DATATABLES
        // =======================================================
        HSCore.components.HSDatatables.init($("#datatable"), {
            select: {
                style: "multi",
                selector: 'td:first-child input[type="checkbox"]',
                classMap: {
                    checkAll: "#datatableCheckAll",
                    counter: "#datatableCounter",
                    counterInfo: "#datatableCounterInfo",
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

        function confirmDelete(UserID) {
            window.location.href = "DeleteUser.php?id=" + UserID;
        }
    </script>

    <!-- End Content -->
</main>     