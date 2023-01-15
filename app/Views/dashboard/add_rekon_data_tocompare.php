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
        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/save_cleansing'); ?>">
                  
          <div class="row mb-3">
                <!-- Value Compare -->
                <div class="col-12">
                  <div class="card mb-4">
                      <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                            <h6 class="m-0 font-weight-bold text-light">Manage Data Rekon</h6>
                      </div>
                      <div class="card-body">
                      
                        <div class="row mb-3">
                        
                          <div class="form-group col-6 mt-2">
                              <label class="form-label">Pilih Kolom Compare</label>
                              <select class="form-select mb-3" name="opt_compare" id="opt_compare">
                                <?php foreach ($data_kolom as $rowData) { ?>
                                  <option value="<?= $rowData['kolom_index'] ?>"><?= $rowData['kolom_name'] ?></option>
                                <?php } ?>
                              </select>
                              <button class="btn btn-primary me-md-2" type="button" id="addCompare">Add</button>
                          </div>
                          

                          <div class="form-group col-6 mt-2">
                            <label class="form-label">Kolom Compare</label>
                            <?php if (count($data_kolom_db) == 0) { ?>
                              <div>
                                <p class="fw-bold fst-italic"> - No Data -</p>
                              </div>
                            <?php } ?>
                            <?php foreach($data_kolom_db as $rowData) { ?>
                              <div class="input-group mb-2">
                                <input class="form-control" value="<?= $rowData['kolom_name'] ?>" style="background: white;" disabled>                     
                                <div class="input-group-append">
                                  <button class="btn btn-danger rmCompare" type="button" data-index="<?= $rowData['kolom_index'] ?>" data-name="<?= $rowData['kolom_name'] ?>" >Remove</button>
                                </div>                      
                              </div>
                            <?php } ?>
                          </div>
                      </div>

                      <hr>

                      <!-- Value SUM -->
                      <div class="row mb-3">
                        
                          <div class="form-group col-6 mt-2">
                              <label class="form-label">Pilih Kolom to SUM</label>
                              <select class="form-select mb-3" name="opt_sum" id="opt_sum">
                                <?php foreach ($data_kolom_sum as $rowData) { ?>
                                  <option value="<?= $rowData['kolom_index'] ?>"><?= $rowData['kolom_name'] ?></option>
                                <?php } ?>
                              </select>
                              <button class="btn btn-primary me-md-2" type="button" id="addSum">Add</button>
                          </div>
                          

                          <div class="form-group col-6 mt-2">
                            <label class="form-label">Kolom to SUM</label>
                            <?php if (count($data_kolom_sum_db) == 0) { ?>
                              <div>
                                <p class="fw-bold fst-italic"> - No Data -</p>
                              </div>
                            <?php } ?>
                            <?php foreach($data_kolom_sum_db as $rowData) { ?>
                              <div class="input-group mb-2">
                                <input class="form-control" value="<?= $rowData['kolom_name'] ?>" style="background: white;" disabled>                     
                                <div class="input-group-append">
                                  <button class="btn btn-danger rmSum" type="button" data-index="<?= $rowData['kolom_index'] ?>" data-name="<?= $rowData['kolom_name'] ?>" >Remove</button>
                                </div>                      
                              </div>
                            <?php } ?>
                          </div>
                      </div>

                      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?php if ($_SESSION['tipe'] == "2") {
                           ?>
                          <!-- <button class="btn btn-warning me-md-2" type="submit">Finish</button> -->

                          <a href="<?php echo base_url('rekon/cleansing_data');?>" class="btn btn-danger">Kembali</a>  
                          <!-- <a href="<?php echo base_url('rekon/rekon_preview');?>" class="btn btn-warning">Next (Preview Data)</a> -->
                          <button type="button" class="btn btn-secondary btnNext" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                              Next (Preview Data)
                          </button>
                        <?php } else {  ?>
                          <!-- <button class="btn btn-primary me-md-2" type="submit">Next (Data #2)</button> -->

                          <a href="<?php echo base_url('rekon/cleansing_data');?>" class="btn btn-danger">Kembali</a>  
                          <!-- <a href="<?php echo base_url('rekon/add_rekon_next');?>" class="btn btn-primary">Next (Data #2)</a> -->
                          <button type="button" class="btn btn-secondary btnNext" data-bs-toggle="modal" data-bs-target="#staticBackdrop" >
                              Next (Data #2)
                          </button>

                        <?php }  ?>
                      </div>
                    </div>
                  </div>
                </div>

              <input name="tipe" type="text" class="form-control" value="1" hidden>
          </div>

          <!-- Tables -->
          <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Rekon</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="table-dark">
                        <tr>
                          <?php
                          foreach ($data_csv as $row) {
                            echo "<th>INDEX</th>";
                            foreach ($row['data_row'] as $key => $data) {
                          ?>
                              <th><?= 'KOLOM' . ($key + 1); ?> </th>
                          <?php } break; }  ?>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                        <?php
                          foreach ($data_csv as $row) {
                            echo "<th>INDEX</th>";
                            foreach ($row['data_row'] as $key => $data) {
                          ?>
                              <th><?= 'KOLOM' . ($key + 1); ?> </th>
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
          <textarea id="jsondata" name="dataCsv"  hidden><?= json_encode($data_csv) ?></textarea>

        </form>
      </div>
    <!--Row-->
</div>
<!---Container Fluid-->


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <!-- <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/upload_with_setting'); ?>"> -->
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Save Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Setting Name" aria-label="Recipient's username" id="namaFile_">
        </div>

        <table>
          <tbody>
              <tr>
                <td class="fw-lighter"># Delimiter</td>
                <td class="fw-lighter">&nbsp;:&nbsp;</td>
                <td><code class="highlighter-rouge"> <?= $data_setting['delimiter']; ?> </code></td>
              </tr>
              <?php foreach($data_setting['clean_rule'] as $row) { ?>
                <tr>
                  <td class="fw-lighter"># Clean Rule</td>
                  <td class="fw-lighter">&nbsp;:&nbsp;</td>
                  <td>KOLOM <?= $row["index_kolom"] ?> <code class="highlighter-rouge"> [<?= $row["rule"] ?>] </code> <?= $row["rule_value"] ?></code></td>
                </tr>
              <?php } ?>
              <?php foreach($data_setting['kolom_compare'] as $row) { ?>
                <tr>
                  <td class="fw-lighter"># Kolom to Compare</td>
                  <td class="fw-lighter">&nbsp;:&nbsp;</td>
                  <td><?= $row["kolom_name"] ?> </code></td>
                </tr>
              <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnSimpan">Lanjut & Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnNextOk">Lanjut</button>
      </div>

    <!-- </form> -->
    </div>
  </div>
</div>


<script>
  
  $("#btnNextOk").on("click", function() {
    var sess = "<?= $_SESSION['tipe'] ?>";
    if(sess == "1") {
      window.location.replace("<?= base_url('add_rekon_next') ?>")
    } else {
      window.location.replace("<?= base_url('rekon_preview') ?>")
    }
  });

  $("#btnSimpan").click(function() {
        
        $.ajax({
            url : "<?= base_url('setting/save_setting') ?>",
            method : "POST",
            data : {},
            async : true,
            success: function($result){
               if($result == 'sukses'){
                  var sess = "<?= $_SESSION['tipe'] ?>";
                  if(sess == "1") {
                    window.location.replace("<?= base_url('add_rekon_next') ?>")
                  } else {
                    window.location.replace("<?= base_url('rekon_preview') ?>")
                  }
               }
            }
        });   
  });  

  $("#addCompare").click(function() {
        var id = $('#opt_compare').val();
        var idt = $('#opt_compare option:selected').text();
        console.log(id);
        console.log(idt);
        $.ajax({
            url : "<?= base_url('rekon/add_kolom_compare') ?>",
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

  $(".rmCompare").click(function() {
      var indx= $(this).data('index');
      var nme= $(this).data('name');
      
      var id = $('#opt_compare').val();
      var idt = $('#opt_compare option:selected').text();
      console.log(id);
      console.log(idt);
      $.ajax({
          url : "<?= base_url('rekon/rm_kolom_compare') ?>",
          method : "POST",
          data : {"rekon_index": indx, "rekon_name": nme},
          async : true,
          success: function($result){
             if($result == 'sukses'){
                location.reload();
             }
          }
      });   
  });  

  $(".rmSum").click(function() {
      var indx= $(this).data('index');
      var nme= $(this).data('name');

      $.ajax({
          url : "<?= base_url('rekon/rm_kolom_sum') ?>",
          method : "POST",
          data : {"rekon_index": indx, "rekon_name": nme},
          async : true,
          success: function($result){
             if($result == 'sukses'){
                location.reload();
             }
          }
      });   
  });  

  $("#addSum").click(function() {      
      var id = $('#opt_sum').val();
      var idt = $('#opt_sum option:selected').text();
      console.log(id);
      console.log(idt);
      $.ajax({
          url : "<?= base_url('rekon/add_kolom_sum') ?>",
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