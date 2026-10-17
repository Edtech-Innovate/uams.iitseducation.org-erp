<?php
//   ini_set('display_errors', 1); 
require '../../includes/db-config.php';
    session_start();
  if(isset($_GET['uid']) && isset($_GET['type'], $_GET['sid']) && $_SESSION['university_id']==39 && $_GET['type']==75){
     
    
    $university_id = intval($_GET['uid']);
    $admission_type_id = intval($_GET['type']);


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
        
    $checkExistsTOCSubject = $conn->query("select * from student_toc_subjects where student_id=".$_GET['sid']);
    $tocSubjects = $checkExistsTOCSubject->fetch_assoc();
    $subjectsArr = $tocSubjects['subjects'];
    ?>
    <style>
        #subjectTable thead tr {
            width: 100% !important;
        }
    </style>
      
<div class="col-md-12">
    <h3>Details of Last Passed Subject *</h3>
</div>

   <div class="col-md-4">
        <div class="form-group form-group-default" aria-required="true">
    <label>Subject Type</label>
    <select id="subjectType" class="form-control" name="subject_type_name">
    <option value="">--SELECT ONE--</option>
    <?php foreach ($result as $type => $list): ?>
        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
    <?php endforeach; ?>
</select>
 </div>
   </div>
    <!-- Subject Type -->


<div class="col-md-4">
    <div class="form-group form-group-default " aria-required="true">
    <label>Subject</label>
    <select id="subjectName" class="form-control" name="subject_id">
    <option value="">--SELECT ONE--</option>
</select>
 </div>
</div>
<!-- Subject -->


<!-- Marks Inputs -->
<div class="col-md-4">
    <div class="form-group form-group-default " aria-required="true">
    <label>Practical Marks</label>
    <input type="number" id="practical" class="form-control" placeholder="Practical Marks">
 </div>
</div>
<div class="col-md-4">
    <div class="form-group form-group-default " aria-required="true">
    <label>Assignment Marks</label>
    <input type="number" id="assignment" class="form-control" placeholder="Assignment Marks">
 </div>
</div>
<div class="col-md-4">
    <div class="form-group form-group-default " aria-required="true">
    <label>Theory Marks</label>
   <input type="number" id="theory" class="form-control" placeholder="Theory Marks">
 </div>
</div>
<div class="col-md-4">
    
<div class="form-group form-group-default " aria-required="true">
    <label>Obtained Marks</label>
   <input type="number" id="obtained" class="form-control" placeholder="Obtained Marks">
 </div>
</div>
<div class="col-md-4">
    <div class="form-group form-group-default " aria-required="true">
    <label>Maximum Marks</label>
    <input type="number" id="maximum" class="form-control" placeholder="Maximum Marks">
 </div>
</div>




<div class="col-md-2">
    
<button id="addSubject" class="btn btn-primary" type="button">+ADD</button>
</div>


<!-- Table -->
<div class="col-md-12">
    <table id="subjectTable" class="table table-responsive">
    <thead>
        <tr>
            <th style="width:20% !important;">Subject Type</th>
            <th style="width:30% !important;">Subject Name</th>
            <th style="width:10% !important;">Practical</th>
            <th style="width:10% !important;">Assignment</th>
            <th style="width:10% !important;">Theory</th>
            <th style="width:10% !important;">Obtained</th>
            <th style="width:10% !important;">Maximum</th>
            <th style="width:10% !important;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if($subjectsArr){
                $subjectsArr = json_decode($subjectsArr,true);
                foreach($subjectsArr as $type=>$data){
                    $count = count($data['subject_id']);
                    for($i=0; $i<$count; $i++){
                        echo "<tr>
                                <td><input type='text' readonly name='student_toc_subject[$type][subject_type][]' value='{$data['subject_type'][$i]}' class='form-control'></td>
                                <td>
                                    <input type='hidden' name='student_toc_subject[$type][subject_id][]' value='{$data['subject_id'][$i]}'>
                                    <input type='text' readonly name='student_toc_subject[$type][subject_name][]' value='{$data['subject_name'][$i]}' class='form-control'>
                                </td>
                                <td><input type='number' name='student_toc_subject[$type][practical][]' value='{$data['practical'][$i]}' class='form-control'></td>
                                <td><input type='number' name='student_toc_subject[$type][assignment][]' value='{$data['assignment'][$i]}' class='form-control'></td>
                                <td><input type='number' name='student_toc_subject[$type][theory][]' value='{$data['theory'][$i]}' class='form-control'></td>
                                <td><input type='number' name='student_toc_subject[$type][obtained][]' value='{$data['obtained'][$i]}' class='form-control'></td>
                                <td><input type='number' name='student_toc_subject[$type][maximum][]' value='{$data['maximum'][$i]}' class='form-control'></td>
                                <td><span class='removeRow btn btn-danger btn-sm'>✖</span></td>
                              </tr>";
                    }
                }
            }
            ?>
    </tbody>
