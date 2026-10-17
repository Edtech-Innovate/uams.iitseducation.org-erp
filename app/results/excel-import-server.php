<?php
if (isset($_FILES['file'])) {
  require '../../includes/db-config.php';

  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');
  session_start();

  ini_set('max_execution_time', 1400);
  $export_data = array();
  $header = array('Course', 'Sub-Course', 'Enrollment Number', 'Subject Code', 'Obtained External Marks', 'Obtained Internal Marks', 'Semester', 'Exam Month', 'Exam Year', 'Paper Code', 'Remark');
  $export_data[] = $header;
  $mimes = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

  if (in_array($_FILES["file"]["type"], $mimes)) {
    $uploadFilePath = basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);
    $reader = new SpreadsheetReader($uploadFilePath);
    $totalSheet = count($reader->sheets());
    for ($i = 0; $i < $totalSheet; $i++) {
      $reader->ChangeSheet($i);

      foreach ($reader as $row) {
     
        $course = mysqli_real_escape_string($conn, trim($row[0]));
        $sub_course = mysqli_real_escape_string($conn, trim($row[1]));
        $enrollment = mysqli_real_escape_string($conn, $row[2]);
        $subject_code = mysqli_real_escape_string($conn, trim($row[3]));
        $obt_ext_marks = (isset($row[4])) ? mysqli_real_escape_string($conn, trim($row[4])) : NULL;
        $obt_int_marks = (isset($row[5])) ? mysqli_real_escape_string($conn, trim($row[5])) : NULL;
        $semester = mysqli_real_escape_string($conn, $row[6]);
        $exam_month = isset($row[7]) ? mysqli_real_escape_string($conn, $row[7]) : '';
        $exam_year = isset($row[8]) ? intval($row[8]) : '';
        $paper_code = mysqli_real_escape_string($conn, $row[9]);
        $created_at = date("Y-m-d:H:i:s");

        // if (is_string($obt_ext_marks) && strtolower($obt_ext_marks) !== "ab") {
        //     $export_data[] = array_merge($row, ['Enter valid Obtained External Marks!']);
        //     continue;
        // }

        // if (is_string($obt_int_marks) && strtolower($obt_int_marks) !== "ab") {
        //     $export_data[] = array_merge($row, ['Enter valid Obtained Internal Marks!']);
        //     continue;
        // }

        $course = $conn->query("SELECT ID, University_ID FROM Courses WHERE University_ID = " . $_SESSION['university_id'] . " AND (Name = '$course' OR Short_Name = '$course')");
        if ($course->num_rows == 0) {
          $export_data[] = array_merge($row, ['Course not found!']);
          continue;
        }

        $course_ids = array();
        while ($course_id = $course->fetch_assoc()) {
          $course_ids[] = $course_id['ID'];
        }

        $sub_course = $conn->query("SELECT ID, Course_ID, Name FROM Sub_Courses WHERE University_ID = " . $_SESSION['university_id'] . " AND Name = '$sub_course' AND Course_ID IN (" . implode(',', $course_ids) . ")");
        if ($sub_course->num_rows == 0) {
          $export_data[] = array_merge($row, ['Sub-Course not found!']);
          continue;
        }

        $sub_course = $sub_course->fetch_assoc();
        $course_id = $sub_course['Course_ID'];
        $sub_course_id = $sub_course['ID'];
        $sub_course_name = $sub_course['Name'];

        if (empty($obt_int_marks) || $obt_int_marks == '' || $obt_int_marks == 0) {
          $checkres = $conn->query("SELECT marksheets.obt_marks_int FROM marksheets LEFT JOIN Syllabi on subject_id = Syllabi.ID  WHERE enrollment_no ='$enrollment' AND University_ID = " . $_SESSION['university_id'] . " AND Code = '$subject_code' AND Syllabi.Semester =  '" . $semester . "'  AND Course_ID = $course_id AND Sub_Course_ID = $sub_course_id");
          if ($checkres->num_rows > 0) {
            $obt_int_marks = $checkres->fetch_assoc()['obt_marks_int'];
          }
        }


        $check_enrollment = $conn->query("SELECT ID FROM Students WHERE Enrollment_No = '$enrollment'");
        if ($check_enrollment->num_rows == 0) {
          $export_data[] = array_merge($row, ['Invalid Enrollment No.']);
          continue;
        }


        $check_student_sub_course = $conn->query("SELECT Users.Code,Users.ID, Role FROM Students LEFT JOIN Users on Added_For= Users.ID WHERE Enrollment_No = '$enrollment' AND Sub_Course_ID = '$sub_course_id' AND Course_ID = '$course_id'");
        if ($check_student_sub_course->num_rows == 0) {
          $export_data[] = array_merge($row, ['This Sub-Course is not assigned to this Student.']);
          continue;
        }

        $subjects = $conn->query("SELECT ID, Min_Marks,Max_Marks,University_ID FROM Syllabi WHERE University_ID = " . $_SESSION['university_id'] . " AND Code = '$subject_code' AND Semester = '" . $semester . "'  AND Course_ID = $course_id AND Sub_Course_ID = $sub_course_id ");
        if ($subjects->num_rows == 0) {
          $export_data[] = array_merge($row, ['Subject not found!']);
          continue;
        }

        $subject_ids = array();
        $subject_arr = $subjects->fetch_assoc();
        $subject_ids = $subject_arr['ID'];
            

        $marksArr = getMarksdetails($conn, $subject_ids, $obt_ext_marks, $obt_int_marks);
 
        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no ='$enrollment' AND subject_id='$subject_ids' ");

        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE `marksheets` SET  `obt_marks_ext` = '" . $marksArr['add_obt_ext_marks'] . "',  `obt_marks_int` = '" .$marksArr['add_obt_int_marks'] . "',   `obt_marks` = '" . $marksArr['total_obt'] . "',  `remarks` = '" . $marksArr['remarks']. "',`status` = '" . $marksArr['status']. "',  `exam_month` = '" . $exam_month . "', `exam_year` = " . $exam_year . ", `updated_at` = '" . $created_at . "'
              WHERE enrollment_no = '" . $enrollment . "' AND subject_id = '" . $subject_ids . "' ");
          if ($update) {
            $export_data[] = array_merge($row, ['Result Updated successfully!']);
          } else {
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        } else {
          $add = $conn->query("INSERT INTO `marksheets`(`enrollment_no`, `subject_id`, `obt_marks_ext`, `obt_marks_int`, `obt_marks`, `remarks`, `status`, `exam_month`,`exam_year`,`created_at`,`paper_code`) VALUES ('" . $enrollment . "', " . $subject_ids . ", '" . $marksArr['add_obt_ext_marks'] . "','" . $marksArr['add_obt_int_marks'] . "', '" . $marksArr['total_obt'] . "', '" .$marksArr['remarks'] . "','".$marksArr['status']."', '" . $exam_month . "', $exam_year,'$created_at','" . $paper_code . "')");
          if ($add) {
            $export_data[] = array_merge($row, ['Result added successfully!']);
          } else {
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        }
      }
    }
    //  die;
    unlink($uploadFilePath);
    $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Result Status ' . date('h m s') . '.xlsx');
  }
}

