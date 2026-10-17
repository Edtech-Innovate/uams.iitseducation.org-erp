<?php
  if(isset($_POST['id']) && $_POST['exam_status']){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $exam_status = mysqli_real_escape_string($conn, $_POST['exam_status']);

    if(empty($exam_status)){
      echo json_encode(['status'=>400, 'message'=>'Exit Exam Status is required.']);
      exit();
    }

    $update = $conn->query("UPDATE Students SET exam_exit_status = '$exam_status' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Exit Exam Status updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
