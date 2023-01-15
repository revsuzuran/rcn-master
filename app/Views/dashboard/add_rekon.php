

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload'); ?>" id="uploadForm">
    <div class="row mb-3">
      
          <div class="col-6">
            <div class="card mb-4 pb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Input Detail Rekon</h6>
                </div>
                <div class="card-body">
                
                  
                    <div class="col-lg-6">
                      <div class="form-group">
                          <label class="form-label">Nama Rekon</label>
                          <input name="namaRekon" type="text" class="form-control" placeholder="ex : Rekon Bank Indonesia" value="" required>
                      </div>
                    </div>

                
                </div>

            </div>
          </div>

          <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Upload File Rekon #1</h6>
                </div>
                <div class="card-body">
                  <div class="form-group mb-3">
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
                  <?php $isError = isset($_SESSION['error']) ? $_SESSION['error'] : ""; ?>
                  

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
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="btnSetting">
                      Load Setting
                    </button>
                    <button class="btn btn-primary me-md-2" type="submit">Next</button>
                  </div>
              </div>
            </div>
          </div>

        <input name="tipe" type="text" class="form-control" value="1" hidden>
        <input name="id_setting" type="text" class="form-control" id="id_setting" hidden>
      
    </div>
    </form>
    <!--Row-->
</div>
<!---Container Fluid--> 

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <!-- <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload_with_setting'); ?>"> -->
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Load Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group col-6 mt-2">
            <label class="form-label">Pilih Setting</label>
            <input type="file" name="csvFile_" id="csvFile_" accept=".csv" hidden>
            <select class="form-select mb-3" name="opt_setting" id="opt_setting">
              <option value="0">-</option>
              <?php foreach ($data_setting as $rowData) { ?>
                <option value="<?= $rowData['_id'] ?>"><?= $rowData['nama_setting'] ?></option>
              <?php } ?>
            </select>
        </div>
        <hr>
        <label class="form-label fw-bold" style="color:#0d6efd;">Setting Details</label>
        <div class="form-group mt-2" id="datasetting">
                       
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submitProses">Proses Data</button>
      </div>

    <!-- </form> -->
    </div>
  </div>
</div>

<script>
   
  $("#opt_setting").on('change', function() {
    var id = $(this).find(":selected").val();
    $.ajax({
        url : "<?= base_url('get_setting') ?>",
        method : "POST",
        data : {id: id},
        success: function(hasil){
          
          $("#id_setting").val(id);
          
          let contentHehe = "<table><tbody>";
          const anu = JSON.parse(hasil);
          contentHehe += `<tr>
                 <td class="fw-lighter"># Delimiter</td>
                 <td class="fw-lighter">&nbsp;:&nbsp;</td>
                 <td><code class="highlighter-rouge"> "${anu.delimiter}" </code></td>
               </tr>`;
          for (const row of anu.clean_rule) {
              contentHehe += `<tr>
                 <td class="fw-lighter"># Clean Rule</td>
                 <td class="fw-lighter">&nbsp;:&nbsp;</td>
                 <td>KOLOM ${parseInt(row.index_kolom)+1} <code class="highlighter-rouge"> [${row.rule}] </code> ${row.rule_value} </td>
               </tr>`;
              
          }

          for (const row of anu.kolom_compare) {
              contentHehe += `<tr>
                 <td class="fw-lighter"># Kolom to Compare</td>
                 <td class="fw-lighter">&nbsp;:&nbsp;</td>
                 <td>${row.kolom_name} </td>
               </tr>`;
              
          }

          for (const row of anu.kolom_sum) {
              contentHehe += `<tr>
                 <td class="fw-lighter"># Kolom to SUM</td>
                 <td class="fw-lighter">&nbsp;:&nbsp;</td>
                 <td>${row.kolom_name} </td>
               </tr>`;
              
          }
          
          contentHehe += "</tbody></table>";
          $("#datasetting").html(contentHehe);
          
        }
    });
  });

  $("#submitProses").on("click", function(e) {
    e.preventDefault();
    $('#uploadForm').attr('action', "<?php echo base_url('rekon/upload_with_setting'); ?>").submit();
  });

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
