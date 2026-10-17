<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .select2-container .select2-selection {
    border-radius: 10px;
    height: 48px !important;
    font-size: 17px;
    font-family: system-ui;
  }

  .select2-container .select2-selection .select2-selection__arrow {
    top: auto;
    bottom: 11px;
  }

  .select2-container--open .select2-selection {
    box-shadow: none;
    border: 1px solid #2b303b !important;
  }

  .select2-results .select2-results__option--highlighted {
    background-color: #55638d !important;
    border-radius: 3px;
    color: #ffffff !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['filterByUniversity']);
unset($_SESSION['subCourseFilter']);
unset($_SESSION['durationFilter']);
unset($_SESSION['usersFilter']);

?>
<style>
  thead tr th {
    font-weight: 700 !important;
  }
</style>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php
              $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  $breadcrumbText = str_replace("-", " ", $crumb[0]); // Replace hyphens with spaces
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords(strtolower($breadcrumbText)) . '</li>';
                endif;
              }
              ?>
              <div>
                <button class="btn btn-primary" aria-label="" title="" data-toggle="tooltip"
                  data-original-title="Upload" onclick="add('subjects', 'lg')">Upload Subject <i
                    class="uil uil-export"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->

          </div>

        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="card card-transparent">
          <div class="card-header">
            <div class="row justify-content-between">


              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <?php $get_course = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Sub_Courses LEFT JOIN Courses on Courses.ID = Course_ID WHERE Sub_Courses.Status = 1 AND Sub_Courses.University_ID = " . $_SESSION['university_id'] . " ORDER BY Sub_Courses.Name ASC"); ?>

                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_course"
                    onchange="addFilter(this.value, 'sub_course')" data-placeholder="Choose Sub-Courses">
                    <option value="">Select Sub-Courses</option>
                    <?php while ($row = $get_course->fetch_assoc()) { ?>
                      <option value="<?php echo $row['ID']; ?>"><?= $row['Name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                    onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">

                  </select>
                </div>
              </div>
              <div class="col-md-3"></div>
              <div class="col-md-2">
                <input type="text" id="users-search-table" class="form-control pull-right custom_search_section"
                  placeholder="Search">
              </div>
            </div>

          </div>

          <div class="card-body">
            <div class="">
              <table class="table table-hover nowrap table-responsive" id="users-table">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Min/Max Marks</th>
                    <th>Paper Type</th>
                    <th>Credit</th>
                    <th>Upload Syllabus</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $(function () {

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/subject/server'
          },
          'columns': [
            {
              data: "Code",
              width: "15%",
            },
            {
              data: "Name",
              width: "15%",
            },

            {
              data: "course_name",
              width: "15%",
            },
            {
              data: "Semester",
              width: "15%",
            },
            {
              data: "Marks",
              width: "15%",
            },
            {
              data: "Paper_Type",
              width: "15%",
            },
            {
              data: "Credit",
              width: "15%",
            },



            {
              data: "ID",
              width: "15%",
              "render": function (data, type, row) {
                var uni_id = '<?= $_SESSION['university_id'] ?>';
                let downloadSylBtn = '';
                if (row.files != null) {
                  downloadSylBtn = '<a href="..' + row.files + '"><i class="uil uil-down-arrow icon-xs cursor-pointer" title="Download" ></i></a>';
                }
                // if (row.files != null && row.files.trim() !== '') {
                //   let files = row.files.split("|");
                //   $.each(files, function (index, file) {
                //     downloadSylBtn += '<a href="' + file + '" target="_blank" download="' + row.Code + '" class="me-1 text-primary"><i class="uil uil-import icon-xs" title="Download"></i></a>';
                //   });
                // }

                downloadSylBtn = ['Administrator', 'University Head'].includes(role) ? downloadSylBtn:"";

                var uploadSylBtn = ['Administrator', 'University Head'].includes(role)
                  ? '<i class="uil uil-upload icon-xs cursor-pointer " title="Upload Syllabus" onclick="upload(&#39;subject&#39;, &#39;' + data + '&#39, &#39;' + row.Code + '&#39, &#39;' + row.Name + '&#39)"></i>'
                  : '';
                  var editBtn = ['Administrator', 'University Head'].includes(role)
                  ? '<i class="uil uil-edit icon-xs cursor-pointer " title="Edit" onclick="edit(&#39;subject&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>'
                  : '';
                  var deleteBtn = ['Administrator', 'University Head'].includes(role) ? '<i class="uil uil-trash icon-xs cursor-pointer custom_edit_button" title="Delete" onclick="destroy(&#39;subject&#39;, &#39;' + data + '&#39)"></i>' : '';
                
                  
                return `
                      <div class="button-list text-center">
                        ${downloadSylBtn}
                        ${uploadSylBtn}
                        ${editBtn}
                        ${deleteBtn}
                      </div>
                    `;
              }
            },
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 25,
          "drawCallback": function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
          },
        };

        table.dataTable(settings);
        // search box for table
        $('#users-search-table').keyup(function () {
          table.fnFilter($(this).val());
        });

      })
    </script>

    <script>
      function addFilter(id, by) {
        $.ajax({
          url: '/app/subject/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {
            const universityId = <?php echo json_encode($_SESSION['university_id']); ?>;
            if (by == "sub_course") {
              getDuration(id);
            }
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
      function allotCenter(id, modal) {
        $.ajax({
          url: '/app/subjects/allot-center?id=' + id,
          type: 'GET',
          success: function (data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        });
      }
      $(document).ready(function () {
        getDuration();
        $("#center").select2({
          placeholder: 'Choose Center',
        })
        $("#sub_course").select2({
          placeholder: 'Choose Sub Course',
        })
        $("#exam_type").select2({
          placeholder: 'Choose Exam Type',
        })
      })
      function getDuration(id) {
        $.ajax({
          url: '/app/regions/get-duration',
          data: { id: id },
          type: 'POST',
          success: function (data) {
            $("#duration").html(data);
            addFilter(id);
          }
        })
      }


      function upload(url, id, code, subject_name) {
        var modal = 'md';
        $.ajax({
          url: '/app/' + url + '/upload',
          type: 'POST',
          data: { id: id, modal: modal, code: code, subject_name: subject_name },
          success: function (data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
    </script>
    <script type="text/javascript">
      function uploadFile(table, column, id) {
        $.ajax({
          url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
          type: 'GET',
          success: function (data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>