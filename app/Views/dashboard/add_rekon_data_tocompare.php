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
                      <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
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
                          <a href="<?php echo base_url('rekon/add_rekon_preview');?>" class="btn btn-warning">Next (Preview Data)</a>
                        <?php } else {  ?>
                          <!-- <button class="btn btn-primary me-md-2" type="submit">Next (Data #2)</button> -->
                          <a href="<?php echo base_url('rekon/add_rekon_next');?>" class="btn btn-primary">Next (Data #2)</a>

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
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
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

<script>
  
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