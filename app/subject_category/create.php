<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i></button>
  <h5 class="font-weight-bold text-black">Add <span class="semi-bold">Subject Category</span></h5>
</div>

<form role="form" id="form-add-sub-category" action="/app/subject_category/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Mechanical Engineering" required>
        </div>
      </div>
      <!--<div class="col-md-6">-->
      <!--  <div class="form-group form-group-default required">-->
      <!--    <label>Short Name</label>-->
      <!--    <input type="text" name="short_name" class="form-control" placeholder="ex: ME" required>-->
      <!--  </div>-->
      <!--</div>-->
    </div>
  </div>

  <div class="modal-footer text-end">
    <button type="button" data-dismiss="modal" class="btn custom_sec_form_btn mr-2">
      <i class="ti ti-circle-dashed-x mr-2"></i> Close
    </button>
    <button type="submit" class="btn custom_form_btn">
      <i class="ti ti-circle-check mr-2"></i> Save
    </button>
  </div>
</form>

<script src="../../assets/plugins/select2/js/select2.full.min.js"></script>

<script>
  $(function () {
    // Select2 init (अर future में कोई dropdown add करोगे तो काम आएगा)
    $(".select2").select2();

    // Form Validation
    $('#form-add-sub-category').validate({
      rules: {
        name: { required: true },
        short_name: { required: true }
      },
      highlight: function (element) {
        $(element).addClass('error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
      }
    });

    // AJAX submit
    $("#form-add-sub-category").on("submit", function (e) {
      e.preventDefault();
      if ($(this).valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        $.ajax({
          url: this.action,
          type: 'POST',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function (data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#subject_category').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
      }
    });
  });
</script>
