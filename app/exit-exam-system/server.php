<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Students.ID DESC";
}

$role_query = str_replace('{{ table }}', 'Students', isset($_SESSION['RoleQuery'])?$_SESSION['RoleQuery']:'');
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$filterByUsers = "";
if (isset($_SESSION['usersFilter'])) {
  $filterByUsers = $_SESSION['usersFilter'];
}

$filterBysubCourse = "";
if (isset($_SESSION['subCourseFilter'])) {
  $filterBysubCourse = $_SESSION['subCourseFilter'];
}

$filterBySubCourses = "";
if (isset($_SESSION['filterBySubCourses'])) {
  $filterBySubCourses = $_SESSION['filterBySubCourses'];
}


$filterByExamStatus = "";
if (isset($_SESSION['filterByExamStatus'])) {
  $filterByExamStatus = $_SESSION['filterByExamStatus'];
}
## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Students.First_Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Name like '%".$searchValue."%' OR Sub_Courses.Name like '%".$searchValue."%' OR Students.Enrollment_No like '%".$searchValue."%'  OR Students.Unique_ID like '%".$searchValue."%'  OR Students.Duration like '%".$searchValue."%' OR Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%')";
}
$userQuery ='';
if($_SESSION['Role']=="Center" || $_SESSION['Role']=="Sub-Center"){
  $userQuery = " AND Added_For = ".$_SESSION['ID'];
}

$universityId = $_SESSION['university_id'];
$filterByUniversity = " AND Students.University_ID =$universityId AND Enrollment_No IS NOT NULL";
$searchQuery .= $filterByUniversity. $filterBysubCourse.$filterBySubCourses .$filterByUsers.$userQuery.$filterByExamStatus.$role_query;


## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(Students.ID) as allcount  FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For =  Users.ID  WHERE 1=1 $searchQuery");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Students.ID) as filtered FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For =  Users.ID  WHERE 1=1  $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Students.ID,Students.Payment_Received,Students.exam_exit_request_date,Students.exam_exit_status,Students.Duration, Students.Enrollment_No,Students.Unique_ID, Students.Mother_Name, Students.Father_Name,  CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS full_name, Sub_Courses.Name AS sub_course_name,Courses.Short_Name as course_short_name, Users.Code, Users.Name as user_name  FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For=  Users.ID  WHERE 1=1  $searchQuery  $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();
$exam_exit_status = array(
  '0' => '',
  '1' => 'Exit C',
  '2' => 'Exit D',
  '3' => 'Exit AD',
  '4' => 'Exit B',
  '5' => 'Enrolled',
  '6'=>'Drop Out',
);
while ($row = mysqli_fetch_assoc($empRecords)) {
  $enroll = $row['Enrollment_No'];
  $duration = $row['Duration'];
  $check = $conn->query("SELECT * FROM Syllabi as s LEFT JOIN marksheets AS m on s.ID = m.subject_id WHERE enrollment_no = '$enroll' AND Semester = '$duration'");
  if ($check->num_rows == 0) {
    $result_uploaded_status = 0;
  } else {
    $result_uploaded_status = 1;
  }

  $exit_status = '';
  if (!empty($row['Payment_Received']) && (empty($row['Enrollment_No']) || $row['Enrollment_No'] === NULL)) {
    $exit_status = 'Active';
  } elseif (!empty($row['Enrollment_No']) && (empty($row['exam_exit_status']) || ($row['exam_exit_status']==5))) {
      $exit_status = 'Enrolled';
  } else if(!empty($row['exam_exit_status'])) {
      $exit_status =  $exam_exit_status[$row['exam_exit_status']] ?? '';
  } 
  
    $data[] = array( 
      "ID"=> $row['ID'],
      "Unique_ID"=> $row['Unique_ID'],
      "full_name" => $row['full_name'],
      "sub_course_name" => ucwords(strtolower($row['sub_course_name'])),
      "Enrollment_No" => trim($row['Enrollment_No']),
      "course_short_name" => $row['course_short_name'],
      "Duration"      => $row["Duration"],
      "Mother_Name"      => $row["Mother_Name"],
      "Father_Name"      => $row["Father_Name"],
      "user_name"      => $row["user_name"].'('.$row['Code'].')',
      "result_uploaded_status"=>$result_uploaded_status,
      "exam_exit_status" => $row['exam_exit_status'],
      "exit_status" =>$exit_status,
      "exam_exit_request_date"=>$row['exam_exit_request_date'],
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
