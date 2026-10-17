<?php 

## Database configuration
include '../../includes/db-config.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$course_name = "";
$durationsDropDown = getDuration();

function getDuration() {
    global $conn;
    global $course_name;
    $course_id = mysqli_real_escape_string($conn,$_REQUEST['course_id']);
    $option = '<option value="">Select Duration</option>';
    
    if ($_SESSION['university_id'] == 48) {
        $durations = $conn->query("SELECT Name, Min_Duration , Course_Category FROM `Sub_Courses` WHERE University_ID = '48' AND ID = '$course_id'");
        $durations = mysqli_fetch_assoc($durations);
        $min_duration = json_decode($durations['Min_Duration'],true);
        $course_Category = json_decode($durations['Course_Category'],true);
        $course_name = $durations['Name'];
        foreach ($min_duration as $month) {
            foreach ($course_Category as $value) {
                $option .= '<option value="'.$month .'('. $value.')">'.$month .'('. $value.')'.'</option>';
            }
        }
    } else if ($_SESSION['university_id'] == 47) {
        $duration = $conn->query("SELECT Name , MAX(CAST(TRIM('\"' FROM Min_Duration) AS int)) as `maxDuration` FROM Sub_Courses WHERE University_ID = '47' AND ID = '$course_id'");        
        $duration = mysqli_fetch_assoc($duration);
        $course_name = $duration['Name'];
        $maxDuration = $duration['maxDuration'];
        $option .= createOptionTag($option,$maxDuration,1);
    }else{
        $duration = $conn->query("SELECT Name , MAX(CAST(TRIM('\"' FROM Min_Duration) AS int)) as `maxDuration` FROM Sub_Courses WHERE  ID = '$course_id'");        
        $duration = mysqli_fetch_assoc($duration);
       
        $course_name = $duration['Name'];
        $maxDuration = $duration['maxDuration'];
        $option .= createOptionTag($option,$maxDuration,1); 
    }
    return $option;
}

function createOptionTag($option,$maxDuration,$currentDuration) {
    if($currentDuration <= $maxDuration) {
        $option .= '<option value="'.$currentDuration.'">'.$currentDuration.'</option>';
        return createOptionTag($option,$maxDuration,++$currentDuration);
    } else {
        return $option;
    }
}

?>

<div class="row" id="duration_center_<?=$_REQUEST['course_id']?>">
    <div class="col-md-12">
        <div class="form-group form-group-default">
            <label>Duration/Semester for <?=$course_name?></label>
            <select class="full-width" style="border: transparent;" id="duration_<?=$_REQUEST['course_id']?>" data-init-plugin="select2" name="duration_<?=$_REQUEST['course_id']?>[]" multiple onchange="getDurationSelectedData(this.id)">
                <?=$durationsDropDown?>
            </select>
        </div>
    </div>
</div>
