<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    table thead tr{
        background: #2b303b !important;
}
table thead tr th{
    color:white !important;
}
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <li class="breadcrumb-item active">Center Ledger</li>
              <div></div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="card card-body">
        <?php if ($_SESSION['Role'] != 'Center') { ?>
          <div class="row d-flex justify-content-start">
            <div class="col-md-4">
              <div class="card shadow-none">
                <div class="card-body p-0">
                  <div class="form-group form-group-default required mb-0">
                    <label>Centers</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="center" onchange="getLedger()">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card shadow-none">
                <div class="card-body p-0">
                  <div class="form-group form-group-default required mb-0">
                    <label>Adm Session</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                      <option value="">Select</option>
                      <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']);
                        while($session = $sessions->fetch_assoc()){
                          echo '<option value="'.$session['ID'].'">'.$session['Name'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php }else{ ?>
          <input type="hidden" id="center" value="<?=$_SESSION['ID']?>">
          <div class="row d-flex justify-content-center">
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <div class="form-group form-group-default required">
                    <label>Adm Session</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                      <option value="">Select</option>
                      <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']);
                        while($session = $sessions->fetch_assoc()){
                          echo '<option value="'.$session['ID'].'">'.$session['Name'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <div class="row m-t-20">
          <div class="col-lg-12">
            <div class="card card-transparent">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                <li class="nav-item">
                  <a class="active" data-toggle="tab" data-target="#students" href="#"><span>Students</span></a>
                </li>
                <li class="nav-item">
                  <a data-toggle="tab" data-target="#pending" href="#"><span>Pending</span></a>
                </li>
                <li class="nav-item">
                  <a data-toggle="tab" data-target="#processed" href="#"><span>Processed</span></a>
                </li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane active" id="students">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="pending">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="processed">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
        </div>
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script>
      function getLedger() {
        var id = $("#center").val();
        var admission_session_id = $("#admission_session_id").val();
        getStudentList(id, admission_session_id);
        getPendingList(id, admission_session_id);
        getProcessedList(id, admission_session_id);
      }

      function getStudentList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/students?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#students").html(data);
          }
        })
      }

      function getPendingList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/pending?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#pending").html(data);
          }
        })
      }

      function getProcessedList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/processed?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#processed").html(data);
          }
        })
      }

      getCenterList('center');

      <?php if ($_SESSION['Role'] == 'Center') {
        echo 'getLedger()';
      } ?>
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
