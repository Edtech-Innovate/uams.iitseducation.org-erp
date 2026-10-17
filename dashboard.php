<?php include('includes/header-top.php'); ?>
<?php include('includes/header-bottom.php'); ?>
<?php include('includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->
    <div class="page-container ">
    <?php include('includes/topbar.php'); ?>      
      <!-- START PAGE CONTENT WRAPPER -->
      <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
          <!-- START JUMBOTRON -->
          <div class="jumbotron" data-pages="parallax">
            <div class=" container-fluid sm-p-l-0 sm-p-r-0">
              <div class="inner">
                <!-- START BREADCRUMB -->
                <ol class="breadcrumb">
                  <li class="breadcrumb-item active">Dashboard</li>
                </ol>
                     <?php if($_SESSION['Role']=='Center'){ ?>
                   <?php 
                        $center_id = mysqli_real_escape_string($conn,$_SESSION['ID']);
                        $searchQuery = "AND IF(Notifications_Generated.center_id != '',JSON_CONTAINS(Notifications_Generated.center_id,'[\"{$center_id}\"]'),true)";
                        $new_notification = $conn->query("SELECT * FROM Notifications_Generated WHERE Status = 1 AND (Send_To = 'center' OR Send_To = '".'all'."')  $searchQuery  ORDER BY Notifications_Generated.ID DESC LIMIT 1");
                        $records = mysqli_fetch_assoc($new_notification);
                        if(  $new_notification->num_rows > 0){
                        $record_count = array();
                        $viewed_id = array();

                        $viewed_notification = $conn->query("SELECT * FROM Notifications_Viewed_By WHERE Reader_ID =  ". $_SESSION['ID'] ." ORDER BY Notifications_Viewed_By.ID DESC LIMIT 1 ");
                        
                        if($viewed_notification->num_rows>0){
                            $viewed_records = mysqli_fetch_assoc($viewed_notification);
                            $viewed_id = json_decode($viewed_records['Notification_ID']);
                        }
                        if(in_array($records['ID'], $viewed_id))
                        {
                            $record_count = '';
                        }else{
                            $record_count = 1;
                        }
                    ?>
                    <div class="justify-content-between align-self-end" id="show-notification">
                    <?php if($record_count != '') { ?>
                        <a type="button" onclick="show_notification('<?=$records['ID'] ?>')"><iconify-icon icon="uil:bell"></iconify-icon>
                    <?php   echo "One New Notification regarding ".$records['Heading'];
                     }else{
                            echo '';
                    }?></a>
                    </div>
                    <?php } }?>
                <!-- END BREADCRUMB -->
              </div>
            </div>
          </div>
          <!-- END JUMBOTRON -->
          <!-- START CONTAINER FLUID -->
          <div class=" container-fluid">
            <!-- BEGIN PlACE PAGE CONTENT HERE -->
            <?php 
              if($_SESSION['Role']=='Student'){
                include ('dashboards/student.php');
              }
            ?>
            <?php if($_SESSION['Role']=='Center'){ ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="card card-default">
                    <div class="card-header separator">
                      <div class="card-title">Matrix</div>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tbody>
                            <?php $head = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.University_ID = ".$_SESSION['university_id']." AND Users.Role = 'University Head'");
                              if($head->num_rows>0){ 
                                $head = $head->fetch_assoc();
                                ?>
                                <tr>
                                  <td><b><?=$head['Role']?></b></td>
                                  <td><?=$head['Name']?></td>
                                  <td><?=$head['Email']?></td>
                                  <td><?=$head['Mobile']?></td>
                                </tr>
                              <?php }
                            ?>
                            <?php $counsellor = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID WHERE Alloted_Center_To_Counsellor.University_ID = ".$_SESSION['university_id']." AND Alloted_Center_To_Counsellor.Code = ".$_SESSION['ID']);
                              if($counsellor->num_rows>0){
                                $counsellor = $counsellor->fetch_assoc();
                                ?>
                                  <tr>
                                    <td><b><?=$counsellor['Role']?></b></td>
                                    <td><?=$counsellor['Name']?></td>
                                    <td><?=$counsellor['Email']?></td>
                                    <td><?=$counsellor['Mobile']?></td>
                                  </tr>
                                <?php
                              }
                            ?>
                            <?php $accountant = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.University_ID = ".$_SESSION['university_id']." AND Users.Role = 'Accountant'");
                              if($accountant->num_rows>0){ 
                                $accountant = $accountant->fetch_assoc();
                                ?>
                                <tr>
                                  <td><b><?=$accountant['Role']?></b></td>
                                  <td><?=$accountant['Name']?></td>
                                  <td><?=$accountant['Email']?></td>
                                  <td><?=$accountant['Mobile']?></td>
                                </tr>
                              <?php }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
             
                   <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-body dash1">
                            <div class="d-flex">
                                <p class="mb-1 tx-inverse">Notifications</p>
                                <div class="ml-auto">
                                    <i class="fas fa-chart-line fs-20 text-primary"></i>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Regarding</th>
                                        <th>Content</th>
                                        <th>Sent To</th>
                                        <th>Notification Sent On</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $university_id = mysqli_real_escape_string($conn,$_SESSION['university_id']);
                                    $query = "SELECT Notifications_Generated.ID , Notification_Heading.Name as `heading` , JSON_UNQUOTE(JSON_EXTRACT(Notifications_Generated.published_on,'$[0].published')) AS `send_on` ,Notifications_Generated.Send_To as `send_to` , Notifications_Generated.Attachment as `document` FROM `Notifications_Generated` LEFT JOIN Notification_Heading ON Notification_Heading.ID = Notifications_Generated.Heading WHERE Notifications_Generated.Status = '1' AND (Notifications_Generated.Send_To = 'center' OR Notifications_Generated.Send_To = 'all') AND Notifications_Generated.university_id = '$university_id' $searchQuery";       
                                    $result_record = $conn->query($query);
                                    $data = array();
                                    while ($row = $result_record->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['heading'] ?></td>
                                            <td><a type="btn" onclick="view_content('<?= $row['ID'] ?>');"><i class="uil uil-eye"></i></a></td>
                                            <td><?= $row['send_to'] ?></td>
                                            <td><?= $row['send_on'] ?></td>
                                            <td>
                                                <a href="<?= $row['document'] ?>" target="_blank" download="<?= $row['heading'] ?>">Download</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>   
                </div>
               
              </div>
            <?php } ?>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include('includes/footer-top.php'); ?>
<?php  include('includes/footer-bottom.php'); ?>
<?php if($_SESSION['Role']=='Center'){ ?>
<script type="text/javascript">
    function view_content(id) {
      $.ajax({
        url: '/app/notifications/contents?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#md-modal-content").html(data);
          $("#mdmodal").modal('show');
        }
      })
    }

    function show_notification(id){
        $.ajax({
        url: '/app/notifications/current-notification?id=' + id,
        type: 'GET',
        success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
        }
      })
    }
    </script>
    <?php }?>

        