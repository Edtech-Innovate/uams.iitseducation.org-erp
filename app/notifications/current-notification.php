<!-- Modal -->
<div class="modal-header clearfix text-left mb-3">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="change_notification_status()"><i class="pg-icon">close</i>
  </button>
  <h5 class="modal-title"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M18 13.18V10a6 6 0 0 0-5-5.91V3a1 1 0 0 0-2 0v1.09A6 6 0 0 0 6 10v3.18A3 3 0 0 0 4 16v2a1 1 0 0 0 1 1h3.14a4 4 0 0 0 7.72 0H19a1 1 0 0 0 1-1v-2a3 3 0 0 0-2-2.82M8 10a4 4 0 0 1 8 0v3H8Zm4 10a2 2 0 0 1-1.72-1h3.44A2 2 0 0 1 12 20m6-3H6v-1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1Z"></path></svg> Notification</h5>
</div>
  <div class="modal-body">
    <div class="row">
    <?php
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            ## Database configuration
            include '../../includes/db-config.php';
            session_start();
            // $conn->query("UPDATE `Notifications_Generated` SET `Status` = 1 WHERE ID =  $id");
        

        $result_record = $conn->query("SELECT Notifications_Generated.*, Notification_Heading.Name as notification_name FROM Notifications_Generated  LEFT JOIN Notification_Heading ON Notification_Heading.ID = Notifications_Generated.Heading  WHERE Notifications_Generated.ID =  $id ");
        while ($row = $result_record->fetch_assoc()) { ?>
        <!--<div>-->
        <!--    <tr>-->
        <!--        <td><?=$row['Heading']?></td>-->
        <!--        <td><?=$row['Content']?></td>-->
        <!--    </tr>-->
        <!--</div>-->
     
        <div class="card-body bg-light">
        <p class="card-text"><span  class="mb-0 card-text fw-semibold text-primary"><b><?= htmlspecialchars($row['notification_name']) ?></b> : </span><?= nl2br(htmlspecialchars($row['Content'])) ?></p>
      </div>
    <?php }} ?>
    </div>
  </div>
  <script type="text/javascript">
    function change_notification_status() {
        <?php
            $ids = array();
            $add ='';
            $updated = '';
            
            $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE Status <> 1 AND Send_To = 'center' OR Send_To = '".'all'."'");
            while ($row = $result_record->fetch_assoc()) {
                $ids[] = $row['ID'];
            }

            $viewed_notification = $conn->query("SELECT * FROM Notifications_Viewed_By WHERE Reader_ID =  ". $_SESSION['ID'] ." ORDER BY Notifications_Viewed_By.ID DESC LIMIT 1");

            if($viewed_notification->num_rows>0){
                $viewed_records = mysqli_fetch_assoc($viewed_notification);
                $viewed_id = json_decode($viewed_records['Notification_ID']);
                foreach ($ids as $key => $value) {
                    if(!in_array($value, $viewed_id)){
                        $new_id[] = $value;
                        $merged_id = array_merge($viewed_id, $new_id);
                        $notification_ids = json_encode($merged_id);
                        $updated = $conn->query("UPDATE `Notifications_Viewed_By` SET `Notification_ID` = '$notification_ids' WHERE Reader_ID =  ". $_SESSION['ID'] ." ");
                    }
                }
            }else{
                $center_ID = $_SESSION['ID'];
                $record_id = json_encode($ids);
                $add = $conn->query("INSERT INTO `Notifications_Viewed_By` (`Notification_ID`, `Reader_ID`, `Status`) VALUES ('$record_id', '$center_ID', 1)");
            }
            if($add !='' || $updated != ''){
        ?>
            location.reload();
        <?php } ?>
    }
</script>