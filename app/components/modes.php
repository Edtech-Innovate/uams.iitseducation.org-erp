<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingModes">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseModes" aria-expanded="false" aria-controls="collapseModes">
          Modes
        </a>
    </div>
  </div>
  <div id="collapseModes" class="collapse" role="tabcard" aria-labelledby="headingModes">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn custom_erp_btn" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Modes"  onclick="addComponents('modes', 'md', <?=$university_id?>)"><i class="ti ti-library-plus"></i></button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap table-striped" id="tableModes">
              <thead>
                <tr>
                  <th width="30%">Name</th>
                  <th data-orderable="false">Status</th>
                  <th data-orderable="false" class="text-end">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table = $('#tableModes');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/modes/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Modes\', \'Modes\', \''+row.ID+'\');" type="checkbox" '+checked+' id="mode-status-switch-'+row.ID+'">\
            <label for="mode-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="ti ti-edit custom_erp_btn p-1 cursor-pointer"  data-toggle="tooltip" title="Edit" onclick="editComponents(\'modes\', \''+data+'\', \'md\');"></i>\
            <i class="ti ti-trash custom_erp_btn p-1 cursor-pointer"  data-toggle="tooltip" title="Delete" onclick="destroyComponents(\'modes\', \'Modes\', \''+data+'\');"></i>\
          </div>'
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
    "iDisplayLength": 5
  };
    $('#tableModes').on('draw.dt', function () {
          $('[data-toggle="tooltip"]').tooltip();
          });
  table.dataTable(settings);
</script>
