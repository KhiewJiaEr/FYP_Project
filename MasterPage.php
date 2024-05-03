<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    require("personalAssetsManagerConn.php");

    $user_id = $_SESSION["userid"];
    $select_user_sql = "SELECT * FROM user_tbl WHERE UserID = $user_id";
    $result_User = mysqli_query($dbconn, $select_user_sql);  
    $row_User = mysqli_fetch_assoc($result_User);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <title>Personal Assets Manager</title>
        
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
        <link rel="preload" href="assets/css/theme-dark.min.css" data-hs-appearance="dark" as="style" />

        <style data-hs-appearance-onload-styles>
            * {
                transition: unset !important;
            }

            body {
                opacity: 0;
            }
        </style>

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

        <!-- ========== HEADER ========== -->

        <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-container navbar-bordered bg-white">
            <div class="navbar-nav-wrap">
                <div class="navbar-nav-wrap-content-start">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                        <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
                        <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->                           
                </div>

                <div class="navbar-nav-wrap-content-end">
                    <!-- Navbar -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <!-- Account -->
                            <div class="dropdown">
                                <a class="navbar-dropdown-account-wrapper" href="javascript:;" id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation>
                                    <div class="avatar avatar-sm avatar-circle">
                                        <img class="avatar-img" src="assets/img/160x160/img1.jpg" alt="Image Description" />
                                    </div>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account" aria-labelledby="accountNavbarDropdown" style="width: 16rem;">
                                    <div class="dropdown-item-text">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0"><?php echo htmlentities($row_User['Username']) ?></h5>
                                                <p class="card-text text-body"><?php echo htmlentities($row_User['UserEmail']) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="Settings.php"><i class="bi bi-gear nav-icon"></i> Settings</a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="Logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                                </div>
                            </div>
                            <!-- End Account -->
                        </li>
                    </ul>
                    <!-- End Navbar -->
                </div>
            </div>
        </header>

        <!-- ========== END HEADER ========== -->

        <!-- ========== MAIN CONTENT ========== -->
        
        <!-- Navbar Vertical -->
        <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered bg-white">
            <div class="navbar-vertical-container">
                <div class="navbar-vertical-footer-offset">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                        <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
                        <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
                    </button>

                    <!-- End Navbar Vertical Toggle -->

                    <!-- Content -->
                    <div class="navbar-vertical-content">
                        <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">
                  
                            <!-- Logo -->
                            <div class="nav-item" aria-label="Front">
                                <a class="nav-link" href="HomeUser.php">
                                    <h4 class="nav-link-title" style="font-family: 'League Spartan'; font-size: 17px; color: #8cbe00;" data-hs-theme-appearance="default">Personal Assets Manager</h4>
                                    <h4 class="nav-link-title" style="font-family: 'League Spartan'; font-size: 17px; color: #8cbe00;" data-hs-theme-appearance="dark">Personal Assets Manager</h4>
                                    <img class="navbar-brand-logo-mini" src="assets/img/logo/logo_mini_light.png" alt="Logo" data-hs-theme-appearance="default">
                                    <img class="navbar-brand-logo-mini" src="assets/img/logo/logo_mini_dark.png" alt="Logo" data-hs-theme-appearance="dark">
                                </a>
                            </div>
                            <!-- End Logo -->
                            <?php
                                if($_SESSION['role'] == "User"){ // Only User can see this
                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="HomeUser.php">';
                                            echo'<i class="bi-house-door nav-icon"></i>';
                                            echo'<span class="nav-link-title">Home</span>';
                                        echo'</a>';
                                    echo'</div>';

                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="Stats.php">';
                                            echo'<i class="bi bi-clipboard-data nav-icon"></i>';
                                            echo'<span class="nav-link-title">Stats</span>';
                                        echo'</a>';
                                    echo'</div>';

                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="AccountInvesting.php">';
                                            echo'<i class="bi bi-graph-up-arrow nav-icon"></i>';
                                            echo'<span class="nav-link-title">Account Investing</span>';
                                        echo'</a>';
                                    echo'</div>';

                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="Expenses.php">';
                                            echo'<i class="bi bi-pie-chart nav-icon"></i>';
                                            echo'<span class="nav-link-title">Expenses</span>';
                                        echo'</a>';
                                    echo'</div>';
                                }
                            ?>

                            <?php
                                if($_SESSION['role'] == "Admin"){ // Only Admin can see this
                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="HomeAdmin.php">';
                                            echo'<i class="bi-house-door nav-icon"></i>';
                                            echo'<span class="nav-link-title">Home</span>';
                                        echo'</a>';
                                    echo'</div>';

                                    echo'<div class="nav-item">';
                                        echo'<a class="nav-link" href="ManageUser.php">';
                                            echo'<i class="bi bi-people nav-icon"></i>';
                                            echo'<span class="nav-link-title">Manage User</span>';
                                        echo'</a>';
                                    echo'</div>';
                                }
                            ?>

                        </div>
                    </div>
                    <!-- End Content -->

                    <!-- Footer -->
                    <div class="navbar-vertical-footer">
                        <ul class="navbar-vertical-footer-list">
                            <li class="navbar-vertical-footer-list-item">
                                <!-- Style Switcher -->
                                <div class="dropdown dropup">
                                    <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation></button>

                                    <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="selectThemeDropdown">
                                        <a class="dropdown-item" href="#" data-icon="bi-brightness-high" data-value="default">
                                            <i class="bi-brightness-high me-2"></i>
                                            <span class="text-truncate" title="Default (light mode)">Default (light mode)</span>
                                        </a>
                                        <a class="dropdown-item active" href="#" data-icon="bi-moon" data-value="dark">
                                            <i class="bi-moon me-2"></i>
                                            <span class="text-truncate" title="Dark">Dark</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- End Style Switcher -->
                            </li>
                        </ul>
                    </div>
                    <!-- End Footer -->
                </div>
            </div>
        </aside>
        <!-- End Navbar Vertical -->
        
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
                    // INITIALIZATION OF NAVBAR VERTICAL ASIDE
                    // =======================================================
                    new HSSideNav(".js-navbar-vertical-aside").init();

                    // INITIALIZATION OF BOOTSTRAP DROPDOWN
                    // =======================================================
                    HSBsDropdown.init();

                    // INITIALIZATION OF SELECT
                    // =======================================================
                    HSCore.components.HSTomSelect.init(".js-select");

                    // INITIALIZATION OF CLIPBOARD
                    // =======================================================
                    HSCore.components.HSClipboard.init(".js-clipboard");
                };
            })();
        </script>

        <!-- Style Switcher JS -->
        <script>
            (function () {
                // STYLE SWITCHER
                // =======================================================
                const $dropdownBtn = document.getElementById("selectThemeDropdown"); // Dropdowon trigger
                const $variants = document.querySelectorAll(`[aria-labelledby="selectThemeDropdown"] [data-icon]`); // All items of the dropdown

                // Function to set active style in the dorpdown menu and set icon for dropdown trigger
                const setActiveStyle = function () {
                    $variants.forEach(($item) => {
                        if ($item.getAttribute("data-value") === HSThemeAppearance.getOriginalAppearance()) {
                            $dropdownBtn.innerHTML = `<i class="${$item.getAttribute("data-icon")}" />`;
                            return $item.classList.add("active");
                        }

                        $item.classList.remove("active");
                    });
                };

                // Add a click event to all items of the dropdown to set the style
                $variants.forEach(function ($item) {
                    $item.addEventListener("click", function () {
                        HSThemeAppearance.setAppearance($item.getAttribute("data-value"));
                    });
                });

                // Call the setActiveStyle on load page
                setActiveStyle();

                // Add event listener on change style to call the setActiveStyle function
                window.addEventListener("on-hs-appearance-change", function () {
                    setActiveStyle();
                });
            })();
        </script>
        <!-- End Style Switcher JS -->