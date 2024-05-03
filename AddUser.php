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

    $error = "";

    if (isset($_POST['submit'])) {
        $role = $_POST['UserRole'];
        $username = mysqli_real_escape_string($dbconn, $_POST['Username']);
        $email = strip_tags($_POST['UserEmail']);
        $password = strip_tags($_POST['UserPassword']);
        $hashedPassword = md5($password);
        $currency = "1";
      
        $check_email = mysqli_query($dbconn, "SELECT * FROM user_tbl WHERE UserEmail = '$email'");
        
        if (empty($role)) {
            $error = "Role cannot be empty";
        } elseif (empty($username)) {
            $error = "Username cannot be empty";
        } elseif (empty($email)) {
            $error = "Email cannot be empty";
        } else if (empty($password)) {
            $error = "Password cannot be empty";
        } else if (mysqli_num_rows($check_email) > 0) {
            $error = "Email address already exists";
        } elseif (!preg_match('/^[a-zA-Z0-9_@.!]+$/', $email)) {
            $error = "Please enter a valid email";
        } else {
            if (!empty($password)) {
                $sql = "INSERT INTO `user_tbl`(`CurrencyID`, `UserRole`, `Username`, `UserEmail`, `UserPassword`) 
                            VALUES ('$currency','$role','$username','$email','$hashedPassword')";
        
                $result = mysqli_query($dbconn, $sql);
                header("Location: ManageUser.php?msg=New user added successfully");
            }
        }
    }
?>

<?php include 'MasterPage.php'; ?>
<main id="content" role="main" class="main">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-3 mb-lg-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">Add User</h1>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="UserRole" class="form-select">
                            <option value="">== Please Select ==</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="Username" class="form-control" placeholder="Username">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">User Email</label>
                        <input type="email" name="UserEmail" class="form-control" placeholder="Email Address">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">User Password</label>
                        <input type="password" name="UserPassword" class="form-control">
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
                            <a href="ManageUser.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>