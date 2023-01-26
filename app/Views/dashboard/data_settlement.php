<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <a type="button" class="btn btn-success" href="<?= base_url('settlement') ?>">Refresh</a>
    </div>

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Settlement</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Count #1</th>
                        <th>Count #2</th>
                        <th>Amount #1</th>
                        <th>Amount #2</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Count #1</th>
                        <th>Count #2</th>
                        <th>Amount #1</th>
                        <th>Amount #2</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($data_rekon as $row) {
                        if (!isset($row['is_proses']) || $row['is_proses'] == "") continue; 
                        
                        if($row['is_proses'] == "disburse") {
                          $status = "<button class='btn btn-info btn-sm' type='button'>Ready Disburse</button>";
                          $btnAct = "<button class='btn btn-primary btn-sm btnShowResult mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResult' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail'><i class='fas fa-file-alt'></i></button>";
                          $btnActDetail = "<button class='btn btn-success btn-sm btnShowResultAmount mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResultAmount' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail Amount'><i class='fas fa-file-invoice-dollar'></i></button>";
                          $btnActDisburse = "<button class='btn btn-danger btn-sm btnDisburse mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnDisburse' data-bs-toggle='tooltip' data-bs-placement='top' title='Proses Disburse'><i class='fas fa-donate'></i></button>";
                        } else {
                          $status = "";
                          $btnAct = "-";
                          $btnActDetail = "-";
                        }

                        /* Compare */
                        $percentageSatu = '-';
                        $totalDataSatu = isset($row['data_result1']) ? $row['data_result1']->compare_result->total_data : "-";
                        $totalMatchSatu = isset($row['data_result1']) ? $row['data_result1']->compare_result->total_match : "-";
                        $totalUnMatchSatu = isset($row['data_result1']) ? $row['data_result1']->compare_result->total_unmatch : "-";
                        if($totalDataSatu != "-" || $totalMatchSatu != "-" || $totalUnMatchSatu != "-") {
                          $percentageSatu = ($totalMatchSatu / $totalDataSatu) * 100;
                        }


                        $percentageDua = '-';
                        $totalDataDua = isset($row['data_result2']) ? $row['data_result2']->compare_result->total_data : "-";
                        $totalMatchDua = isset($row['data_result2']) ? $row['data_result2']->compare_result->total_match : "-";
                        $totalUnMatchDua = isset($row['data_result2']) ? $row['data_result2']->compare_result->total_unmatch : "-";
                        if($totalDataDua != "-" || $totalMatchDua != "-" || $totalUnMatchDua != "-") {
                          $percentageDua = ($totalMatchDua / $totalDataDua) * 100;
                        }

                        /* Sum */
                        $percentageSumSatu = "-";
                        $totalSumDataSatu = isset($row['data_result1']->sum_result->total_sum) ? $row['data_result1']->sum_result->total_sum : "0";
                        $sumMatchSatu = isset($row['data_result1']->sum_result->total_sum_match) ? $row['data_result1']->sum_result->total_sum_match : "0";
                        $sumUnMatchSatu = isset($row['data_result1']->sum_result->total_sum_unmatch) ? $row['data_result1']->sum_result->total_sum_unmatch : "0";
                        if($totalSumDataSatu != "0" || $sumMatchSatu != "0" || $sumUnMatchSatu != "0") {
                          $percentageSumSatu = ($sumMatchSatu / $totalSumDataSatu) * 100;
                        }

                        $percentageSumDua = "-";
                        $totalSumDataDua = isset($row['data_result2']->sum_result->total_sum) ? $row['data_result2']->sum_result->total_sum : "0";
                        $sumMatchDua = isset($row['data_result2']->sum_result->total_sum_match) ? $row['data_result2']->sum_result->total_sum_match : "0";
                        $sumUnMatchDua = isset($row['data_result2']->sum_result->total_sum_unmatch) ? $row['data_result2']->sum_result->total_sum_unmatch : "0";
                        if($totalSumDataDua != "0" || $sumMatchDua != "0" || $sumUnMatchDua != "0") {
                          $percentageSumDua = ($sumMatchDua / $totalSumDataDua) * 100;
                        }
                        
                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_rekon'] ?></td>     
                            <td><?= isset($row['tanggal_rekon']) ? $row['tanggal_rekon']  : "-" ?></td> 
                            <td><?= round($percentageSatu, 2) . "% ($totalDataSatu/$totalMatchSatu/$totalUnMatchSatu)" ?></td>                              
                            <td><?= round($percentageDua,2) ."% ($totalDataDua/$totalMatchDua/$totalUnMatchDua)" ?></td>  
                            <td><?= round($percentageSumSatu, 2) . "% (". rupiah($totalSumDataSatu) . "/" . rupiah($sumMatchSatu) . "/" . rupiah($sumUnMatchSatu) . ")" ?></td>
                            <td><?= round($percentageSumDua, 2) . "% (". rupiah($totalSumDataDua) . "/" . rupiah($sumMatchDua) . "/" . rupiah($sumUnMatchDua) . ")" ?></td>  
                            <td><?= $status; ?></td>  
                            <td><?= $btnAct . " " . $btnActDetail . " " . $btnActDisburse ?></td>                      
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
        </div>
        <!-- tables-->
       
    </div>
    <!--Row-->
</div>
<!---Container Fluid-->

<script>
  
    $('.btnShowResult').on('click', function(event) {
        var id_rekon= $(this).data('id');
        var id_rekon_result= $(this).data('id_rekon_result');

        $.ajax({
            url : "<?= base_url('rekon/rekon_result_post') ?>",
            method : "POST",
            data : {id_rekon: id_rekon, id_rekon_result: id_rekon_result},
            async : true,
            success: function($hasil){
                window.location.replace("<?= base_url('rekon/rekon_result') ?>");
            }
        });
    });

    $('.btnShowResultAmount').on('click', function(event) {
        var id_rekon= $(this).data('id');
        var id_rekon_result= $(this).data('id_rekon_result');

        $.ajax({
            url : "<?= base_url('rekon/rekon_result_post') ?>",
            method : "POST",
            data : {id_rekon: id_rekon, id_rekon_result: id_rekon_result},
            async : true,
            success: function($hasil){
              console.log($hasil)
                window.location.replace("<?= base_url('rekon/rekon_result_amount') ?>");
            }
        });
    });

    $('.btnDisburse').on('click', function(event) {
        var id_rekon_result= $(this).data('id_rekon_result');
        
        var key = "<?= getenv("encryption_key") ?>";        
        var plaintext = JSON.stringify(id_rekon_result);
        let encryption = new Encryption();
        var encryptedData = encryption.encrypt(plaintext, key);
        
        $.ajax({
            url : "<?= base_url('settlement/proses_temp') ?>",
            method : "POST",
            data : {'encryptedData': encryptedData},
            async : true,
            success: function(hasil){
                window.location.replace("<?= base_url('settlement/proses_settlement') ?>");
            }
        });
    });
   

</script>