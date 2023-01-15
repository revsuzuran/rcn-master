<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <div>
          <a type="button" class="btn btn-success" href="<?= base_url('rekon/generate_pdf') ?>">Generate PDF 1</a>
          <a type="button" class="btn btn-success" href="<?= base_url('rekon/generate_pdf2') ?>">Generate PDF 2</a>
      </div>
    </div>
    
      <div class="row mb-3">               
          <!-- Tables -->
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                <h6 class="m-0 fw-bolder text-light">Data Rekon #1</h6>
              </div>

              <div class="card-body">
                <div class="row row-cols-auto mt-2">
                    <div class="form-group mt-2 col-6">
                        <label class="form-label fw-bolder">RESULT DATA</label><br>
                        <span>Total Data : <?= $data_rekon_satu[0]->compare_result->total_data; ?></span><br>  
                        <span>Total Match : <?= $data_rekon_satu[0]->compare_result->total_match; ?></span><br>   
                        <span>Total UnMatch : <?= $data_rekon_satu[0]->compare_result->total_unmatch; ?></span>                     
                    </div>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label fw-bolder">SUMMERIZE DATA (<?= $data_rekon_satu[0]->sum_result[0]->kolom_name; ?>)</label><br>    
                        <span><?= "TOTAL : " . rupiah($data_rekon_satu[0]->sum_result[0]->total); ?></span><br>   
                        <span><?= "TOTAL MATCH : " . rupiah((isset($data_rekon_satu[0]->sum_result[0]->total_match) ? $data_rekon_satu[0]->sum_result[0]->total_match : 0)); ?></span><br>  
                        <span><?= "TOTAL UNMATCH : " . rupiah((isset($data_rekon_satu[0]->sum_result[0]->total_unmatch) ? $data_rekon_satu[0]->sum_result[0]->total_unmatch : 0 )); ?></span><br>    
                    </div> 
                </div>

              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 fw-bolder text-light">Data Unmatch</h6>
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      Export Data
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_unmatch/1/0') ?>" target="_blank">Only Unmatch</a></li>
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_unmatch/1/1') ?>" target="_blank">Semua Kolom</a></li>
                    </ul>
                  </div>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php foreach ($data_rekon_unmatch_satu as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_satu)) continue; ?>
                          <td><?= $rowData; ?></td>
                        <?php  } ?>
                    </tr>
                  <?php  } ?>
                  
                  </tbody>
                </table>
              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 fw-bolder text-light">Data Match</h6>
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      Export Data
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_match/1/0') ?>" target="_blank">Only Match Kolom</a></li>
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_match/1/1') ?>" target="_blank">Semua Kolom</a></li>
                    </ul>
                  </div>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_match_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_match_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php foreach ($data_rekon_match_satu as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_satu)) continue; ?>
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
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                <h6 class="m-0 fw-bolder text-light">Data Rekon #2</h6>
              </div>

              <div class="card-body">
                <div class="row row-cols-auto mt-2">
                    <div class="form-group mt-2 col-6">
                        <label class="form-label fw-bolder">RESULT DATA</label><br>
                        <span>Total Data    : <?= $data_rekon_dua[0]->compare_result->total_data; ?></span><br>  
                        <span>Total Match   : <?= $data_rekon_dua[0]->compare_result->total_match; ?></span><br>   
                        <span>Total UnMatch : <?= $data_rekon_dua[0]->compare_result->total_unmatch; ?></span>                     
                    </div>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label fw-bolder">SUMMERIZE DATA (<?= $data_rekon_dua[0]->sum_result[0]->kolom_name; ?>)</label><br>     
                        <span><?= "TOTAL : " . rupiah($data_rekon_dua[0]->sum_result[0]->total); ?></span><br>  
                        <span><?= "TOTAL MATCH : " . rupiah((isset($data_rekon_dua[0]->sum_result[0]->total_match) ? $data_rekon_dua[0]->sum_result[0]->total_match :0 )); ?></span><br>  
                        <span><?= "TOTAL UNMATCH : " . rupiah((isset($data_rekon_dua[0]->sum_result[0]->total_unmatch) ? $data_rekon_dua[0]->sum_result[0]->total_unmatch : 0)); ?></span><br>  
                    </div> 
                </div>

              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 fw-bolder text-light">Data Unmatch</h6>
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      Export Data
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_unmatch/2/0') ?>" target="_blank">Only Unmatch</a></li>
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_unmatch/2/1') ?>" target="_blank">Semua Kolom</a></li>
                    </ul>
                  </div>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable2">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_dua)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_unmatch_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_dua)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php foreach ($data_rekon_unmatch_dua as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_dua)) continue; ?>
                          <td><?= $rowData; ?></td>
                        <?php  } ?>
                    </tr>
                  <?php  } ?>
                  
                  </tbody>
                </table>
              </div>

              <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 fw-bolder text-light">Data Match</h6>
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      Export Data
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_match/2/0') ?>" target="_blank">Only Match Kolom</a></li>
                      <li><a class="dropdown-item" href="<?= base_url('rekon/export_match/2/1') ?>" target="_blank">Semua Kolom</a></li>
                    </ul>
                  </div>
              </div>
              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable2">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_match_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_dua)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </thead>
                  <tfoot class="table-dark">
                    <tr>
                      <th>No</th>
                      <?php foreach ($data_rekon_match_dua as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_dua)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                      <?php break;} ?>
                    </tr>
                  </tfoot>
                  <tbody>
                        
                  <?php foreach ($data_rekon_match_dua as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_dua)) continue; ?>
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

