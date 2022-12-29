

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload'); ?>">
    <div class="row mb-3">
      
          <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
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
                      <div class="custom-control custom-radio">
                        <input type="radio" id="radioDb" name="radioUpload" class="form-check-input" value="db">
                        <label class="custom-control-label" for="radioDb">Database</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group form-file">
                    <div class="custom-file">
                      <input type="file" name="csvFile" id="csvFile" accept=".csv">
                    </div>
                  </div>
                  
                  <div class="form-group form-ftp col-6">
                    <div class="custom-file">                      
                      <div class="mb-3 ">
                        <label class="form-label">FTP Connection</label>
                        <select class="form-select mb-3" name="ftp_option">
                          <?php foreach ($dataFtp as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['ftp_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Nama File</label>
                        <div class="custom-file">
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="File Name on Ftp Server" aria-label="Recipient's username" aria-describedby="basic-addon2" name="nama_file">
                            <div class="input-group-append">
                              <span class="input-group-text" id="basic-addon2">.csv</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group form-db">
                    <div class="custom-file">                      
                      <div class="mb-3 col-6">
                        <label class="form-label">Database Connection</label>
                        <select class="form-select mb-3" name="db_option">
                          <?php foreach ($dataDb as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['db_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Input Query</label>
                        <textarea class="form-control" name="query" rows="3"></textarea><br>
                        <p class="fst-italic"><code class="text-primary fw-bold">Information</code> :<br> Use single quotation marks</code></p> 
                        <p>Example : <br> SELECT * FROM data_transaksi WHERE date_insert = <code class="highlighter-rouge">'2022-12-30'</code></p> 
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
      
    </div>
    </form>
    <!--Row-->
</div>
<!---Container Fluid-->


<script>
  
  $(".form-ftp").hide();
  $(".form-file").show();  
  $(".form-db").hide();   

  $("#radioFtp").click(function() {
    $(".form-ftp").show();
    $(".form-file").hide();   
    $(".form-db").hide();    
  });

  $("#radioFile").click(function() {
    $(".form-ftp").hide();
    $(".form-file").show();   
    $(".form-db").hide();   
  });

  $("#radioDb").click(function() {
    $(".form-ftp").hide();
    $(".form-file").hide(); 
    $(".form-db").show();    
  });

</script>
