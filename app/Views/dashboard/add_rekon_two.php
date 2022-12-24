

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>

    <div class="row mb-3">
      <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload'); ?>">
          <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                      <h6 class="m-0 font-weight-bold text-light">Input Data Rekon #2</h6>
                </div>
                <div class="card-body">
                
                  <div class="row mb-3">

                    <div class="col-lg-6">
                      <label class="form-label">Sample File Rekon #2</label>
                      <div class="form-group">
                        <div class="custom-file">
                          <input type="file" name="csvFile" id="csvFile" accept=".csv">
                        </div>
                      </div>
                    </div>
                
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <button class="btn btn-primary me-md-2" type="submit">Next</button>
                </div>
              </div>
            </div>
          </div>
        <input name="tipe" type="text" class="form-control" value="2" hidden>
      </form>
    </div>
    <!--Row-->
</div>
<!---Container Fluid-->
