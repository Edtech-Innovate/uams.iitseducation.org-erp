<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        exit(json_encode(['status' => 400, 'message' => 'Only POST method is allowed.']));
    }

    require '../../includes/db-config.php';
    require '../../includes/db-config-another-miu.php';
    //include '/home/vocational.miu.edu.in/public_html/ams/includes/helpers.php';
    //include '/home/vocational.miuskill.in/public_html/ams/includes/helpers.php';
    include '/home/vocational.miu.edu.in/public_html/ams/includes/helpers.php';
    require '../../app/applications/onboarding/index.php';
    session_start();

    if (empty($_REQUEST['id'])) {
        exit(json_encode(['status' => 400, 'message' => 'Student id is required']));
    }

    $idFrom = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($_REQUEST['id'])));

    // Fetch student from conn1
    $student = $conn->query("SELECT * FROM Students WHERE Students.ID = $idFrom");

    if ($student->num_rows === 0) {
        exit(json_encode(['status' => 400, 'message' => 'Student id is not valid!']));
    }

    $rowStudent = $student->fetch_assoc();

    $centerIdFrom = $rowStudent['Added_For'];

    // Fetch source center
    $centerFrom = $conn->query("SELECT Name, Internal_ID FROM Users WHERE ID = $centerIdFrom");

    if ($centerFrom->num_rows === 0) {
        exit(json_encode(['status' => 400, 'message' => 'Center id is not valid!']));
    }

    $rowCenterFrom = $centerFrom->fetch_assoc();

    // Find destination center (priority Internal_ID)
    if (! empty($rowCenterFrom['Internal_ID'])) {
        $stmt = $conn4->prepare("SELECT ID FROM Users WHERE Internal_ID = ? LIMIT 1");
        $stmt->bind_param("s", $rowCenterFrom['Internal_ID']);
        $stmt->execute();
        $centerTo = $stmt->get_result();
    }

    if (empty($centerTo) || $centerTo->num_rows === 0) {
        $stmt = $conn4->prepare("SELECT ID FROM Users WHERE Name = ? LIMIT 1");
        $stmt->bind_param("s", $rowCenterFrom['Name']);
        $stmt->execute();
        $centerTo = $stmt->get_result();
    }

    if ($centerTo->num_rows === 0) {
        exit(json_encode(['status' => 400, 'message' => 'Center not found!']));
    }

    $rowCenterTo = $centerTo->fetch_assoc();
    $Added_For   = $rowCenterTo['ID'];

    $Admission_Session_ID   = $rowStudent['Admission_Session_ID'];
    $admission_session_from = $conn->query("SELECT Name FROM Admission_Sessions WHERE ID = '" . $Admission_Session_ID . "' AND Status =1");

    $admission_session_from = mysqli_fetch_assoc($admission_session_from);
    if (! isset($admission_session_from) || empty($admission_session_from)) {
        exit(json_encode(['status' => 400, 'message' => 'Admission session not found in source.']));
    }
    $admission_session_from_name = $admission_session_from['Name'];
    $admission_session_from_name = str_replace(" CT","",$admission_session_from_name);
    $month = substr($admission_session_from_name, 0, 3); // Jul
    $year  = substr($admission_session_from_name, -2);   // 25
    // print_r($month);die;
    $University_ID = ($_SESSION['university_id'] == 35) ? 42 : (($_SESSION['university_id'] == 48) ? 41 : 42);

    $admission_session_to = $conn4->query("SELECT * FROM Admission_Sessions WHERE University_ID = $University_ID AND Status =1 AND Name LIKE '" . $month . "%$year%' ");
    // print_r("SELECT * FROM Admission_Sessions WHERE University_ID = $University_ID AND Status =1 AND Name LIKE '" . $month . "%$year%' ");die;
    $admission_session_to = mysqli_fetch_assoc($admission_session_to);
    if (! isset($admission_session_to) || empty($admission_session_to)) {
        exit(json_encode(['status' => 400, 'message' => 'Admission session not found in destination.']));
    }
    
    $admission_type_id = ($_SESSION['university_id'] == 35) ? 6 : (($_SESSION['university_id'] == 48) ? 2 : 1);
    if($admission_session_to['is_ct'] == 1 && $_SESSION['university_id']==47) {
    //     if($admission_session_to['lending_sem'] != $rowStudent['Duration']) {
    //       exit(json_encode(['status' => 400, 'message' => 'Landing semester and duration not match in destination.']));
    //   }
       
       $admission_type_id = 6;
   
    // print_r($rowStudent['Duration']);die;
    $Student_ledger_From_Check = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = '" . $idFrom . "' AND Duration = '".$rowStudent['Duration']."' AND Transaction_ID IS NOT NULL ");
    // if ($Student_ledger_From_Check && $Student_ledger_From_Check->num_rows == 0) {
    //     exit(json_encode(['status' => 400, 'message' => 'Student Fee pending in Glocal.']));
    // }
    }
    
    $admission_rm_check = $conn4->query("SELECT * FROM University_User WHERE University_ID = $University_ID AND User_ID =$Added_For");
    if ($admission_rm_check && $admission_rm_check->num_rows == 0) {
        exit(json_encode(['status' => 400, 'message' => 'RM not assign.']));
    }
    

    ///// sub course
    
     if($_SESSION['university_id']==48){
          $subCourseId = $rowStudent['Sub_Course_ID'];
          $category = $rowStudent['Duration'];
          
        //   print_r("select Internal_ID from Specialization_Internal_Code where Sub_Course_ID = '$subCourseId' and Category = '$category' ");die;
          $internalIdQuery = $conn->query("select Internal_ID from Specialization_Internal_Code where Sub_Course_ID = '$subCourseId' and Category = '$category' ");
          $internalId = $internalIdQuery->fetch_assoc()['Internal_ID'];
        //   print_r($internalId );die;
          if($internalIdQuery->num_rows==0){
              exit(json_encode(['status'=>400, 'message'=>'Internal ID not found for this student sub course and duration']));
          }
          $getToSubCourseId = $conn4->query("select * from Sub_Courses where internal_id = '$internalId'");
          if($getToSubCourseId->num_rows==0){
              exit(json_encode(['status'=>400, 'message'=>'Sub Course Not Found On MIU']));
          }
          $SubCourseData = $getToSubCourseId->fetch_assoc();
        //   print_r($SubCourseData);die;
          $Sub_Course_ID_To['ID'] = $SubCourseData['ID'];
        //   print_r($Sub_Course_ID_To);die;
          $Course_ID = $SubCourseData['Course_ID'];
          $Mode_ID = $SubCourseData['Mode_ID'];
          $rowStudent['Duration'] = 1;
          
    }else{
        $Sub_Course_ID_From = $rowStudent['Sub_Course_ID'];
        $Sub_Course_ID_From = $conn->query("SELECT Name FROM Sub_Courses WHERE ID = '" . $Sub_Course_ID_From . "' ");

        $Sub_Course_ID_From = mysqli_fetch_assoc($Sub_Course_ID_From);

        if (! isset($Sub_Course_ID_From) || empty($Sub_Course_ID_From)) {
            exit(json_encode(['status' => 400, 'message' => 'Course not found in source.']));
        }
        $Sub_Course_ID_From = $Sub_Course_ID_From['Name'];

        $Sub_Course_ID_To = $conn4->query("SELECT ID FROM Sub_Courses WHERE University_ID = $University_ID AND Name = '$Sub_Course_ID_From' ");
        $Sub_Course_ID_To = mysqli_fetch_assoc($Sub_Course_ID_To);

        if (! isset($Sub_Course_ID_To) || empty($Sub_Course_ID_To)) {
            exit(json_encode(['status' => 400, 'message' => 'Course not found in destination.']));
        }
    }
    // print_r("SELECT Sub_Course_ID FROM Syllabi WHERE Sub_Course_ID = ".$Sub_Course_ID_To['ID']);die;
    // toc not available
    // $SyllabiFrom = $conn4->query("SELECT Sub_Course_ID FROM Syllabi WHERE Sub_Course_ID = ".$Sub_Course_ID_To['ID']);
    
    // if ($SyllabiFrom->num_rows === 0) {
    //     exit(json_encode(['status' => 400, 'message' => 'TOC not available!']));
    // }
    
    // print_r($rowStudent);die;

    $Added_By                = isset($Added_For) ? $Added_For : '';
    $Unique_ID_From           = $rowStudent['Unique_ID'];
    $Enrollment_No           = $rowStudent['Enrollment_No'];
    $OA_Number               = null;
    $Roll_No                 = null;
    $Admission_Duration      = isset($rowStudent['Admission_Duration']) ? $rowStudent['Admission_Duration'] : 1; 
    $University_ID           = $University_ID;
    $Admission_Type_ID       = $admission_type_id;
    $Admission_Session_ID    = isset($admission_session_to['ID']) ? $admission_session_to['ID'] : '';
    $Course_ID               = ($_SESSION['university_id'] == 35) ? 6 : (($_SESSION['university_id'] == 48) ? 2 : 6);
    $Sub_Course_ID           = isset($Sub_Course_ID_To['ID']) ? $Sub_Course_ID_To['ID'] : '';
    $Mode_ID                 = ($_SESSION['university_id'] == 35) ? 4 : (($_SESSION['university_id'] == 48) ? 2 : 6);
    $Duration                = $rowStudent['Duration'];
    $First_Name  = mysqli_real_escape_string($conn4, $rowStudent['First_Name']);
    $Middle_Name = mysqli_real_escape_string($conn4, $rowStudent['Middle_Name']);
    $Last_Name   = mysqli_real_escape_string($conn4, $rowStudent['Last_Name']);
    $Father_Name = mysqli_real_escape_string($conn4, $rowStudent['Father_Name']);
    $Mother_Name = mysqli_real_escape_string($conn4, $rowStudent['Mother_Name']);
    $Email       = mysqli_real_escape_string($conn4, $rowStudent['Email']);
    $Alternate_Email         = $rowStudent['Alternate_Email'];
    $Contact                 = $rowStudent['Contact'];
    $Alternate_Contact       = $rowStudent['Alternate_Contact'];
    $DOB                     = $rowStudent['DOB'];
    $ABC_ID                  = $rowStudent['ABC_ID'];
    $Category                = $rowStudent['Category'];
    $Aadhar_Number           = $rowStudent['Aadhar_Number'];
    $Gender                  = $rowStudent['Gender'];
    $Nationality             = $rowStudent['Nationality'];
    $Employement_Status      = $rowStudent['Employement_Status'];
    $Marital_Status          = $rowStudent['Marital_Status'];
    $Religion                = $rowStudent['Religion'];
    $Status                  = $rowStudent['Status'];
    $Step                    = $rowStudent['Step'];
    $ID_Card                 = $rowStudent['ID_Card'];
    $Admit_Card              = $rowStudent['Admit_Card'];
    $Exam                    = $rowStudent['Exam'];
    $form_completion_date    = ! empty($rowStudent['form_completion_date']) ? $rowStudent['form_completion_date'] : null;
    $Process_By_Center       = ! empty($rowStudent['Process_By_Center']) ? $rowStudent['Process_By_Center'] : null;
    $Payment_Received        = ! empty($rowStudent['Payment_Received']) ? $rowStudent['Payment_Received'] : null;
    $Document_Verified       = null;
    $Processed_To_University = null;
    $Created_At              = ! empty($rowStudent['Created_At']) ? $rowStudent['Created_At'] : null;
    $Updated_At              = ! empty($rowStudent['Updated_At']) ? $rowStudent['Updated_At'] : null;

    $addressArr = json_decode($rowStudent['Address'] ?? '', true) ?? null;
    $Address    = json_encode($addressArr, JSON_UNESCAPED_UNICODE);
    
    $checkDuplicate = $conn4->query("
        SELECT ID FROM Students 
        WHERE First_Name = '$First_Name'
        AND Father_Name = '$Father_Name'
        AND DOB = '$DOB'
        AND Aadhar_Number = '$Aadhar_Number'
    ");
    
    if ($checkDuplicate && $checkDuplicate->num_rows > 0) {
        exit(json_encode([
            'status' => 400,
            'message' => 'Student already transferred'
        ]));
    }
    
    $query = $conn4->query("
    INSERT INTO Students (
        Added_By, Added_For, Roll_No,
        University_ID, Admission_Type_ID, Admission_Session_ID,Admission_Duration,
        Course_ID, Sub_Course_ID, Mode_ID, Duration,
        First_Name, Middle_Name, Last_Name,
        Father_Name, Mother_Name, Email, Alternate_Email,
        Contact, Alternate_Contact, DOB, ABC_ID, Category,
        Aadhar_Number, Gender, Nationality, Employement_Status,
        Marital_Status, Religion, Address, Status, Step,
        ID_Card, Admit_Card, Exam, form_completion_date,
        Process_By_Center, Payment_Received, Document_Verified,
        Processed_To_University,Is_Transferred, Created_At, Updated_At
    ) VALUES (
        '$Added_By', '$Added_For', '$OA_Number',
        $University_ID, $Admission_Type_ID, $Admission_Session_ID,$Admission_Duration,
        $Course_ID, $Sub_Course_ID, $Mode_ID, $Duration,
        '$First_Name', '$Middle_Name', '$Last_Name',
        '$Father_Name', '$Mother_Name', '$Email', '$Alternate_Email',
        '$Contact', '$Alternate_Contact', '$DOB', '$ABC_ID', '$Category',
        '$Aadhar_Number', '$Gender', '$Nationality', '$Employement_Status',
        '$Marital_Status', '$Religion', '$Address', '$Status', '$Step',
        '$ID_Card', '$Admit_Card', '$Exam',
        " . (! empty($form_completion_date) ? "'$form_completion_date'" : "NULL") . ",
        " . (! empty($Process_By_Center) ? "'$Process_By_Center'" : "NULL") . ",
        " . (! empty($Payment_Received) ? "'$Payment_Received'" : "NULL") . ",
        " . (! empty($Document_Verified) ? "'$Document_Verified'" : "NULL") . ",
        " . (! empty($Processed_To_University) ? "'$Processed_To_University'" : "NULL") . ",'$Unique_ID_From',
         '$Created_At', '$Updated_At'
    )
");

    $newStudentId = $conn4->insert_id;

    $has_unique_student_id = $conn4->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = " . $University_ID . " AND Has_Unique_StudentID = 1");
    if ($has_unique_student_id->num_rows > 0) {
        $has_unique_student_id = $has_unique_student_id->fetch_assoc();
        $suffix                = $has_unique_student_id['ID_Suffix'];
        $characters            = $has_unique_student_id['Max_Character'];
        $unique_id             = generateStudentID($conn, $suffix, $characters, $_SESSION['university_id']);
        $conn4->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $newStudentId");

    }

    //$conn4->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $newStudentId");
    // print_r($newStudentId);die;
    // print_r(generateStudentLedger($conn4, $newStudentId));die;
    //generateStudentLedgerForTransfer($conn4, $newStudentId);
    transferAcademicDetails($conn,$conn4,$idFrom,$newStudentId);
    $Student_ledger_From = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = '" . $idFrom . "' ");
    if ($Student_ledger_From && $Student_ledger_From->num_rows > 0) {

        while ($row = $Student_ledger_From->fetch_assoc()) {
            $fee = $row['Fee'];
            
            if($row['Type'] == 1){
                $key = ($_SESSION['university_id'] == 47) ? 1 : (($_SESSION['university_id'] == 48) ? 2 : 1);
                $fee = json_encode([$key => $fee]);
            } else {
                if (is_numeric($fee)) {
                    $fee = json_encode(['Paid' => $fee]);
                } else {
                    $fee = $fee;
                }
            }
            
            $ledgerDurationRaw = $row['Duration'];
            preg_match('/\d+/', $ledgerDurationRaw, $m);
            $ledgerDuration = isset($m[0]) ? (int)$m[0] : 1;
        $query = $conn4->query("INSERT INTO Student_Ledgers (Date,Student_ID,Duration,University_ID,Type,Source,Transaction_ID,Fee,Fee_Without_Sharing,Status,Discount,Premium,Created_At,Updated_At)
           VALUES ('{$row['Date']}','$newStudentId','$ledgerDuration','{$University_ID}','{$row['Type']}','{$row['Source']}','{$row['Transaction_ID']}','$fee',null,1,0,0,'{$row['Created_At']}','{$row['Updated_At']}')");
        }
    }
    
    $Student_document_From = $conn->query("SELECT * FROM Student_Documents WHERE Student_ID = '" . $idFrom. "'");
    if ($Student_document_From && $Student_document_From->num_rows > 0) {
       while ($rowDoc = $Student_document_From->fetch_assoc()) {
          $query = $conn4->query("INSERT INTO Student_Documents (Student_ID,Type,Location) VALUES ('$newStudentId','{$rowDoc['Type']}','{$rowDoc['Location']}')");
          $imageName = $rowDoc['Location']; // e.g. /uploads/marksheet/high_school/203_High_Marksheet_0.jpg

          $folderPath = substr($imageName, 0, strrpos($imageName, '/'));

          //$destDir = "/home/vocational.miu.edu.in/public_html/ams".$folderPath;
          //$destDir = "/home/vocational.miuskill.in/public_html/ams".$folderPath;
          $destDir = "/home/vocational.miu.edu.in/public_html/ams".$folderPath;
          $srcFile = "/home/iitseducation.org/uams.iitseducation.org".$imageName;
          
          
//           echo "USER: " . exec('whoami') . "<br>";
// echo "BASE: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
// echo "REAL: " . realpath($_SERVER['DOCUMENT_ROOT']) . "<br>";
// echo "OPEN_BASEDIR: " . ini_get('open_basedir') . "<br>";
          
        //   echo "SRC readable: " . (is_readable($srcFile) ? 'YES' : 'NO') . "\n";
          
        //   echo "DEST writable: " . (is_writable($destDir) ? 'YES' : 'NO') . "\n";
          
          $destDir = rtrim($destDir, '/') . '/';
          
         $cmd = "cp " . escapeshellarg($srcFile) . " " . escapeshellarg($destDir);
         
        // echo "<pre>";print_r($cmd);die;
         
         exec($cmd, $output, $status);

            // if ($status === 0) {
            //     echo "Copy successful";die;
            // } else {
            //     echo "Copy failed";
            //     echo "<pre>" . implode("\n", $output) . "</pre>";die;
            // }
        //   if (file_exists($srcFile)) {
        //       unlink($srcFile);
        //   } 
      }

    }

    $conn->query("UPDATE Students SET Is_Transferred = 'MIU' WHERE ID = $idFrom");
    exit(json_encode(['status' => 200, 'message' => 'Student successfully transferred']));

} catch (Exception $e) {
    exit(json_encode(['status' => 400, 'message' => $e->getMessage() . $e->getLine()]));
}

function transferAcademicDetails($conn,$conn4,$oldId,$newId){
    $insert = "";
    if($conn && $conn4 && $oldId && $newId){
        $getAllAcademicDetails = $conn->query("select * from Student_Academics where Student_ID=$oldId");
        while($academicData = $getAllAcademicDetails->fetch_assoc()){
            $type = $academicData['Type'];
            $year = $academicData['Year'];
            $subject = $academicData['Subject'];
            $rollno = $academicData['Roll_No'];
            $board = $academicData['Board/Institute'];
            $obtainMarks = $academicData['Marks_Obtained'];
            $maxMarks = $academicData['Max_Marks'];
            $totalMarks = $academicData['Total_Marks'];
            $insertDataQuery = "INSERT INTO Student_Academics (`Student_ID`,`Type`,`Year`,`Subject`,`Roll_No`,`Board/Institute`,`Marks_Obtained`,`Max_Marks`,`Total_Marks`) values ('$newId','$type','$year','$subject','$rollno','$board','$obtainMarks','$maxMarks','$totalMarks')"; 
            $insert = $conn4->query($insertDataQuery);
        }
    }
    return $insert;
}