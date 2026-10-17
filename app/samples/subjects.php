<?php
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  session_start();
  $column = "";
    if($_SESSION['university_id']==39){
        $column = " Subject Category";
    }
  $header[] = array('Scheme', 'Course', 'Sub-Course', 'Semester', 'Subject Code', 'Subject Name', 'Type (Theory/Practical)', 'Credit','Minimum Marks', 'Maximum Marks', $column);

  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('Subjects Sample.xlsx');