function getMarksdetails($conn, $subject_id, $obt_ext_marks, $obt_int_marks)
{
  $remarks = "Fail";
  $obt_ext_final_marks = $obt_ext_marks;
  $obt_int_final_marks = $obt_int_marks;
  $status = $total_obt = $int_obt_int_marks = $ext_obt_int_marks = 0;
  
  $get_min_max_sql = $conn->query("SELECT Min_Marks, Max_Marks FROM Syllabi where ID = $subject_id");
  $row = $get_min_max_sql->fetch_assoc();
  $min_marks = (int) $row['Min_Marks']; // internal max value
  $max_marks = (int) $row['Max_Marks']; // external max value

  $min_percent = 40;
  $int_min_value = $min_marks * $min_percent / 100; // internal min value
  $ext_min_value = $max_marks * $min_percent / 100; // external min value
  $total_min_value = ($min_marks + $max_marks) * $min_percent / 100; // total min value with 40% 


  // for the obtain external marks
  if (is_null($obt_ext_marks) || $obt_ext_marks === "") {
    $ext_obt_int_marks = 0;
  } else if (is_string($obt_ext_marks) && strtolower($obt_ext_marks) === "ab") {
    $ext_obt_int_marks = 0;
  } else if ((is_numeric($obt_ext_marks) || $obt_ext_marks === 0) && !is_null($obt_ext_marks) && $obt_ext_marks != "") {
    $ext_obt_int_marks = $obt_ext_marks;
      if (is_numeric($obt_ext_marks) && fmod($obt_ext_marks, 1) !== 0.0 ) {
        $obt_ext_final_marks = round($obt_ext_marks);
        $ext_obt_int_marks = round($obt_ext_marks);
      }
    
  }
 
  // for the obtain internal marks
  if (is_null($obt_int_marks) || $obt_int_marks === "") {
    $int_obt_int_marks = 0;
  } else if (is_string($obt_int_marks) && strtolower($obt_int_marks) === "ab") {
    $int_obt_int_marks = 0;
  } else if ((is_numeric($obt_int_marks) || $obt_int_marks === 0) && !is_null($obt_int_marks) && $obt_ext_marks != "") {
    $int_obt_int_marks = $obt_int_marks;
    if (is_float($obt_int_marks) && fmod($obt_int_marks, 1) !== 0.0 ) {
    $obt_int_final_marks = round($obt_int_marks);
    $int_obt_int_marks = round($obt_int_marks);
  }
  }

  $total_obt = $int_obt_int_marks + $ext_obt_int_marks; // total obt marks

  // find the status if the internal marks is less then min value
  // ($int_obt_int_marks >= $int_min_value && $ext_obt_int_marks >= $ext_min_value && $total_obt >= $total_min_value)
  if ($int_obt_int_marks >= $int_min_value && $ext_obt_int_marks >= $ext_min_value) {
    $remarks = "Pass";
    $status = 1;
  } 

  $data = array(
     'total_obt' => $total_obt,
     'add_obt_ext_marks' => $obt_ext_final_marks,
     'add_obt_int_marks' => $obt_int_final_marks,
     'remarks' => $remarks,
     'status' => $status,
  );
  return $data;
}