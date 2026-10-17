<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
  if(isset($_POST['name']) && isset($_POST['course']) && isset($_POST['university_id']) && isset($_POST['scheme']) || isset($_POST['mode']) && isset($_POST['min_duration']) && isset($_POST['max_duration']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    if(empty($name) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    $update = $conn->query("UPDATE `subject_category` SET `name` = '$name' WHERE ID = $id");
    if($update  ){
        echo json_encode(['status'=>200, 'message'=>$short_name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Not Updated!']);
    }
  }
