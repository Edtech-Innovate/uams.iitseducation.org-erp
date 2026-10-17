<?php
  if(isset($_POST['id']) && $_POST['deb_id']){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $deb_id = mysqli_real_escape_string($conn, $_POST['deb_id']);
    $select = $conn->query("SELECT * FROM Students WHERE DEB_ID = '$deb_id' ");
    if($select->num_rows > 1){
      echo json_encode(['status'=>400, 'message'=>'This DEB ID is Already Exist!']);
      die;
    }


    if(empty($deb_id)){
      echo json_encode(['status'=>400, 'message'=>'ABC ID is required.']);
      exit();
    }

    $update = $conn->query("UPDATE Students SET DEB_ID = '$deb_id' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'DEB ID updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>