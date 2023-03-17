<!-- Container Fluid-->
<?php

$totalData = count($data_csv);
$totalKolom = 0;
foreach ($data_csv as $row) {
  $totalKolom = 1;
  foreach ($row['data_row'] as $data) {
    $totalKolom++;
  }
  break;
}?>
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    
      <div class="row mb-3">
        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('data_transaksi/save_cleansing'); ?>">
                  
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Cleaning Data Options</h6>
              </div>
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
                    <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioRegex" name="customRadio" class="form-check-input"  value="radioRegex">
                      <label class="custom-control-label" for="radioRegex">Regex Replace</label>
                    </div>
                    <div class="custom-control custom-radio m-2">
                      <input type="radio" id="radioSubstr" name="customRadio" class="form-check-input" value="radioSubstr">
                      <label class="custom-control-label" for="radioSubstr">Substring</label>
                    </div>
                  </div>
                </div>
                
                <hr>

                <!-- Element Div -->
                <div class="col-lg-6 radio-element" id="elementRemoveRow">
                  <div class="form-group">
                      <label class="form-label">Pilih Row [0 - <?= $totalData; ?>]</label>
                      <!-- <input name="rowRemove" type="number" class="form-control"  value="0" min="0" max="<?= $totalData; ?>" > -->
                      <div class="input-daterange input-group">
                        <input type="number"  value="0" min="0" max="<?= $totalData; ?>" class="input-sm form-control" name="rowRemoveStart" id="inputReplaceStart">
                        <div class="input-group-prepend">
                          <span class="input-group-text">to</span>
                        </div>
                        <input type="number"  value="0" min="0" max="<?= $totalData; ?>" class="input-sm form-control" name="rowRemoveEnd" id="inputReplaceEnd">
                      </div>
                  </div>
                </div>

                <div class="form-group radio-element" id="elementReplace">
                  <div class="form-group col-lg-6">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $totalKolom-1; ?>]</label>
                      <input name="rowReplaceKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $totalKolom-1; ?>" >
                  </div>

                  <div class="row row-cols-auto mt-2">
                    <div class="form-group">
                        <label class="form-label">Old Value</label>
                        <input name="rowReplaceOld" type="text" class="form-control" value="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Value</label>
                        <input name="rowReplaceNew" type="text" class="form-control" value="">
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementUpper">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $totalKolom-1; ?>]</label>
                      <input name="rowUpperKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $totalKolom-1; ?>" >
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementLower">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $totalKolom-1; ?>]</label>
                      <input name="rowLowerKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $totalKolom-1; ?>" >
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementRegex">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $totalKolom-1; ?>]</label>
                      <input name="rowRegexKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $totalKolom-1; ?>" >
                  </div>

                  <div class="row row-cols-auto mt-2">
                    <div class="form-group">
                        <label class="form-label">Old Value</label>
                        <input name="rowRegexReplaceOld" type="text" class="form-control" value="" placeholder="/[^0-9]/">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Value</label>
                        <input name="rowRegexReplaceNew" type="text" class="form-control" value="">
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 radio-element" id="elementSubstrRow">
                  <div class="form-group">
                      <label class="form-label">Pilih Kolom Index [1 - <?= $totalKolom-1; ?>]</label>
                      <input name="rowSubstrKolomIndex" type="number" class="form-control"  value="1" min="1" max="<?= $totalKolom-1; ?>" >
                  </div>

                  <div class="row row-cols-auto mt-2">
                    <div class="form-group">
                        <label class="form-label">Start Index</label>
                        <input name="rowSubstrStart" type="number" class="form-control" value="" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Index</label>
                        <input name="rowSubstrEnd" type="number" class="form-control" value="" placeholder="">
                    </div>
                  </div>
                </div>

                <!-- Button -->
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <button class="btn btn-secondary me-md-2" id="btn_save_preview" type="submit">Save & Preview</button>
                  <button onclick="history.back()" class="btn btn-danger">Kembali</button>  
                  <a href="<?php echo base_url('data_transaksi/add_compare');?>" class="btn btn-primary">Next</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Tables -->
          <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Transaksi Preview (20 Data)</h6>
                    <a href="<?php echo base_url('data_transaksi/cleansing_data/all');?>" class="btn btn-sm btn-light me-md-2">Show All Data</a>
                    <!-- <button class="btn btn-sm btn-light me-md-2" type="button" onclick="history.back()">Show All Data</button>    -->
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="table-dark">
                        <tr>
                          <?php
                          $totalData = count($data_csv);
                          foreach ($data_csv as $row) {
                            $noKolom = 1;
                            echo "<th>INDEX</th>";
                            foreach ($row['data_row'] as $key => $data) {
                          ?>
                              <th><?php 
                              echo 'KOLOM' . $noKolom;
                              $noKolom++; ?> </th>
                          <?php } break; }  ?>
                        </tr>
                      </thead>
                      <tfoot class="table-dark">
                        <tr>
                          <?php
                          $totalData = count($data_csv);
                          foreach ($data_csv as $row) {
                            $noKolom = 1;
                            echo "<th>INDEX</th>";
                            foreach ($row['data_row'] as $key => $data) {
                          ?>
                              <th><?php 
                              echo 'KOLOM' . $noKolom;
                              $noKolom++; ?> </th>
                          <?php } break; }  ?>
                        </tr>
                      </tfoot>
                      <tbody>

                      <?php foreach ($data_csv as $row) { ?> 
                        <tr>
                            <td><?= $row['row_index'] ?></td>
                          <?php foreach ($row['data_row'] as $data) { ?>                        
                            <td><?= $data; ?></td>
                          <?php } ?>
                        </tr>
                      <?php } ?>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
          </div>
          <!-- Tables-->
      
          <input name="tipe" type="text" class="form-control" value="1" hidden>
          <!-- <textarea id="jsondata" name="dataCsv"  hidden><?= json_encode($data_csv) ?></textarea> -->

        </form>
      </div>
    <!--Row-->
