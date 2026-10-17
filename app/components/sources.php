<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingSources">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSources" aria-expanded="false" aria-controls="collapseSources">
          Channels
        </a>
    </div>
  </div>
  <div id="collapseSources" class="collapse" role="tabcard" aria-labelledby="headingSources">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn custom_erp_btn" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Channel" onclick="addComponents('sources', 'md', '')"><i class="ti ti-library-plus"></i></button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap table-striped" id="tableSources">
              <thead>
                <tr>
                  <th width="50%">Name</th>
                  <th data-orderable="false">Status</th>
                  <th data-orderable="false">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
