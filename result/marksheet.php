<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
ini_set('display_errors', 1);
require '../includes/helpers.php';

?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
    }

    .table-box-bottom table {
        border-collapse: collapse;
        border: 2px solid #8b8b8b;
        width: 100%;
    }

    .table-box-bottom th {
        border: 2px solid #8b8b8b;
    }

    .table-box-bottom td {
        border: 2px solid #8b8b8b;
    }

    body {
        margin: 0.7cm;
    }

    td.col {
        padding: 10px;
        border: 2px solid #8b8b8b;
    }

    th {
        padding: 9px;
        border-bottom: 2px solid #8b8b8b;
        border-left: 2px solid #8b8b8b;
    }


    .main-result-box {
        width: 92%;
    }

    .des {
        text-align: justify;
    }

    /* span.fw-bold,th,td ,p.text-dark,p{font-weight: 700;font-size: medium;} */
</style>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- START PAGE CONTENT WRAPPER -->
    <style>
        .table thead tr th {
            font-weight: 700;
            font-size: 11.5px;
        }
    </style>
    <div class="page-content-wrapper ">

        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i):
                                    $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                                endif;
                            }
                            ?>
                            <div>
                            </div>
                        </ol>
                        <!-- END BREADCRUMB -->

                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class=" container-fluid">
                <!-- BEGIN PlACE PAGE CONTENT HERE -->
                <div class="card card-transparent">


                    <?php

                    $webQuery = '';
                    $semQuery = '';
                    $sem = '';
                    $studentId = '';
                    if (isset($_GET['year_sem'])) {
                        $sem = $_GET['year_sem'];
                        $semQuery = "&year_sem=" . $sem;
                    }
                    if (isset($_GET['user_id']) && isset($_GET['password'])) {
                        $user_id = $_GET['user_id'];
                        $password = $_GET['password'];

                        $webQuery = "?user_id=" . $user_id . "&password=" . $password . $semQuery;
                    } else if (isset($_GET['studentId'])) {
                        $studentId = $_GET['studentId'];
                        $webQuery = "?studentId=" . $studentId . $semQuery;
                    } else {
                        if ($_SESSION['Role'] == 'Student') {
                            $webQuery = "?enroll_no=" . $_SESSION['Enrollment_No'] . $semQuery;
                        }
                    }
                    $rows = [];

                    $get_result_data_url = ERP_URL . "/result/api" . $webQuery;
                    $context = stream_context_create(array(
                        'http' => array(
                            'follow_location' => false,
                            'timeout' => 2,
                        )
                    ));
                    $result = file_get_contents($get_result_data_url, false, $context);
                    $rows = json_decode($result, true);

                    $total_duration = getMaxDuration($conn, $_SESSION['university_id']);

                    ?>

                    <div class="text-center mt-5">
                        <div class="col-md-3" style="margin: -38px 0px 0px 25px;">
                            <select class="form-control" name="year_semester" id="year_semester">
                                <option value="">Select</option>
                                <?php for ($i = 1; $i <= $total_duration; $i++) { ?>
                                    <option value="<?= $i ?>" <?= ($i == $sem) ? 'selected' : '' ?>>Semester <?= $i ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php if (isset($rows['status']) && $rows['status'] == 1) {
                        $row = $rows['data']; ?>
                        <div id="contentpdf" class="html-content">
                            <div class="result_section">
                                <div class="mt-5 body"
                                    style="border:3px solid #1e1919; height: 1204px; width: 900px; margin: 0 auto; background-position: center; background-size: contain; background-repeat: no-repeat; padding: 20px;">
                                    <div class="" style="display:flex; justify-content:center;">
                                        <img src="<?= $row['uni_logo']?>"
                                            alt="" style="margin-top: 15px;width:27%">
                                    </div>
                                    <p
                                        style="margin-top:1%;text-align: center;font-weight: 700;font-size: 20px!important;color:black !important;">
                                        (A University Established by UP Act 2 of 2012)</p>
                                    <div class="main-result-box"
                                        style="padding: 0px; height: 0px; margin: 0 auto; position: relative; top: 0px; right: 0px;">
                                        <p class="text-center text-dark fw-bold" style="font-size:medium; font-weight:700">
                                            Statement Of Marks</p>
                                        <p class="text-center text-dark fw-bold" style="font-size:medium; font-weight:700">
                                            <?= ucwords(strtolower($row['duration_val'])) ?> in
                                            <?= ucwords(strtolower($row['course'])) ?>
                                        </p>
                                        <p class="text-center text-dark fw-bold" style="font-size:medium; font-weight:700">
                                            Admission Session
                                            :<?= ucwords($row['Admission_Session']) ?></p>
                                        <img src="<?= ERP_URL ?>/<?= $row['Photo'] ?>" alt="" width="100" height="100"
                                            class="img-pp" style="position: absolute;right: 1px;top: 10px;">
                                        <div class="row" style="margin-top: 50px;">
                                            <div class="col-lg-12 mb-1">
                                                <div class="table-resposive">
                                                    <table class="table-bordered mb-3">
                                                        <tbody>
                                                            <tr>
                                                                <td class="col text-start"
                                                                    style="width:600px; height:40px;">
                                                                    <span class="fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700">Name:</span>
                                                                    <span class="text-dark fw-bold"
                                                                        style="font-size:medium; font-weight:700"><?= $row['stu_name'] ?></span>
                                                                </td>
                                                                <td class="col text-start"
                                                                    style="width:400px; height:40px;">
                                                                    <span class="fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700">
                                                                        Enrollment No:</span>
                                                                    <span class="text-dark fw-bold"
                                                                        style="font-size:medium; font-weight:700"><?= $row['Enrollment_No'] ?></span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="col text-start"
                                                                    style="width:600px; height:40px;">
                                                                    <span class="fw-bold "
                                                                        style="color: #05519E;font-size:medium; font-weight:700">Father
                                                                        Name:</span>
                                                                    <span class="text-dark fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700"><?= $row['Father_Name'] ?></span>
                                                                </td>
                                                                <td class="col text-start"
                                                                    style="width:400px; height:40px;">
                                                                    <span class="fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700"><?= $row['mode_type'] ?>:</span>
                                                                    <span class="text-dark fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700">
                                                                        <?= $row['durMonthYear'] ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="col text-start"
                                                                    style="width:600px; height:40px;">
                                                                    <span class="fw-bold "
                                                                        style="color: #05519E;font-size:medium; font-weight:700">School:</span>
                                                                    <span class="text-dark fw-bold"
                                                                        style="color: #05519E;font-size:medium; font-weight:700">
                                                                        <?= $row['university_name'] ?></span>
                                                                </td>
                                                                <td class="col text-start"
                                                                    style="width:400px; height:40px;"><span class="fw-bold "
                                                                        style="color: #05519E;font-size:medium; font-weight:700">Exam
                                                                        Session:</span><span class="text-dark fw-bold "
                                                                        style="color: #05519E;font-size:medium; font-weight:700">
                                                                        <?= $row['stu_exam_session'] ?> </span></td>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-box">
                                            <table width="100%"
                                                style="border-collapse: collapse; border: 2px solid #8b8b8b; width: 100%;">

                                                <tbody>
                                                    <tr class="text-center border-bottom-0">
                                                        <th scope="col" class="col blue"
                                                            style="width: 10%;border-bottom: 1px solid #fff;font-size:medium; font-weight:700; border-right: 2px solid #8b8b8b;">
                                                            Subject Code
                                                        </th>
                                                        <th scope="col" class="col blue"
                                                            style="width: 60%;border-bottom: 1px solid #fff;font-size:medium; font-weight:700; border-right: 2px solid #8b8b8b;">
                                                            Subject Name
                                                        </th>
                                                        <th scope="col" colspan="2" class="blue"
                                                            style="font-size:medium; font-weight:700; border-right: 2px solid #8b8b8b;">
                                                            Internal</th>
                                                        <th scope="col" colspan="2" class="col blue"
                                                            style="width: 10%;font-size:medium; font-weight:700; border-right: 2px solid #8b8b8b;">
                                                            External</th>
                                                        <th scope="col" colspan="2" class="col blue"
                                                            style="width: 10%;font-size:medium; font-weight:700; ">
                                                            Total</th>
                                                    </tr>
                                                    <tr class="border-top-0 text-center">
                                                        <th scope="col"></th>
                                                        <th scope="col" style="border-left: 2px solid #8b8b8b;"></th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style="width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-left: 2px solid #8b8b8b; border-bottom: 2px solid #8b8b8b;">
                                                            OBT</th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style=" width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-bottom: 2px solid #8b8b8b;">
                                                            Max</th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style=" width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-bottom: 2px solid #8b8b8b;">
                                                            OBT</th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style=" width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-bottom: 2px solid #8b8b8b;">
                                                            Max</th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style=" width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-bottom: 2px solid #8b8b8b;">
                                                            OBT</th>
                                                        <th scope="col" class="col border-top-1 blue"
                                                            style=" width:10%; border-top: 1px solid #8b8b8b;font-size:medium; font-weight:700; border-bottom: 2px solid #8b8b8b;">
                                                            Max</th>
                                                    </tr>
                                                    <?php foreach ($row['marks'] as $result) { ?>
                                                        <tr class="text-center" style="font-weight: 700;">
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;border-top: 2px solid #8b8b8b;"
                                                                class="text-dark"><?= $result['Code'] ?></td>
                                                            <td class="text-left text-dark"
                                                                style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px; border-top: 2px solid #8b8b8b; text-align:start !important;">
                                                                <?= $result['subject_name'] ?>
                                                            </td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= $result['obt_marks_int'] ?></td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= $result['Min_Marks'] ?></td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= $result['obt_marks_ext'] ?></td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= $result['Max_Marks'] ?></td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= ($result['total_max_marks']) ?></td>
                                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                                class="text-dark"><?= ($result['obt_marks']) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                        <p class="text-center mt-3 mb-3"
                                            style="text-align:center;font-size: 22px; font-weight: 900; color: #05519E;">
                                            AGGREGATE
                                            MARKS</p>
                                        <div class="table-box-bottom">
                                            <table class="text-center"
                                                style="border-collapse: collapse;border: 2px solid #8b8b8b;width: 100%;">
                                                <tbody>
                                                    <tr style="color: #05519E; font-weight: 700;">
                                                        <th
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700">
                                                            Marks</th>
                                                        <th
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700">
                                                            GRAND TOTAL</th>
                                                        <th
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700">
                                                            RESULT</th>
                                                        <th
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700">
                                                            PERCENTAGE</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark">Obtained
                                                            Mark</th>
                                                        <td style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark">
                                                            <?= $row['total_obt'] ?>
                                                        </td>
                                                        <td rowspan="2"
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark">
                                                            <?= $row['result_status'] ?>
                                                        </td>
                                                        <td rowspan="2"
                                                            style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark">
                                                            <?= $row['percentage'] ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark"> Maximum
                                                            Mark</th>
                                                        <td style="border: 2px solid #8b8b8b;font-size:medium; font-weight:700"
                                                            class="text-dark">
                                                            <?= $row['total_max'] ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="des">
                                            <p class="p-0 m-0"
                                                style="position: relative;font-size: 20px; top: 14px; right: 10px;color: #05519E;font-weight: 700;display: inline-block;">
                                                <span class="top-heading-u"></span>Disclaimer :
                                            </p>
                                            <p
                                                style="position: relative; top: 17px;color: #05519E;font-weight: 700;display: inline-block;">
                                                <span class="top-heading-u"></span> The published result is provisional
                                                only. <?= $row['uni_name'] ?>
                                                 is not responsible for any inadvertent error that may have crept
                                                in the data
                                                / results being published online.This is being published just for the
                                                immediate
                                                information to the examinees. The final mark sheet(s) issued by <?= $row['uni_name'] ?> will
                                                only be treated authentic &amp; final in this regard.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center no-print mb-4 mt-3">
                            <button type="button" class="btn btn-primary" id="cmd" onclick="printDiv('contentpdf')">Download
                                as
                                PDF/Print</button>
                        </div>
                    <?php } else { ?>
                        <div class="mt-5 mb-4 text-center">
                            <div class="card-body text-center">
                                <img src="/assets/cross.png" alt="Info Icon"
                                    style="width: 80px; height: 80px; opacity: 0.6;">
                                <h2 class="mt-3" style="color: #555;">
                                    <?= $rows['msg'] ?>
                                </h2>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- END PLACE PAGE CONTENT HERE -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
        <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <?php if (isset($_SESSION['Role']) && $_SESSION['Role'] == "Student") { ?>
        <?php } else { ?>
            <script type="text/javascript">
                $("#year_semester").change(function () {
                    var year_sem = $("#year_semester").val();

                    window.location.href = '/result/marksheet?studentId=' + <?= $studentId ?> + '&year_sem=' + year_sem;
                });

            </script>
        <?php } ?>
        <script>
            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }

            function toRoman(type) {
                var roman = ["st", "nd", "rd", "th", "th", "th", "th", "th"];
                $('.semsyear').text(roman[type - 1]);
            }
        </script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>