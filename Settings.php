<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();
    require("personalAssetsManagerConn.php");

    if (!isset($_SESSION['role'])) {
        header("Location: Login.php");
        exit;
    }

    if ($_SESSION['role'] !== "User" && $_SESSION['role'] !== "Admin") {
        header("Location: Login.php");
        exit;
    }

    $user_id = $_SESSION["userid"];
    $select_user_sql = "SELECT * FROM user_tbl WHERE UserID = $user_id";
    $result_User = mysqli_query($dbconn, $select_user_sql);  
    $row_User = mysqli_fetch_assoc($result_User);

    $errorAlert = "";

    if (isset($_POST['submit'])) {
        if (isset($_POST['UserEmail']))

            $check_email = mysqli_real_escape_string($dbconn, $_POST['UserEmail']);
            $sql_email_check = "SELECT UserEmail FROM user_tbl WHERE UserEmail='$check_email' AND NOT UserID='$user_id' ";
            $result_email = mysqli_query($dbconn, $sql_email_check);
            $count = mysqli_num_rows($result_email);

        if ($count > 0) {

        } else {

            $username = mysqli_real_escape_string($dbconn, $_POST['Username']);
            $email = strip_tags(mysqli_real_escape_string($dbconn, $_POST['UserEmail']));
            $currency = strip_tags($_POST['CurrencyID']);

            if (empty($username)) {
                $error = "Username cannot be empty";
                $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
                    . $error .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
            } else if (empty($email)) {
                $error = "Email cannot be empty";
                $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
                    . $error .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>';
            } else {
                if (!empty($email) || !empty($username)) {
                    if (!preg_match('/^[a-zA-Z0-9_@.!]+$/', $email)) {
                        $error = "Don't include single quotation in your email";
                        $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
                            . $error .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>';
                    } else {
                        $sql = "UPDATE `user_tbl` SET `Username`='$username',`UserEmail`='$email',`CurrencyID`='$currency'
                                WHERE UserId = $user_id";
                        mysqli_query($dbconn, $sql);
                        header("Location: Settings.php?msg=Profile updated successfully");
                    }
                }
            }
        }
    }

    if (isset($_POST["submit_new_password"])) {
        $newPassword = strip_tags(mysqli_real_escape_string($dbconn, $_POST["newPassword"]));
        $hashedPassword = md5($newPassword);
        $comfirmNewPassword = strip_tags(mysqli_real_escape_string($dbconn, $_POST["comfirmNewPassword"]));
        if ($newPassword != $comfirmNewPassword) {
            $error = "New Password and Re-enter New Password does not match..";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else if (!empty($newPassword) && !empty($comfirmNewPassword)) {
            if ($newPassword == $comfirmNewPassword) {
                mysqli_query($dbconn, "UPDATE user_tbl SET UserPassword = '$hashedPassword' WHERE UserId = '$user_id'");
                header("Location: Settings.php?msg=Password updated successfully");
                exit();
            }
        } else {
            $error = "Password cannot be empty.";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
        }
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
                    <h1 class="page-header-title">Settings</h1>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

            <section class="section profile">
                <div class="row">
                    <div class="col">
                        <?= $errorAlert ?>
                        <div class="card">
                            <div class="card-body">
                                <!-- Bordered Tabs -->
                                <ul class="nav nav-tabs nav-tabs-bordered">
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit_profile">Edit Profile</button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#change_password">Change Password</button>
                                    </li>
                                </ul>

                                <div class="tab-content pt-3">
                                    <div class="tab-pane fade pt-3 show active profile-overview" id="edit_profile">
                                        <!-- Edit Profile Form -->
                                        <form action="" method="post">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" name="Username" class="form-control" value ="<?php echo htmlentities($row_User['Username']) ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="userEmail" class="form-label">Email</label>
                                                <input type="email" name="UserEmail" class="form-control" value ="<?php echo $row_User['UserEmail'] ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="CurrencyID" class="form-label">Currency</label>
                                                <select name="CurrencyID" class="form-select">
                                                    <option value="1" <?php echo ($row_User['CurrencyID'] == '1') ? 'selected' : ''; ?>>$ (US Dollar)</option>
                                                    <option value="2" <?php echo ($row_User['CurrencyID'] == '2') ? 'selected' : ''; ?>>€ (Euro)</option>
                                                    <option value="3" <?php echo ($row_User['CurrencyID'] == '3') ? 'selected' : ''; ?>>£ (British Pound)</option>
                                                    <option value="4" <?php echo ($row_User['CurrencyID'] == '4') ? 'selected' : ''; ?>>¥ (Japanese Yen)</option>
                                                    <option value="5" <?php echo ($row_User['CurrencyID'] == '5') ? 'selected' : ''; ?>>RM (Malaysian Ringgit)</option>
                                                </select>
                                            </div>

                                            <div class="d-grid gap-2 col-3 mx-auto">
                                                <button type="submit" class="btn btn-primary" name="submit">Save Changes</button>
                                            </div>
                                        </form>
                                        <!-- End Edit Profile Form -->
                                    </div>

                                    <div class="tab-pane fade pt-3" id="change_password">
                                        <!-- Change Password Form -->
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="newPassword" class="form-label">New Password</label>
                                                <input type="password" name="newPassword" class="form-control" id="newPassword">
                                            </div>

                                            <div class="mb-3">
                                                <label for="comfirmNewPassword" class="form-label">Reconfirm New Password</label>
                                                <input name="comfirmNewPassword" type="password" class="form-control" id="comfirmNewPassword">
                                            </div>

                                            <?php
                                                if(isset($_POST["submit_new_password"])){

                                                    $userNewPassword = $_POST["newPassword"];
                                                    $userComfirmNewPassword = $_POST["comfirmNewPassword"];

                                                    if($userNewPassword != $userComfirmNewPassword){
                                                    echo '<script>alert("*New Password and Re-enter New Password does not match.")</script>';
                                                    }
                                                }
                                            ?>

                                            <div class="d-grid gap-2 col-3 mx-auto">
                                                <button type="submit" class="btn btn-primary" name="submit_new_password">Change Password</button>
                                            </div>
                                        </form>
                                        <!-- End Change Password Form -->
                                    </div>
                                </div>
                                <!-- End Bordered Tabs -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    </div>

    <!-- End Content -->
</main>