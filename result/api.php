<?php


ini_set('display_errors', 1);
require '../includes/db-config.php';
require '../includes/helpers.php';
session_start();

$typoArr = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th"];

if (isset($_GET['user_id']) && isset($_GET['password'])) {
    $user_id = $_GET['user_id'];
    $password = $_GET['password'];
    $searchQuery = " AND Students.Unique_ID LIKE '$user_id' AND Students.Unique_ID = '$password' ";
} else if (isset($_GET['enroll_no'])) {
    $searchQuery = " AND Students.Enrollment_No = '" . $_GET['enroll_no'] . "'";
} else if (isset($_GET['studentId'])) {
    $searchQuery = " AND Students.ID = '" . $_GET['studentId'] . "'";
} else {
    $searchQuery = " AND Students.Enrollment_No = '" . $_SESSION['Enrollment_No'] . "'";
}

$student = $conn->query("SELECT  Universities.Name as uni_name,Universities.Logo,Students.Exam, Students.ID,Students.Enrollment_No,Students.University_ID,Students.Duration,Sub_Courses.Min_Duration,Students.Course_ID ,Students.Sub_Course_ID , CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS stu_name,Students.Father_Name,Courses.Name as program_Type, Sub_Courses.Name as course,Modes.Name as mode, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Sessions.Exam_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course FROM Students LEFT JOIN Modes on Students.University_ID=Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID INNER JOIN Universities ON Universities.ID = Students.University_ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE 1=1 $searchQuery LIMIT 1");
$Students_temps = [];
if ($student->num_rows > 0) {
    $Students_temps = $student->fetch_assoc();
    //  ECHO $_SESSION['Role']; die;
    // if ($Students_temps['Exam'] == 1 && (isset($_SESSION['Role']) && $_SESSION['Role'] == 'Student')) {
    //     echo json_encode(['status' => false, 'msg' => 'Result not published yet!']);
    //     die();
    // }

    $result_test = "";
    $semQuery = '';
    $category_type = '';
    $due_val = '';
    $obt_query = "";

    if (isset($_GET['year_sem'])) {
        $sem = $_GET['year_sem'];
    } else {
        $sem = $Students_temps['Duration'];
        // $sem = 1;
    }
    $due_val = $sem;
    $semQuery = ' AND s.Semester=' . $sem;


    $total_subject = totalUloadedSubjectsFunc($conn, $Students_temps['ID'], $due_val, "");
    $total_subject_count = count($total_subject);
    $getDataSQL = $conn->query("SELECT Paper_Type,s.Name as subject_name,m.exam_month,m.exam_year,m.status, m.remarks, s.Code,s.Max_Marks, s.Min_Marks, m.obt_marks,m.remarks,m.obt_marks_ext,m.obt_marks_int From marksheets AS m LEFT JOIN Syllabi AS s ON m.subject_id = s.ID WHERE m.enrollment_no = '" . $Students_temps['Enrollment_No'] . "' AND s.Course_ID = " . $Students_temps['Course_ID'] . " AND  s.Sub_Course_ID = " . $Students_temps['Sub_Course_ID'] . "   $semQuery $obt_query ORDER BY m.paper_code");

    if ($getDataSQL->num_rows == 0) {
        echo json_encode(['status' => false, 'msg' => 'Result Not Published Yet.']);
        die;
    }


    $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = " . $Students_temps['ID'] . " AND Type = 'Photo'");
    if ($photo->num_rows > 0) {
        $photo = $photo->fetch_assoc();
        $Students_temps['Photo'] = $photo['Location'];
    }
    $exam_date = $conn->query("SELECT m.exam_month,m.exam_year FROM marksheets AS m LEFT JOIN Syllabi AS s ON m.subject_id = s.ID WHERE m.enrollment_no = '" . $Students_temps['Enrollment_No'] . "' AND s.Course_ID = " . $Students_temps['Course_ID'] . "  AND  s.Sub_Course_ID = " . $Students_temps['Sub_Course_ID'] . " $semQuery  GROUP BY m.enrollment_no");
    if ($exam_date->num_rows > 0) {
        $examArr = $exam_date->fetch_assoc();
        if (!empty($examArr['exam_month']) || !empty($examArr['exam_year'])) {
            $Students_temps['stu_exam_session'] = ucwords($examArr['exam_month']) . '-' . $examArr['exam_year'];
        } else {
            $Students_temps['stu_exam_session'] = ucwords($Students_temps['Exam_Session']);
        }
    } else {
        $Students_temps['stu_exam_session'] = "";
    }

    $total_obt = 0;
    $total_max = 0;
    $result_status = [];
    $total_obt_marks = [];
    $obt_marks_ab_int = 0;
    $obt_marks_ab_ext = 0;

    while ($getDataArr = $getDataSQL->fetch_assoc()) {
        
        $result_status[] = $getDataArr['status'];
        $total_max = $total_max + $getDataArr['Min_Marks'] + $getDataArr['Max_Marks'];
        $total_obt_marks[] = $getDataArr['obt_marks'];
        if(is_null($getDataArr['obt_marks_int']) || $getDataArr['obt_marks_int']==""){
            $getDataArr['obt_marks_int']="-";
        }
        if(is_null($getDataArr['obt_marks_ext']) || $getDataArr['obt_marks_ext']==""){
            $getDataArr['obt_marks_ext']="-";
        }
        $obt_int[] = $getDataArr['obt_marks_int'];
        $obt_ext[] = $getDataArr['obt_marks_ext'];
        $getDataArr['total_max_marks'] =   $getDataArr['Min_Marks'] + $getDataArr['Max_Marks'];
        $Students_temps['marks'][] = $getDataArr;
    }
    $Students_temps['total_max'] = $total_max; // total sum of max_internal marks & max_external_marks
    $Students_temps['total_obt'] = array_sum($total_obt_marks); // total sum of each subjects obt_int + obt_ext marks
    $percentage = 0;
    if ($total_max != 0) {
        $percentage = ($Students_temps['total_obt'] / $total_max) * 100; // percentage of total marks
    }


    if (in_array(0, $result_status)) { // If the student has failed in any subject (status 0), mark overall result as failed
        $Students_temps['result_status'] = 'Fail';
        $Students_temps['percentage'] = '';
    } else {
        $Students_temps['result_status'] = 'Pass';
        $Students_temps['percentage'] = number_format($percentage, 2) . '%';
    }

    $Students_temps['total_max'] = $total_max;
    $Students_temps['total_obt'] = array_sum($total_obt_marks);
    $Students_temps['in_word_marks'] = ucwords(strtolower(numberToWordFunc($Students_temps['total_obt'])));
    $Students_temps['Enrollment_No'] = isset($Students_temps['Enrollment_No']) ? $Students_temps['Enrollment_No'] : '';

    // if the total uploaded subject count == total uploaded internal and external marks count then the result will be published
    if ($total_subject_count !== count($obt_int) || $total_subject_count !== count($obt_ext)) {
        echo json_encode(['status' => false, 'msg' => 'Result not published yet!']);
        die;
    }

    $durMonthYear = "";
    if ($Students_temps['mode'] == "Monthly") {
        $durMonthYear = " Months";
    } elseif ($Students_temps['mode'] == "YEARLY") {
        $durMonthYear = " Years";
    } else {
        $durMonthYear = " Semester";
    }

    $Students_temps['mode_type'] = "Semester";
    $Students_temps['university_name'] = "Vocational Studies";
    $Students_temps['durMonthYear'] = $sem . $typoArr[$sem];
    $Students_temps['duration_val'] = "B. VOC";
    $Students_temps['stu_name'] = strtoupper($Students_temps['stu_name']);
    $Students_temps['Father_Name'] = strtoupper($Students_temps['Father_Name']);
    $Students_temps['uni_name'] = ucwords($Students_temps['uni_name']);
    $Students_temps['uni_logo'] = ERP_URL.$Students_temps['Logo'];

    if (!is_numeric($Students_temps['Min_Duration'])) {
        $Students_temps['Min_Duration'] = json_decode($Students_temps['Min_Duration'], true)[0];
    }
    // echo "<pre>";
    // print_r($Students_temps);die;

    echo json_encode(['status' => true, 'data' => $Students_temps]);

} else {
    echo json_encode(['status' => false, 'msg' => 'Invalid credentials!']);
    die;
}

function totalUloadedSubjectsFunc($conn, $student_id, $duration, $searchSqlQuery = null)
{
    $getStuData = $conn->query("SELECT Course_ID,Sub_Course_ID,Added_For,University_ID  FROM Students WHERE ID = $student_id");
    $stuData = $getStuData->fetch_assoc();
    $sub_count = $conn->query("SELECT Syllabi.Name FROM Syllabi WHERE Course_ID='" . $stuData['Course_ID'] . "' AND Sub_Course_ID='" . $stuData['Sub_Course_ID'] . "' AND Semester = '" . $duration . "' AND University_ID ='" . $stuData['University_ID'] . "' $searchSqlQuery ");
    $subjectArr = [];
    while ($row = $sub_count->fetch_assoc()) {
        $subjectArr[] = $row;
    }
    return $subjectArr;
}



