<?php
  ini_set('display_errors', 1); 

  if(isset($_POST['name'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);

    if(empty($name) ){
      echo json_encode(['status'=>403, 'message'=>'Name field is mandatory!']);
      exit();
    }
   
    
    $check = $conn->query("SELECT ID FROM subject_category WHERE (name like '$name')");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>$name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("INSERT INTO `subject_category`(`name`) VALUES ('$name')");
    if($add){
        echo json_encode(['status'=>200, 'message'=>$name.' added successlly!']);
    }else{
      echo json_encode(['status'=>4000, 'message'=>'Not Added!']);
    }
  }
