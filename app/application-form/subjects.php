<?php
  ini_set('display_errors', 1); 
require '../../includes/db-config.php';
    session_start();
  if(isset($_GET['university_id']) && isset($_GET['admission_type_id']) && $_SESSION['university_id']==39 && isset($_GET['sub_course_id']) ){
      $ids = [];
    if($_REQUEST['id']>0  ){
        $studentId = $_REQUEST['id'];
        $studentSubjects = $conn->query("select * From student_subjects where student_id=$studentId");
        if($studentSubjects->num_rows>0){
            while($subId = $studentSubjects->fetch_assoc()){
                $ids[] = $subId['subject_id'];
            }
        }
    }
    $university_id = intval($_GET['university_id']);
    $admission_type_id = intval($_GET['admission_type_id']);

    if(empty($admission_type_id)){
      echo '<option value="">Please configure Admission-Type</option>';
      exit();      
    }

    $admission_type = $conn->query("SELECT Name FROM Admission_Types WHERE ID = $admission_type_id");
    if($admission_type->num_rows==0){
      echo '<option value="">Please configure Admission-Type</option>';
      exit();
    }
    $admission_type = mysqli_fetch_assoc($admission_type);
    $admission_type = $admission_type['Name'];

    $subcourseId = $_GET['sub_course_id'];
      $subjects_category = $conn->query("SELECT sc.name AS category_name, s.name AS subject_name, s.ID as subject_id
                                        FROM subject_category sc
                                        JOIN Syllabi s ON sc.id = s.subject_category_id
                                        where s.Sub_Course_ID = $subcourseId
                                        ORDER BY sc.name;");
      $result = []; // Final array

    // Assume $rows is the result of your SQL query
    while ($row = $subjects_category->fetch_assoc()) {
        $category = $row['category_name'];
        $subject = $row['subject_name'];
        $subject_id = $row['subject_id'];
        if (!isset($result[$category])) {
            $result[$category] = [];
        }
    
        $result[$category][$subject_id] = $subject;
    }

    foreach($result as $category_name => $subjects){
        ?>
         <div class="col-md-4 mb-4">
                <div class="category-block">
                    <h5><?= htmlspecialchars($category_name) ?></h5>
                    <?php foreach ($subjects as $subject_id => $subject_name): ?>
                        <div class="form-check">
                            <input class="form-check-input <?= htmlspecialchars($category_name) ?>" type="checkbox" name="subjects[]" value="<?= $subject_id ?>" <?php if(in_array($subject_id,$ids)){ echo "checked";}?> id="subject<?= $subject_id ?>">
                            <label class="form-check-label" for="subject<?= $subject_id ?>">
                                <?= htmlspecialchars($subject_name) ?>
                            </label>
                            <?php if(in_array($subject_id,$ids)){ ?> <i class="uil uil-copy" onclick="copyText('<?= htmlspecialchars($subject_name) ?>')"></i> <?php }?>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php
    }
  }
  
  ?>
  <script>
      function copyText(text) {
          navigator.clipboard.writeText(text);
          alert(text+" Copied To Clipboard");
        }
  </script>