</div>
<!---Container Fluid-->

<script>

  hideAllElementRadio();

  $("#btn_save_preview").hide(); 
  
  
  $("#inputReplaceStart").keyup(function() {
    var valueStart = $(this).val();
    var valueEnd = $("#inputReplaceEnd").val();
    var limit = <?= $totalData?>;
    if(valueStart > limit) {
      $(this).val(limit);
      $("#inputReplaceEnd").val(limit);
    }
    
    if(valueEnd < valueStart) {
      $("#inputReplaceEnd").val(valueStart);
    }

  });

  $("#inputReplaceEnd").change(function() {
    var valueEnd = $(this).val();
    var limit = <?= $totalData?>;
    if(valueEnd > limit) {
      $(this).val(limit);
    }

  });

  $("#inputReplaceEnd").keyup(function() {
    var valueEnd = $(this).val();
    var valueStart = $("#inputReplaceStart").val();
    var limit = <?= $totalData?>;
    if(valueEnd < valueStart) {
      $("#inputReplaceEnd").val(valueStart);
    }

  });
  
  $("#radioRowRemove").click(function() {
    hideAllElementRadio();
    $("#elementRemoveRow").show();
    $("#btn_save_preview").show();    
  });

  $("#radioReplace").click(function() {
    hideAllElementRadio();
    $("#elementReplace").show();
    $("#btn_save_preview").show();    
  });

  $("#radioUpper").click(function() {
    hideAllElementRadio();
    $("#elementUpper").show();
    $("#btn_save_preview").show();    
  });

  $("#radioLower").click(function() {
    hideAllElementRadio();
    $("#elementLower").show();
    $("#btn_save_preview").show();    
  });

  $("#radioRegex").click(function() {
    hideAllElementRadio();
    $("#elementRegex").show();
    $("#btn_save_preview").show();    
  });

  $("#radioSubstr").click(function() {
    hideAllElementRadio();
    $("#elementSubstrRow").show();
    $("#btn_save_preview").show();    
  });

  function hideAllElementRadio() {
    // $(".btn_save_preview").hide();    
    const radioElement = document.querySelectorAll('.radio-element').forEach(function(el) {
      el.style.display = 'none';
    });
  }
  

</script>