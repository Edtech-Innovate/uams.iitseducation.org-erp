<?php
require '../../includes/db-config.php';
require '../../includes/helpers.php';

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['type'] == 'Course') {
    // subcourse
    $course_id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $option = "<option value=''>Choose Sub-Course</option>";
    $option .= getSubCourses($conn, $course_id);
    echo $option; die;

} else if (isset($_GET['course_id']) && !empty($_GET['course_id']) && isset($_GET['sub_course_id']) && !empty($_GET['sub_course_id']) && $_GET['type'] == 'Semester') {
    // duration 
    $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : NULL;
    $sub_course_id = isset($_GET['sub_course_id']) ? $_GET['sub_course_id'] : NULL;
    $duration_sql = $conn->query("SELECT Min_Duration From Sub_Courses WHERE  ID = $sub_course_id AND Course_ID = $course_id AND  Status=1");
    $sem = 0;
    if ($duration_sql->num_rows > 0) {
        $duration = $duration_sql->fetch_assoc();
        $sem = $duration['Min_Duration'];
    }
    $options = '';
    if ($sem == 0) {
        $options = "<option value =''>No Record Found</option>";
    } else {
        for ($i = 1; $i <= $sem; $i++) {
            $options .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }
    echo $options;

} else {
    echo "<option value=''>No Record Found</option>";
}