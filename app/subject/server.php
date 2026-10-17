<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
ini_set('display_errors',1);
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'] ?? "all";// Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = 'Syllabi.'.$_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}


$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Syllabi.ID DESC";
}
$query='';
// Admin Query
$query = " AND Syllabi.University_ID = ".$_SESSION['university_id'];


## Search 
$searchQuery = " "." AND Syllabi.University_ID = ".$_SESSION['university_id'];
if($searchValue != ''){
  $searchQuery = " AND (Syllabi.Name like '%".$searchValue."%' OR Syllabi.Code like '%".$searchValue."%' OR Syllabi.Paper_Type like '%".$searchValue."%' )";
}
$filterBysubCourse = "";
if (isset($_SESSION['subCourseFilter'])) {
  $searchQuery .= $_SESSION['subCourseFilter'];
}

$filterByDuration = "";
if (isset($_SESSION['durationFilter'])) {
  $searchQuery.= $_SESSION['durationFilter'];
}


## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Syllabi WHERE ID IS NOT NULL $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];
     if ($rowperpage == "all") {
        $rowperpage = $totalRecords;
      }

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Syllabi.ID) as filtered FROM Syllabi LEFT JOIN Courses ON Syllabi.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE 1=1  $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];
## Fetch records
$result_record = "SELECT Syllabi.Name,Syllabi.Syllabus,Syllabi.Sub_Course_ID,Syllabi.Semester,Syllabi.University_ID, Syllabi.ID,Credit,Syllabi.Code, Paper_Type, Semester,CONCAT(Min_Marks,'/',Max_Marks) as marks , CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as course_name FROM Syllabi LEFT JOIN Courses ON Syllabi.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE 1=1  $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
      "Code" => $row["Code"],
      "Name" => $row["Name"],
      "course_name" => $row["course_name"],
      "Sub_Course_ID" => $row["Sub_Course_ID"],
      "Paper_Type"=>$row["Paper_Type"],
      "Marks" => $row["marks"],
      "Credit"  => $row["Credit"],
      "ID"      => $row["ID"],
      "Semester"      => $row["Semester"],
      "uni_id"      => $row["University_ID"],
      "files"      => $row["Syllabus"],

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