</table>
</div>

<script>
    let subjects = <?php echo json_encode($result); ?>;

    // Populate subject dropdown when type is selected
    $("#subjectType").on("change", function() {
        let type = $(this).val();
        let subjectDropdown = $("#subjectName");
        subjectDropdown.empty().append('<option value="">--SELECT ONE--</option>');

        if(subjects[type]) {
            $.each(subjects[type], function(id, name) {
                subjectDropdown.append('<option value="'+id+'">'+name+'</option>');
            });
        }
    });

    // Add row to table
    // $("#addSubject").on("click", function() {
    //     let type = $("#subjectType").val();
    //     let subjectName = $("#subjectName option:selected").text();
    //     let practical = $("#practical").val();
    //     let assignment = $("#assignment").val();
    //     let theory = $("#theory").val();
    //     let obtained = $("#obtained").val();
    //     let maximum = $("#maximum").val();

    //     if(type && subjectName && obtained && maximum) {
    //         let row = "<tr>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][subject_type][]' value='"+type+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][subject_name][]' value='"+subjectName+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][practical][]' value='"+(practical || 0)+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][assignment][]' value='"+(assignment || 0)+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][theory][]' value='"+(theory || 0)+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][obtained][]' value='"+obtained+"'></td>" +
    //         "<td><input type='text' readonly name='student_toc_subject["+type+"][maximum][]' value='"+maximum+"'></td>" +
    //         "<td><span class='removeRow'>✖</span></td>" +
    //             "</tr>";

    //         $("#subjectTable tbody").append(row);
            
    //          // ✅ Show what data is being created
    //     let subjectObj = {
    //         subject_type: type,
    //         subject_name: subjectName,
    //         practical: practical || 0,
    //         assignment: assignment || 0,
    //         theory: theory || 0,
    //         obtained: obtained,
    //         maximum: maximum
    //     };

    //     console.log("Row Added:", subjectObj);  // browser console me
    //     alert(JSON.stringify(subjectObj, null, 2)); // popup me

    //         // clear inputs
    //         $("#practical, #assignment, #theory, #obtained, #maximum").val("");
    //     } else {
    //         alert("Please select subject type, subject, and enter obtained/maximum marks.");
    //     }
    // });
    
     // Add row to table
        $("#addSubject").on("click", function() {
            let type = $("#subjectType").val();
            let subjectId = $("#subjectName").val();
            let subjectName = $("#subjectName option:selected").text();
            let practical = $("#practical").val();
            let assignment = $("#assignment").val();
            let theory = $("#theory").val();
            let obtained = $("#obtained").val();
            let maximum = $("#maximum").val();
            
            if (type && subjectId && subjectName && obtained && maximum) {
                // $('#subject'+subjectId).prop("checked",true);
                let row = "<tr>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][subject_type][]' value='" + type + "'></td>" +
                    "<td>" +
                    "<input type='hidden' name='student_toc_subject[" + type + "][subject_id][]' value='" + subjectId + "'>" +
                    "<input type='text' readonly name='student_toc_subject[" + type + "][subject_name][]' value='" + subjectName + "'>" +
                    "</td>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][practical][]' value='" + (practical || 0) + "'></td>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][assignment][]' value='" + (assignment || 0) + "'></td>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][theory][]' value='" + (theory || 0) + "'></td>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][obtained][]' value='" + obtained + "'></td>" +
                    "<td><input type='text' readonly name='student_toc_subject[" + type + "][maximum][]' value='" + maximum + "'></td>" +
                    "<td><span class='removeRow btn btn-danger btn-sm'>✖</span></td>" +
                    "</tr>";

                    $("#subjectTable tbody").append(row);

                // clear inputs
                $("#practical, #assignment, #theory, #obtained, #maximum").val("");
            } else {
                alert("Please select subject type, subject, and enter obtained/maximum marks.");
            }
        });


    // Remove row
    $(document).on("click", ".removeRow", function() {
        $(this).closest("tr").remove();
    });
</script>
    
    <?php
    
  }
  ?>

