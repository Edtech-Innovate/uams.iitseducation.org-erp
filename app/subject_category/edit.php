<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_GET['id']);
  $subject_category = $conn->query("SELECT * FROM subject_category WHERE id = $id");
  $subject_category = mysqli_fetch_assoc($subject_category);
?>
<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<div class="modal-header clearfix text-left">
  <button type="button" class="close" data-dismiss="modal"><i class="pg-icon">close</i></button>
  <h5 class="font-weight-bold text-black">Edit <span class="semi-bold">Subject Category</span></h5>
</div>

<form id="form-edit-sub-course" action="/app/subject_category/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <input type="hidden" name="id" value="<?= $subject_category['ID'] ?>">

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" 
                 placeholder="ex: Mechanical Engineering" 
                 value="<?= htmlspecialchars($subject_category['name']) ?>" required>
        </div>
      </div>
    </div>
  </div>

  <div class="modal-footer text-end">
    <button type="button" data-dismiss="modal" class="btn custom_sec_form_btn mr-2">
      <i class="ti ti-circle-dashed-x mr-2"></i> Close
    </button>
    <button type="submit" class="btn custom_form_btn">
      <i class="ti ti-circle-check mr-2"></i> Update
    </button>
  </div>
</form>

<script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
<script>
$(function(){
  $("#form-edit-sub-course").validate({
    rules: {
      name: { required: true }
    },
    highlight: function(el) {
      $(el).addClass('error').closest('.form-control').addClass('has-error');
    },
    unhighlight: function(el) {
      $(el).removeClass('error').closest('.form-control').removeClass('has-error');
    }
  });

  $("#form-edit-sub-course").on("submit", function(e) {
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
        success: function(data) {
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
<?php } ?>
