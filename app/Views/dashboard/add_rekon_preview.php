<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    
      <div class="row mb-3">
          <!-- Tables -->
          <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                    <h6 class="m-0 font-weight-bold text-light">Data Rekon</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="table-dark">
                        <tr>
                          <?php
                          $totalData = count($data_csv);
                          foreach ($data_csv as $row) {
                            $noKolom = 1;
                            foreach ($row as $data) {
                          ?>
                              <th>KOLOM <?php echo $noKolom;
                              $noKolom++; ?> </th>
                          <?php } break; }  ?>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <?php foreach ($data_csv as $row) { ?>
                            <?php foreach ($row as $data) { ?>
                              <th>KOLOM</th>
                            <?php }break;  } ?>
                        </tr>
                      </tfoot>
                      <tbody>

                      <?php foreach ($data_csv as $row) { ?> 
                        <tr>
                          <?php foreach ($row as $data) { ?>                        
                            <td><?= $data ?></td>
                          <?php } ?>
                        </tr>
                      <?php } ?>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
          </div>
          <!-- Tables-->

         
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-light">Cleaning Data Options</h6>
              </div>
              <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/save_cleansing'); ?>">
              <div class="card-body">
                <div class="form-group">
                  <!-- Radio Div -->
                  <div class="row row-cols-auto">
                    <div class="custom-control custom-radio m-2 ">
                      <input type="radio" id="radioRowRemove" name="customRadio" class="form-check-input" value="radioRowRemove">
                      <label class="custom-control-label" for="radioRowRemove">Remove Row</label>
                    </div>
                    <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioReplace" name="customRadio" class="form-check-input" value="radioReplace">
                      <label class="custom-control-label" for="radioReplace">Replace</label>
                    </div>
                    <!-- <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioSplit" name="customRadio" class="form-check-input">
                      <label class="custom-control-label" for="radioSplit">Split</label>
                    </div> -->
                    <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioUpper" name="customRadio" class="form-check-input" value="radioUpper">
                      <label class="custom-control-label" for="radioUpper">Uppercase</label>
                    </div>
                    <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioLower" name="customRadio" class="form-check-input" value="radioLower">
                      <label class="custom-control-label" for="radioLower">Lowercase</label>
                    </div>
                    <!-- <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioRegex" name="customRadio" class="form-check-input">
                      <label class="custom-control-label" for="radioRegex">Regex</label>
                    </div> -->
                    <!-- <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioSubtr" name="customRadio" class="form-check-input">
                      <label class="custom-control-label" for="radioSubtr">Substring</label>
                    </div> -->
                  </div>
                </div>
                
                <hr>

                <!-- Element Div -->
                <div class="col-lg-6 radio-element" id="elementRemoveRow">
                  <div class="form-group">
                      <label class="form-label">Pilih Row [0 - <?= $totalData; ?>]</label>
                      <input name="rowRemove" type="number" class="form-control"  value="0" min="0" max="<?= $totalData; ?>" >
                  </div>
                </div>

                <div class="form-group radio-element" id="elementReplace">

                  <div class="form-group col-lg-6">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $noKolom-1; ?>]</label>
                      <input name="rowReplaceKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $noKolom-1; ?>" >
                  </div>

                  <div class="row row-cols-auto mt-2">
                    <div class="form-group">
                        <label class="form-label">Old Value</label>
                        <input name="rowReplaceOld" type="text" class="form-control" value="" >
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Value</label>
                        <input name="rowReplaceNew" type="text" class="form-control" value="" >
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementUpper">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $noKolom-1; ?>]</label>
                      <input name="rowUpperKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $noKolom-1; ?>" >
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementLower">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $noKolom-1; ?>]</label>
                      <input name="rowLowerKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $noKolom-1; ?>" >
                  </div>
                </div>

                <!-- <div class="col-lg-6 radio-element" id="elementRegex">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $noKolom-1; ?>]</label>
                      <input name="rowRemove" type="number" class="form-control"  value="1" min="1" max="<?= $noKolom-1; ?>" >
                  </div>

                  <div class="form-group mt-2">
                        <label class="form-label">Regex Value</label>
                        <input name="rowRemove" type="text" class="form-control" value="" >
                  </div>
                </div> -->

                <!-- Button -->
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <input name="tipe" type="text" class="form-control" value="1" hidden>
                  <textarea id="jsondata" name="dataCsv"  hidden><?= json_encode($data_csv) ?></textarea>
                  <button class="btn btn-primary me-md-2" type="submit">Next</button>
                  <!-- <button class="btn btn-primary" type="button">Next</button> -->
                </div>
              </div>

              
              </form>
            </div>
          </div>
      
      </div>
     
    <!--Row-->
</div>
<!---Container Fluid-->

<script>

  hideAllElementRadio();
  
  $("#radioRowRemove").click(function() {
    hideAllElementRadio();
    $("#elementRemoveRow").show();
  });

  $("#radioReplace").click(function() {
    hideAllElementRadio();
    $("#elementReplace").show();
  });

  $("#radioUpper").click(function() {
    hideAllElementRadio();
    $("#elementUpper").show();
  });

  $("#radioLower").click(function() {
    hideAllElementRadio();
    $("#elementLower").show();
  });

  $("#radioRegex").click(function() {
    hideAllElementRadio();
    $("#elementRegex").show();
  });

  function hideAllElementRadio() {
    const radioElement = document.querySelectorAll('.radio-element').forEach(function(el) {
      el.style.display = 'none';
    });
  }
  

</script>