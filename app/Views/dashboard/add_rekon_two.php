

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload'); ?>">
    <div class="row mb-3">
      
          <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                      <h6 class="m-0 font-weight-bold text-light">Upload File Rekon #2</h6>
                </div>
                <div class="card-body">
                  <div class="form-group mb-3">
                    <!-- Radio Div -->
                    <div class="row row-cols-auto">
                      <div class="custom-control custom-radio ">
                        <input type="radio" id="radioFile" name="radioUpload" class="form-check-input" value="local" checked="checked">
                        <label class="custom-control-label" for="radioFile">Local File</label>
                      </div>
                      <div class="custom-control custom-radio">
                        <input type="radio" id="radioFtp" name="radioUpload" class="form-check-input" value="ftp">
                        <label class="custom-control-label" for="radioFtp">FTP</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group form-file">
                    <div class="custom-file">
                      <input type="file" name="csvFile" id="csvFile" accept=".csv">
                    </div>
                  </div>
                  
                  <div class="form-group col-6 form-ftp">
                    <label class="form-label">Nama File</label>
                    <div class="custom-file">
                      <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="File Name on Ftp Server" aria-label="Recipient's username" aria-describedby="basic-addon2" name="nama_file">
                        <div class="input-group-append">
                          <span class="input-group-text" id="basic-addon2">.csv</span>
                        </div>
                      </div>
                      <a class="fw-bold" href="<?php echo base_url('ftp');?>">FTP Server Setting's</a>
                      <input type="text" name="ftp" value="false" hidden>
                    </div>
                  </div>

                  <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary me-md-2" type="submit">Next</button>
                  </div>
              </div>
            </div>
          </div>

        <input name="tipe" type="text" class="form-control" value="2" hidden>
      
    </div>
    </form>
    <!--Row-->
</div>
<!---Container Fluid-->


<script>
  

  $(".form-ftp").hide();
  $(".form-file").show();  

  $("#radioFtp").click(function() {
    $(".form-ftp").show();
    $(".form-file").hide();    
  });

  $("#radioFile").click(function() {
    $(".form-ftp").hide();
    $(".form-file").show();    
  });

</script>
