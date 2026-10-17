<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container">
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
               <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div class="text-end">
                <button class="btn btn-primary" onclick="add('notification-type','lg')">Notification Type <i class="uil ml-2 uil-bell"></i></button>
                <button class="btn btn-primary" onclick="add('notifications','lg')">Add <i class="uil uil-plus-circle"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
          <div class="card card-body">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row">
          <div class="col-md-2">
            <div class="form-group form-group-default custom_border_radius">
              <label>Notification Heading</label>
              <select class="full-width" style="border: transparent;" id="heading_filter" onchange="reloadTable()"></select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group form-group-default custom_border_radius">
              <label>Notification by user</label>
              <select class="full-width" style="border: transparent;" id="user_filter" onchange="reloadTable()"></select>
            </div>
          </div>
          <div class="col-md-6"></div>
          <div class="col-md-2">
            <input type="text" id="notification-search" class="form-control pull-right custom_search_section" placeholder="Search"></div>
        </div>

        <div class="card card-transparent">
          <div class="card-header">
            <div class="pull-right">
              <div class="col-xs-12">
              
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="">
              <table class="table table-hover nowrap table-responsive" id="notification_table">
                <thead>
                  <tr>
                    <th>Regarding</th>
                    <th>Sent To</th>
                    <th>Created At</th>
                    <th>Content</th>
                    <th>Group Filter</th>
                    <th>User List</th>
                    <th>Attachment</th>
                    <th>Published On</th>
                    <th>Status</th>
                  </tr>
                </thead>
              </table>
            </div>
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

var table = $('#notification_table');

var settings = {
  'processing': true,
  'serverSide': true,
  'serverMethod': 'post',
  'ajax': {
    'url': '/app/notifications/server',  
    'type': 'POST',
    "data" : function(d) {
      d.headingFilter =  $("#heading_filter").val();
      d.sendTo = $("#user_filter").val();
    }
  },
  'columns': [{
      data: "Heading",  width: "15%",
    },{
      data: "Send_To" ,  width: "15%",
    },{
      data : "created_at" ,  width: "15%",
    },{
      data: "Content",   width: "15%",
      render : function(data, type, row) {
        return '<button type="btn" class = "btn btn-sussess p-1 px-2" onclick="view_content('+row.ID+');"> view <i class="uil ml-2 uil-eye"></i></button>';
      }
    },{
      data : "group_filter" ,  width: "15%",
      render : function(data,type,row) {
        return '<button type="btn"  class = "badge badge-success p-2 border-0" onclick="viewGroupFilter('+row.ID+');">view filter</button>';
      }
    },{
      data: "user_list",   width: "15%",
      render : function(data, type, row) {
        return '<button class="btn btn-sussess p-1 px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Export Notify User List" onclick="exportUserData(&#39;'+row.ID+'&#39;)"> <i class="uil uil-down-arrow"></i></button>';
      }
    },{
      data : "Attachment" ,   width: "15%",
      render : function(data,type,row) {
        return '<a href="'+data+'" target="_blank" download  class="badge badge-success p-2"">Download</a>'
      } 
    },{
      data : "published_on" ,  width: "15%",
      render : function(data,type,row) {
        var active = ( data == 'Not Published') ? 'Not Published Yet' : data;
        return '<div><b>'+active+'</b></div>';
      }
    },
      {
          data: "status",
          width: "15%",
          render: function(data, type, row) {
            var active = (row.status == 1) ?
              '<span class="badge badge-success">Active</span>' :
              '<span class="badge badge-danger">Inactive</span>';

            var checked = (row.status == 1) ? 'checked' : '';

            return '<div class="form-check form-check-inline switch switch-lg success">\
        <input onclick="changeNotificationStatus(\'' + row.ID + '\')" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
        <label for="status-switch-' + row.ID + '">' + active + '</label>\
    </div>';
          },
        }
  ],
  "sDom": "<t><'row'<p i>>",
  "destroy": true,
  "scrollCollapse": true,
  "oLanguage": {
    "sLengthMenu": "_MENU_ ",
    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
  },
  "aaSorting": [],
  "iDisplayLength": 10,
  "drawCallback": function( settings ) {
    $('[data-toggle="tooltip"]').tooltip();
  },
};

$('#notification-search').keyup(function() {
  table.fnFilter($(this).val());
});

function reloadTable() {
  table.dataTable(settings);
}

$(document).ready(function(){
  table.dataTable(settings);
  getFilterData();
});

function getFilterData() {
  var filter_data_field = ['heading','user'];
  $.ajax({
    url : "/app/notifications/notification-filter",
    type : "post",
    contentType: 'json',  // Set the content type to JSON 
    data: JSON.stringify(filter_data_field), 
    dataType: 'json', 
    success : function(data) {
      for (const key in data) {
        $("#"+key+"_filter").html(data[key]);
      }
    }   
  })
}

function getSemester(id) {
  $.ajax({
    url: '/app/subjects/semester?id=' + id,
    type: 'GET',
    success: function(data) {
      $("#semester").html(data);
    }
  })
}

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

function viewGroupFilter(id) {
  $.ajax({
    url: "/app/notifications/viewGroupFilter",
    type: 'POST',
    data : {id},
    success: function(data) {
      $("#lg-modal-content").html(data);
      $("#lgmodal").modal('show');
    }
  })
}

function exportUserData(id) {
  $.ajax({
    url: '/app/notifications/notifyUser-server',
    type: 'POST',
    data: { "notification_id": id },
    xhrFields: {
      responseType: 'blob' // Ensures response is treated as binary
    },
    success: function(blob, status, xhr) {
      const contentType = xhr.getResponseHeader("Content-Type");

      // Check if response is an error instead of a valid file
      if (contentType.includes("application/json")) {
        const reader = new FileReader();
        reader.onload = function() {
          const errorMessage = JSON.parse(reader.result);
          notification('danger', errorMessage.message || 'Failed to download file.');
        };
        reader.readAsText(blob);
        return;
      }

      // Otherwise, create download link
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `Notification_UserList_${new Date().toISOString().replace(/:/g, '-')}.xlsx`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error, xhr.responseText);
      notification('danger', 'Failed to download file.');
    }
  });
}


function changeNotificationStatus(id) {
  $.ajax({
    url: '/app/notifications/changeNotificationStatus',
    type: 'POST',
    data : {id},
    dataType: "json",
    success: function(data) {
      if(data.status==200){
        notification('success', data.message);
        table.dataTable(settings);
      }else{
        notification('danger', data.message);
      }
    }
  })
}

function uploadFile(table,column,id) {
  $.ajax({
    url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
    type: 'GET',
    success: function(data) {
      $("#md-modal-content").html(data);
      $("#mdmodal").modal('show');
    }
  })
}

</script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
