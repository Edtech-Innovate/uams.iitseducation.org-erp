<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    .card {
        box-shadow: none !important;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!--start am chart -->
<style>
    #chartdiv {
        width: 100%;
        height: 300px;
    }
</style>

<!-- Resources -->
<script src="/assets/js/am-chart/core.js"></script>
<script src="/assets/js/am-chart/chart.js"></script>
<!--end am chart -->
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php
                            $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i):
                                    $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    $breadcrumbText = str_replace("-", " ", $crumb[0]); // Replace hyphens with spaces
                                    echo '<li class="breadcrumb-item ' . $active . '">' . ucwords(strtolower($breadcrumbText)) . '</li>';
                                endif;
                            }
                            ?>
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4 col-xl-3 col-lg-4">
                        <div class="card custom-card custom_round">
                            <div class="card-body dash1 custom_round dash_card_shadow">
                                <a href="/users/centers" class="link_dash_card cursor-pointer">
                                    <div class="d-flex">
                                        <p class="mb-1 tx-inverse h4 font-weight-bold custom_text_font">Total Centers
                                        </p>
                                        <div class="ml-auto">
                                            <i class="fas fa-chart-line fs-20 text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'Center' ");
                                        $records = mysqli_fetch_assoc($all_count);
                                        $totalRecords = $records['allcount'];
                                        ?>
                                        <h3 class="dash-25 dashboard_gap_panel d-flex align-items-end custom_text_font">
                                            <span><i class="uil uil-graduation-hat dashboards_icon_bg"></i></span><span
                                                class="fw_custom_text"><?= $totalRecords ?></span>
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xl-3 col-lg-4">
                        <div class="card custom-card custom_round">
                            <div class="card-body dash1 custom_round dash_card_shadow">
                                <a href="/admissions/applications" class="link_dash_card cursor-pointer">
                                    <div class="d-flex">
                                        <p class="mb-1 tx-inverse h4 font-weight-bold custom_text_font">Total Students
                                        </p>
                                        <div class="ml-auto">
                                            <i class="fas fa-chart-line fs-20 text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " ");
                                        $records = mysqli_fetch_assoc($all_count);
                                        $totalRecords = $records['allcount'];
                                        ?>
                                        <h3 class="dash-25 dashboard_gap_panel d-flex align-items-end custom_text_font">
                                            <span><i class="uil uil-users-alt dashboards_icon_bg"></i></span><span
                                                class="fw_custom_text"><?= $totalRecords ?></span>
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xl-3 col-lg-4">
                        <div class="card custom-card custom_round">
                            <div class="card-body dash1 custom_round dash_card_shadow">
                                <a href="/academics/specializations" class="link_dash_card cursor-pointer">
                                    <div class="d-flex">
                                        <p class="b-1 tx-inverse h4 font-weight-bold custom_text_font mb-0">Total
                                            Courses</p>
                                        <div class="ml-auto">
                                            <i class="fas fa-chart-line fs-20 text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Sub_Courses WHERE University_ID = " . $_SESSION['university_id'] . " ");
                                        $records = mysqli_fetch_assoc($all_count);
                                        $totalRecords = $records['allcount'];
                                        ?>
                                        <h3 class="dash-25 dashboard_gap_panel d-flex align-items-end custom_text_font">
                                            <span><i class="uil uil-book-open dashboards_icon_bg"></i></span><span
                                                class="fw_custom_text"><?= $totalRecords ?></span>
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xl-3 col-lg-4">
                        <div class="card custom-card custom_round">
                            <div class="card-body dash1 custom_round dash_card_shadow">
                                <div class="d-flex">
                                    <p class="b-1 tx-inverse h4 font-weight-bold custom_text_font mb-0">Total Revenue
                                    </p>
                                    <div class="ml-auto">
                                        <i class="fas fa-chart-line fs-20 text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    $all_counts = $conn->query("SELECT SUM(Amount) as totalamount FROM Payments WHERE Status = 1 ");
                                    $records = mysqli_fetch_assoc($all_counts);
                                    $totalRecords = $records['totalamount'];
                                    ?>
                                    <h3 class="dash-25 dashboard_gap_panel d-flex align-items-end custom_text_font">
                                        <span><iconify-icon icon="uil:rupee-sign"
                                                class="h1 dashboards_icon_bg "></iconify-icon></span><span
                                            class="fw_custom_text"><?= $totalRecords ?></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($_SESSION['Role']=="Administrator" || $_SESSION['Role']=="Operations"){ ?>
                    <div class="col-sm-12 col-xl-12 col-lg-12">
                        <div class="card custom-card custom_round">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Specialization Reports</h3>
                                    <a href="/app/applications/dashboard-sub-course-adm-export"
                                        class="btn btn-primary btn-sm">Download</a>
                                </div>
                                <?php

                                $role_query = str_replace('{{ table }}', 'Students', isset($_SESSION['RoleQuery']) ? $_SESSION['RoleQuery'] : '');
                                $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                                $adm_reports = [];
                                $sqldata = $conn->query("SELECT Sub_Courses.Name, COUNT(Students.ID) AS total_adm FROM Sub_Courses LEFT JOIN Students ON Students.Sub_Course_ID = Sub_Courses.ID AND Students.University_ID = " . $_SESSION['university_id'] . " AND Step =4  AND Students.Process_By_Center IS NOT NULL $role_query  AND Students.Deleted_At IS NULL GROUP BY Sub_Courses.ID ORDER BY total_adm DESC");
                                if ($sqldata->num_rows > 0) {
                                    while ($row = $sqldata->fetch_assoc()) {
                                        // $adm_reports[] = $row;
                                        $name = str_replace("'", "", $row['Name']);

                                        $adm_reports[] = array('Name' => $name, 'total_adm' => $row['total_adm']);

                                    }
                                    $adm_reports_data = json_encode($adm_reports);
                                } ?>
                                <div class="card-body">


                                    <div id="chartdiv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div>
                                    <h6 class="card-title mb-1">Recent added centers</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>Center Name</th>
                                                <th>Code</th>
                                                <th>Created AT</th>
                                                <th>Updated ON</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $centers = $conn->query("SELECT * FROM Users WHERE Role = 'Center' ORDER BY Users.ID DESC LIMIT 15");
                                            if ($centers->num_rows > 0) {
                                                while ($row = $centers->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row['Name'] ?></td>
                                                        <td><?= $row['Code'] ?></td>
                                                        <td><?= $row['Created_At'] ?></td>
                                                        <td><?= $row['Updated_On'] ?></td>
                                                        <td><?php if ($row['Status'] == 1) { ?> <span
                                                                    class="badge badge-success">Active</span>
                                                            <?php } else { ?> <span class="badge badge-danger">Inactive</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div>
                                    <h6 class="card-title mb-1">Recent added Students</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Student Code</th>
                                                <th>DOB</th>
                                                <th>Created At</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $centers = $conn->query("SELECT * FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " ORDER BY Students.ID DESC LIMIT 15");
                                            if ($centers->num_rows > 0) {
                                                while ($row = $centers->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row['First_Name'] ?></td>
                                                        <td><?= $row['Unique_ID'] ?></td>
                                                        <td><?= $row['DOB'] ?></td>
                                                        <td><?= $row['Created_At'] ?></td>
                                                        <td><?php if ($row['Status'] == 1) { ?> <span
                                                                    class="badge badge-success">Active</span>
                                                            <?php } else { ?> <span class="badge badge-danger">Inactive</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <script>
            // Create chart instance
            var chart = am4core.create("chartdiv", am4charts.PieChart);

            var chartdata = '<?= $adm_reports_data ?>';
            var data = JSON.parse(chartdata);
            var totalAdmissions = 0;

            $.each(data, function (key, val) {
                totalAdmissions += parseInt(val.total_adm);
            });

            $.each(data, function (key, val) {
                var total_count = val.total_adm;
                var name =
                    chart.data.push({
                        "sub_course": val.Name + " (" + total_count + ")",
                        "total_adm": val.total_adm
                    });
            });

            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "total_adm";

            pieSeries.dataFields.category = "sub_course";


            var title = chart.titles.create();
            title.text = "Total Admissions: " + totalAdmissions;
            title.fontSize = 18;
            title.marginBottom = 10;
           
        </script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>