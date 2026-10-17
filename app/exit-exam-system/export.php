<?php
include '../../includes/db-config.php';
session_start();
error_reporting(0);
$exam_exit_status = " AND exam_exit_status  IN (1, 2,3,4,6)";

$userQuery = '';
if ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Sub-Center") {
    $userQuery = " AND Added_For = " . $_SESSION['ID'];
}


$role_query = str_replace('{{ table }}', 'Students', isset($_SESSION['RoleQuery'])?$_SESSION['RoleQuery']:'');
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$conditionsQr = $exam_exit_status . $userQuery.$role_query;
$unversity_id = $_SESSION['university_id'];
$result_record = $conn->query("SELECT Students.ID,Students.exam_exit_request_date,Students.exam_exit_status,Students.Duration, Students.Enrollment_No,Students.Unique_ID, Students.Mother_Name, Students.Father_Name,  CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS full_name, Sub_Courses.Name AS sub_course_name,Courses.Short_Name as course_short_name, Users.Code, Users.Name as user_name  FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For=  Users.ID  WHERE 1=1 AND Students.University_ID = $unversity_id $conditionsQr ORDER BY Students.ID DESC");
if ($result_record->num_rows > 0) {
    $randomNumber = rand(1, 10000000000);
    $filename = "Document-Issue-ance" . $randomNumber . ".csv";

    header("Content-Disposition: attachment; filename=" . $filename . "");
    header("Content-Type: text/csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $fp = fopen('php://output', 'w');
    $exam_exit_status = "1";
    $h = [];
    $flag = false;
    $h[] = "Sr. No.\t";
    $h[] = "Name\t";
    $h[] = "Enrollment No.\t";
    $h[] = "Exit Exam Status\t";
    $h[] = "Current Duration\t";
    $h[] = "Unique ID\t";
    $h[] = "Sub-Course\t";
    $h[] = "Center Name\t";

    fputcsv($fp, $h);
    $nums = 1;
    $exam_exit_status = array(
        '0' => '',
        '1' => 'Exit C',
        '2' => 'Exit D',
        '3' => 'Exit AD',
        '4' => 'Exit B',
        '5' => 'Enrolled',
        '6' => 'Drop Out',
    );

    while ($row = $result_record->fetch_assoc()) {
        $exit_status = $exam_exit_status[$row['exam_exit_status']] ?? '';
        $data = [];
        $data[] = $nums;
        $data[] = $row['full_name'];
        $data[] = $row['Enrollment_No'];
        $data[] = $exit_status;
        $data[] = $row['Duration'];
        $data[] = $row['Unique_ID'];
        $data[] = $row['sub_course_name'];
        $data[] = $row['user_name'] . "(" . $row['Code'] . ")";

        fputcsv($fp, $data);
        $nums++;
    }

    fclose($fp);
} else {
    echo "No data found";
}
