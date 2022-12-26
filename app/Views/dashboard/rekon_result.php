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
                <h6 class="m-0 font-weight-bold text-light">Data Rekon #1</h6>
              </div>

              <div class="card-body">
                <div class="row row-cols-auto mt-2">
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">RESULT DATA</label><br>
                        <span>Total Data : <?= $data_rekon_satu[0]->compare_result->total_data; ?></span><br>  
                        <span>Total Match : <?= $data_rekon_satu[0]->compare_result->total_match; ?></span><br>   
                        <span>Total UnMatch : <?= $data_rekon_satu[0]->compare_result->total_unmatch; ?></span>                     
                    </div>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">SUMMERIZE DATA</label><br>         
                        <?php foreach ($data_rekon_satu[0]->sum_result as $row) { ?>
                          <span><?= $row->kolom_name; ?></span><br>  
                          <span><?= "TOTAL : " . rupiah($row->total); ?></span><br>  
                        <?php } ?>              
                    </div> 
                </div>

              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-light">Data Unmatch Rekon #1</h6>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <th><?= "KOLOM " . ($key+1) ?> </th>
                      <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <th><?= "KOLOM " . ($key+1) ?> </th>
                      <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php
                    foreach ($data_rekon_unmatch_satu as $key => $row) { ?> 
                    <tr>
                        <td>1</td>
                      <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <td><?= $rowData; ?></td>
                  <?php  } ?>
                  </tr>
                  <?php  } ?>
                  
                  </tbody>
                </table>
              </div>

            </div>
          </div>
          <!-- Tables-->

          <!-- Tables -->
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                <h6 class="m-0 font-weight-bold text-light">Data Rekon #2</h6>
              </div>

              <div class="card-body">
                <div class="row row-cols-auto mt-2">
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">RESULT DATA</label><br>
                        <span>Total Data : <?= $data_rekon_dua[0]->compare_result->total_data; ?></span><br>  
                        <span>Total Match : <?= $data_rekon_dua[0]->compare_result->total_match; ?></span><br>   
                        <span>Total UnMatch : <?= $data_rekon_dua[0]->compare_result->total_unmatch; ?></span>                     
                    </div>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">SUMMERIZE DATA</label><br>         
                        <?php foreach ($data_rekon_dua[0]->sum_result as $row) { ?>
                          <span><?= $row->kolom_name; ?></span><br>  
                          <span><?= "TOTAL : " . rupiah($row->total); ?></span><br>  
                        <?php } ?>              
                    </div> 
                </div>

              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-light">Data Unmatch Rekon #2</h6>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable2">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <th><?= "KOLOM " . ($key+1) ?> </th>
                      <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <th><?= "KOLOM " . ($key+1) ?> </th>
                      <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php
                    foreach ($data_rekon_unmatch_dua as $key => $row) { ?> 
                    <tr>
                        <td>1</td>
                      <?php foreach ($row['row_data'] as $key => $rowData) { ?>
                          <td><?= $rowData; ?></td>
                  <?php  } ?>
                  </tr>
                  <?php  } ?>
                  
                  </tbody>
                </table>
              </div>

            </div>
          </div>
          <!-- Tables-->

      </div>
    <!--Row-->
</div>
<!---Container Fluid-->

