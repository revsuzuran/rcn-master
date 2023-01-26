<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    
      <div class="row mb-3">               
          <!-- Tables -->
          <div class="col-lg-12">
            <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/save_compare_sum'); ?>">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Rekon SUM #1</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="table-dark">
                        <tr>
                          <?php
                          $indexDataSatu = array();
                          foreach ($data_compare_satu as $row) { array_push($indexDataSatu, $row['kolom_index'])?>
                              <th><?= $row['kolom_name'] ?> </th>
                          <?php } ?>
                        </tr>
                      </thead>
                      <tfoot class="table-dark">
                        <tr>
                          <?php
                          foreach ($data_compare_satu as $row) { ?>
                              <th><?= $row['kolom_name'] ?> </th>
                          <?php } ?>
                        </tr>
                      </tfoot>
                      <tbody>

                      <?php
                      $kolomName = "";
                      foreach ($data_compare_satu_db as $row) { ?> 
                          <tr>
                          <?php foreach ($row['data_row'] as $key => $data) {
                          if (!in_array($key, $indexDataSatu)) continue;?>
                              <td><?= $data; ?></td>
                          <?php } ?>
                          </tr>
                      <?php  } ?>
                        
                      </tbody>
                    </table>
                  </div>

                  <hr>

                  <div class="card-body">

                        <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                          <h6 class="m-0 font-weight-bold text-light">Compare Data Options</h6>
                        </div>

                        <div class="row row-cols-auto mt-2">
                            <div class="form-group mt-2 col-6">
                                <label class="form-label">Pilih Kolom (Data Rekon #1)</label>
                                <select class="form-select mb-3" name="kolom_compare_satu">
                                  <?php foreach ($data_compare_satu as $row) { ?>
                                    <option value="<?= $row['kolom_index'] ?>"><?= $row['kolom_name'] ?></option>
                                  <?php } ?>
                                </select>
                                <label class="form-label">Pilih Kolom to Compare (Data Rekon #2)</label>
                                <select class="form-select mb-3" name="kolom_compare_satu2">
                                  <?php foreach ($data_compare_dua as $row) { ?>
                                    <option value="<?= $row['kolom_index'] ?>"><?= $row['kolom_name'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                            <div class="form-group mt-2 col-6">
                                <label class="form-label">Data Compare</label>
                                <?php foreach ($data_compare_satu as $row) { ?>
                                  <div>
                                    <p style="margin: 3px;border-bottom: 3.5px solid #e74c3c;" class="fw-lighter mb-3"> <?= $row['kolom_name'] ?> =&gt;  <?= $row['to_compare_name'] ?><code class="highlighter-rouge"> [<?= $row['rule'] ?><?php if($row['rule'] != "equal") echo "='" . $row['rule_value'] . "'" ?>]</code> </p>
                                  </div>
                                <?php } ?>
                                
                            </div> 
                        </div>
                        

                        <div class="form-group">
                            <!-- Radio Div -->
                            <label class="form-label">Pilih Options Compare</label>
                            <div class="row row-cols-auto">
                              <div class="custom-control custom-radio m-2 ">
                                <input type="radio" id="radioEqualSatu" name="compareRadioSatu" class="form-check-input" value="equal">
                                <label class="custom-control-label" for="radioEqualSatu">Equals</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioContainSatu" name="compareRadioSatu" class="form-check-input" value="contain">
                                <label class="custom-control-label" for="radioContainSatu">Contains</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioBeginSatu" name="compareRadioSatu" class="form-check-input" value="begin">
                                <label class="custom-control-label" for="radioBeginSatu">Begin With</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioEndSatu" name="compareRadioSatu" class="form-check-input" value="end">
                                <label class="custom-control-label" for="radioEndSatu">Ends With</label>
                              </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">

                          <div class="col-lg-6 radio-element" id="elementContain">
                            <div class="form-group">
                                <label class="form-label">Contain Value</label>
                                <input name="contain" type="text" class="form-control">
                            </div>
                          </div>

                          <div class="col-lg-6 radio-element" id="elementBegin">
                            <div class="form-group">
                                <label class="form-label">Begin Value</label>
                                <input name="begin" type="text" class="form-control">
                            </div>
                          </div>

                          <div class="col-lg-6 radio-element" id="elementEnd">
                            <div class="form-group">
                                <label class="form-label">End Value</label>
                                <input name="end" type="text" class="form-control">
                            </div>
                          </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                          <input type="text" name="tipe" class="form-check-input" value="1" hidden>
                          <button class="btn btn-secondary" type="submit" id="saveCompareDataSatu">Save & Preview</button>
                        </div>
                  
                  </div>

                </div>
            </form>  
          </div>
          <!-- Tables-->

          <!-- Tables -->
          <div class="col-lg-12">
            <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/save_compare_sum'); ?>">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Rekon SUM #2</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="table-dark">
                        <tr>
                          <?php
                          $indexDataDua = array();
                          foreach ($data_compare_dua as $row) { array_push($indexDataDua, $row['kolom_index'])?>
                              <th><?= $row['kolom_name'] ?> </th>
                          <?php } ?>
                        </tr>
                      </thead>
                      <tfoot class="table-dark">
                        <tr>
                          <?php
                          foreach ($data_compare_dua as $row) { ?>
                              <th><?= $row['kolom_name'] ?> </th>
                          <?php } ?>
                        </tr>
                      </tfoot>
                      <tbody>

                      <?php
                      foreach ($data_compare_dua_db as $row) { ?> 
                          <tr>
                          <?php foreach ($row['data_row'] as $key => $data) {
                          if (!in_array($key, $indexDataDua)) continue;?>
                              <td><?= $data; ?></td>
                          <?php } ?>
                          </tr>
                      <?php  } ?>
                        
                      </tbody>
                    </table>
                  </div>

                  <hr>

                  <div class="card-body">

                        <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                          <h6 class="m-0 font-weight-bold text-light">Compare Data Options</h6>
                        </div>

                        <div class="row row-cols-auto mt-2">
                            <div class="form-group mt-2 col-6">
                                <label class="form-label">Pilih Kolom (Data Rekon #2)</label>
                                <select class="form-select mb-3" name="kolom_compare_dua">
                                  <?php foreach ($data_compare_dua as $row) { ?>
                                    <option value="<?= $row['kolom_index'] ?>"><?= $row['kolom_name'] ?></option>
                                  <?php } ?>
                                </select>
                                <label class="form-label">Pilih Kolom to Compare (Data Rekon #1)</label>
                                <select class="form-select mb-3" name="kolom_compare_dua2">
                                  <?php foreach ($data_compare_satu as $row) { ?>
                                    <option value="<?= $row['kolom_index'] ?>"><?= $row['kolom_name'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                            <div class="form-group mt-2 col-6">
                                <label class="form-label">Data Compare</label>
                                <?php foreach ($data_compare_dua as $row) { ?>
                                  <div>
                                    <p style="margin: 3px;border-bottom: 3.5px solid #e74c3c;" class="fw-lighter mb-3"> <?= $row['kolom_name'] ?> =&gt;  <?= $row['to_compare_name'] ?><code class="highlighter-rouge"> [<?= $row['rule'] ?><?php if($row['rule'] != "equal") echo "='" . $row['rule_value'] . "'" ?>]</code> </p>
                                  </div>
                                <?php } ?>
                                
                            </div> 
                        </div>
                        

                        <div class="form-group">
                            <!-- Radio Div -->
                            <label class="form-label">Pilih Options Compare</label>
                            <div class="row row-cols-auto">
                              <div class="custom-control custom-radio m-2 ">
                                <input type="radio" id="radioEqualDua" name="compareRadioDua" class="form-check-input" value="equal">
                                <label class="custom-control-label" for="radioEqualDua">Equals</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioContainDua" name="compareRadioDua" class="form-check-input" value="contain">
                                <label class="custom-control-label" for="radioContainDua">Contains</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioBeginDua" name="compareRadioDua" class="form-check-input" value="begin">
                                <label class="custom-control-label" for="radioBeginDua">Begin With</label>
                              </div>
                              <div class="custom-control custom-radio m-2">
                                <input type="radio" id="radioEndDua" name="compareRadioDua" class="form-check-input" value="end">
                                <label class="custom-control-label" for="radioEndDua">Ends With</label>
                              </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">

                          <div class="col-lg-6 radio-element-dua" id="elementContainDua">
                            <div class="form-group">
                                <label class="form-label">Contain Value</label>
                                <input name="contain" type="text" class="form-control">
                            </div>
                          </div>

                          <div class="col-lg-6 radio-element-dua" id="elementBeginDua">
                            <div class="form-group">
                                <label class="form-label">Begin Value</label>
                                <input name="begin" type="text" class="form-control">
                            </div>
                          </div>

                          <div class="col-lg-6 radio-element-dua" id="elementEndDua">
                            <div class="form-group">
                                <label class="form-label">End Value</label>
                                <input name="end" type="text" class="form-control">
                            </div>
                          </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                          <input type="text" name="tipe" class="form-check-input" value="2" hidden>
                          <button class="btn btn-secondary" type="submit" id="saveCompareDataDua">Save & Preview</button>
                          <a href="<?php echo base_url('rekon/rekon_preview');?>" class="btn btn-danger">Kembali</a>  
                          <a class="btn btn-primary" href="<?php echo base_url('rekon/add_rekon_finish');?>">Finish</a>
                        </div>

                  </div>

                
            </form>  
          </div>
          <!-- Tables-->

          

      </div>
    <!--Row-->
</div>
<!---Container Fluid-->

<script>

  hideAllElementRadioSatu();

  $("#radioEqualSatu").click(function() {
    hideAllElementRadioSatu();
  });

  $("#radioContainSatu").click(function() {
    hideAllElementRadioSatu();
    $("#elementContain").show();
  });

  $("#radioBeginSatu").click(function() {
    hideAllElementRadioSatu();
    $("#elementBegin").show();
  });

  $("#radioEndSatu").click(function() {
    hideAllElementRadioSatu();
    $("#elementEnd").show();
  });
  
  function hideAllElementRadioSatu() {
    // $(".btn_save_preview").hide();    
    const radioElement = document.querySelectorAll('.radio-element').forEach(function(el) {
      el.style.display = 'none';
    });
  }

  hideAllElementRadioDua();

  $("#radioEqualDua").click(function() {
    hideAllElementRadioDua();
  });

  $("#radioContainDua").click(function() {
    hideAllElementRadioDua();
    $("#elementContainDua").show();
  });

  $("#radioBeginDua").click(function() {
    hideAllElementRadioDua();
    $("#elementBeginDua").show();
  });

  $("#radioEndDua").click(function() {
    hideAllElementRadioDua();
    $("#elementEndDua").show();
  });
  
  function hideAllElementRadioDua() {
    // $(".btn_save_preview").hide();    
    const radioElement = document.querySelectorAll('.radio-element-dua').forEach(function(el) {
      el.style.display = 'none';
    });
  }

  $("#btnNext").click(function() {
      var id = $('#opt_compare').val();
      var idt = $('#opt_compare option:selected').text();
      console.log(id);
      console.log(idt);
      $.ajax({
          url : "<?= base_url('rekon/save_compare_sum') ?>",
          method : "POST",
          data : {"rekon_index": id, "rekon_name": idt},
          async : true,
          success: function($result){
              if($result == 'sukses'){
                location.reload();
              }
          }
      });
  }); 

</script>