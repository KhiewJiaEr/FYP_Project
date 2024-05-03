<?php
    session_start();

    require("personalAssetsManagerConn.php");
    $error = "";
    $errorAlert = '';
    $successMessage = '';

    if (isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($dbconn, $_POST['Username']);
        $email = strip_tags($_POST['UserEmail']);
        $password = strip_tags($_POST['UserPassword']);
        $comfirmpassword = strip_tags($_POST['ComfirmPassword']);
        $hashedPassword = md5($password);
        $currency = "1";
        $role = "User";

        $check_email = mysqli_query($dbconn, "SELECT * FROM user_tbl WHERE UserEmail = '$email'");

        if (empty($username)) {
            $error = "Please enter your username";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif (empty($email)) {
            $error = "Please enter your email";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif (!preg_match('/^[a-zA-Z0-9_@.!]+$/', $email)) {
            $error = "Please enter a valid email";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else if (mysqli_num_rows($check_email) > 0) {
            $error = "This email address is already used";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else if (empty($password)) {
            $error = "Please enter your password";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif (empty($comfirmpassword)) {
            $error = "Please enter the comfirm password";
            $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
            . $error .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            if (!empty($password)) {
                if ($password != $comfirmpassword) {
                    $error = "Password and Confirm Password are not the same";
                    $errorAlert .= "<div class='alert alert-warning alert-dismissible fade show' role=alert'>"
                    . $error .
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } elseif ($password == $comfirmpassword) {
                    $sql = "INSERT INTO `user_tbl`(`CurrencyID`, `UserRole`, `Username`, `UserEmail`, `UserPassword`) 
                        VALUES ('$currency','$role','$username','$email','$hashedPassword')";

                    $result = mysqli_query($dbconn, $sql);
                    if ($result) {
                        $successMessage = "User sign up successful!";
                    } else {
                        $error = "An error occurred while registering. Please try again later.";
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <title>Sign Up</title>
        
        <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png" />
        <link rel="manifest" href="site.webmanifest" />
        <link rel="mask-icon" color="#fe6a6a" href="safari-pinned-tab.svg" />
        <meta name="msapplication-TileColor" content="#ffffff" />
        <meta name="theme-color" content="#ffffff" />

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet" />
		<link rel="stylesheet" media="screen" href="assets/vendor/tiny-slider/dist/tiny-slider.css"/>
        <link rel="stylesheet" href="assets/css/vendor.min.css" />

        <link rel="stylesheet" href="assets/css/theme.minc619.css?v=1.0" />

        <link rel="preload" href="assets/css/theme.min.css" data-hs-appearance="default" as="style" />

        <script>
            window.hs_config = {
                autopath: "@@autopath",
                deleteLine: "hs-builder:delete",
                "deleteLine:build": "hs-builder:build-delete",
                "deleteLine:dist": "hs-builder:dist-delete",
                previewMode: false,
                startPath: "/index.html",
                vars: { themeFont: "https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap", version: "?v=1.0" },
                layoutBuilder: { extend: { switcherSupport: true }, header: { layoutMode: "default", containerMode: "container-fluid" }, sidebarLayout: "default" },
                themeAppearance: {
                    layoutSkin: "default",
                    sidebarSkin: "default",
                    styles: { colors: { primary: "#377dff", transparent: "transparent", white: "#fff", dark: "132144", gray: { "100": "#f9fafc", "900": "#1e2022" } }, font: "Inter" },
                },
                languageDirection: { lang: "en" },
                skipFilesFromBundle: {
                    dist: ["assets/js/hs.theme-appearance.js", "assets/js/hs.theme-appearance-charts.js", "assets/js/demo.js"],
                    build: [
                        "assets/css/theme.css",
                        "assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js",
                        "assets/js/demo.js",
                        "assets/css/theme-dark.html",
                        "assets/css/docs.css",
                        "assets/vendor/icon-set/style.html",
                        "assets/js/hs.theme-appearance.js",
                        "assets/js/hs.theme-appearance-charts.js",
                        "node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.html",
                        "assets/js/demo.js",
                    ],
                },
                minifyCSSFiles: ["assets/css/theme.css", "assets/css/theme-dark.css"],
                copyDependencies: { dist: { "*assets/js/theme-custom.js": "" }, build: { "*assets/js/theme-custom.js": "", "node_modules/bootstrap-icons/font/*fonts/**": "assets/css" } },
                buildFolder: "",
                replacePathsToCDN: {},
                directoryNames: { src: "./src", dist: "./dist", build: "./build" },
                fileNames: { dist: { js: "theme.min.js", css: "theme.min.css" }, build: { css: "theme.min.css", js: "theme.min.js", vendorCSS: "vendor.min.css", vendorJS: "vendor.min.js" } },
                fileTypes: "jpg|png|svg|mp4|webm|ogv|json",
            };
            window.hs_config.gulpRGBA = (p1) => {
                const options = p1.split(",");
                const hex = options[0].toString();
                const transparent = options[1].toString();

                var c;
                if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
                    c = hex.substring(1).split("");
                    if (c.length == 3) {
                        c = [c[0], c[0], c[1], c[1], c[2], c[2]];
                    }
                    c = "0x" + c.join("");
                    return "rgba(" + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(",") + "," + transparent + ")";
                }
                throw new Error("Bad Hex");
            };
            window.hs_config.gulpDarken = (p1) => {
                const options = p1.split(",");

                let col = options[0].toString();
                let amt = -parseInt(options[1]);
                var usePound = false;

                if (col[0] == "#") {
                    col = col.slice(1);
                    usePound = true;
                }
                var num = parseInt(col, 16);
                var r = (num >> 16) + amt;
                if (r > 255) {
                    r = 255;
                } else if (r < 0) {
                    r = 0;
                }
                var b = ((num >> 8) & 0x00ff) + amt;
                if (b > 255) {
                    b = 255;
                } else if (b < 0) {
                    b = 0;
                }
                var g = (num & 0x0000ff) + amt;
                if (g > 255) {
                    g = 255;
                } else if (g < 0) {
                    g = 0;
                }
                return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
            };
            window.hs_config.gulpLighten = (p1) => {
                const options = p1.split(",");

                let col = options[0].toString();
                let amt = parseInt(options[1]);
                var usePound = false;

                if (col[0] == "#") {
                    col = col.slice(1);
                    usePound = true;
                }
                var num = parseInt(col, 16);
                var r = (num >> 16) + amt;
                if (r > 255) {
                    r = 255;
                } else if (r < 0) {
                    r = 0;
                }
                var b = ((num >> 8) & 0x00ff) + amt;
                if (b > 255) {
                    b = 255;
                } else if (b < 0) {
                    b = 0;
                }
                var g = (num & 0x0000ff) + amt;
                if (g > 255) {
                    g = 255;
                } else if (g < 0) {
                    g = 0;
                }
                return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
            };
        </script>
    </head>

    <body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl footer-offset">
        <script src="assets/js/hs.theme-appearance.js"></script>
        <script src="assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js"></script>

        <!-- ========== MAIN CONTENT ========== -->

        <main>
			<section class="jarallax py-7 d-flex justify-content-center align-items-center pt-7 pb-4" data-jarallax="" data-speed="0" style="height: 100vh;">
				<div class="position-fixed top-0 end-0 start-0 bg-img-start" style="height: 100%; background-image: url('assets/img/background_image_login/login_background_image3.jpg');"></div>
				<div class="container">
					<div class="col-lg-4 mx-auto">
						<div class="card card-lg mb-5">
							<div class="card-body">
								<div class="js-validate needs-validation" novalidate>
									<div class="text-center">
										<div class="mb-5">
											<h1 class="display-4 mb-grid-gutter">Sign Up</h1>
										</div>
									</div>

                                    <form action="" method="post">
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" name="Username" class="form-control" placeholder="Username">
                                            </div>

                                            <div class="mb-3">
                                                <label for="userEmail" class="form-label">Email Address</label>
                                                <input type="email" name="UserEmail" class="form-control" placeholder="Email Address">
                                            </div>

                                            <div class="mb-3">
                                                <label for="userPassword" class="form-label">Password</label>
                                                <input type="password" name="UserPassword" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label for="comfirmPassword" class="form-label">Comfirm Password</label>
                                                <input type="password" name="ComfirmPassword" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                                                <label for="terms" class="form-check-label">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#TnCModalToggle"> Terms and Conditions</a></label>
                                            </div>

                                            <?php if ($successMessage) : ?>
                                                <script>
                                                    alert("<?php echo $successMessage; ?>");
                                                    window.location.href = "Login.php"; // Redirect to Login.php
                                                </script>
                                            <?php endif; ?>

                                            <?= $errorAlert ?>
                                        
                                            <div class="d-grid gap-2 col-6 mx-auto">
                                                <button type="submit" class="btn btn-primary" name="submit">Sign Up</button>
                                            </div>
                                
                                        </div>
                                    </form>
								</div>

								<div class="row">
									<div class="col-sm-6">
									</div>

									<div class="col-sm-6">
										<!-- Form -->
										<div class="mb-4">
											<p class="text-center pt-4 mb-0"><a href="Login.php">Sign In</a></p>
										</div>
										<!-- End Form -->
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</section>
		</main>

        <!-- TnC Modal -->
        <div class="modal fade" id="TnCModalToggle" aria-hidden="true" aria-labelledby="TnCModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="TnCModalToggleLabel">Terms and Conditions</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>1. By checking this checkbox, users agree to comply with the terms and conditions outlined by Personal Assets Manager.</h5></br>

                        <h5>2. Users must conduct themselves in a respectful and professional manner when using Personal Assets Manager.</h5></br>

                        <h5>3. Users must refrain from engaging in any unlawful or harmful activities using Personal Assets Manager.</h5></br>

                        <h5>4. Users must refrain from uploading, posting, or sharing any content that is offensive, defamatory, or infringing on intellectual property rights.</h5></br>

                        <h5>5. By storing data in Personal Assets Manager, users agree to grant Personal Assets Manager the non-exclusive right to use, modify, and distribute the data.</h5></br>

                        <h5>6. Personal Assets Manager does not guarantee that any stored data will be reviewed or acted upon and is not responsible for any loss or damage resulting from the submission or use of any data.</h5></br>

                        <h5>7. Personal Assets Manager reserves the right to terminate or suspend user accounts at its discretion, with or without cause and with or without notice. Users agree that Personal Assets Manager shall not be liable for any termination of their accounts.</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ========== END MAIN CONTENT ========== -->

        <script src="assets/js/demo.js"></script>
        <!-- END ONLY DEV -->

        <!-- JS Implementing Plugins -->
        <script src="assets/js/vendor.min.js"></script>
		<script src="assets/vendor/tiny-slider/dist/min/tiny-slider.js"></script>

        <script src="assets/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

        <!-- JS Front -->
        <script src="assets/js/theme.min.js"></script>
        <script src="assets/js/hs.theme-appearance-charts.js"></script>

		<!-- JS Plugins Init. -->
		<script>
			(function () {
				window.onload = function () {
					// INITIALIZATION OF TOGGLE PASSWORD
					// =======================================================
					new HSTogglePassword(".js-toggle-password");
				};
			})();
		</script>
	</body>
</html>