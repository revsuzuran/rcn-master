<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <a type="button" class="btn btn-success" href="<?= base_url('rekon') ?>">Refresh</a>
    </div>

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Rekon</h6>
                </div>
                <div class="table-responsive p-3">
                  <?php if(isset($_SESSION['masukAdmin'])) { ?>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="btnCheckBox">Disburse</button>
                    <button type="button" class="btn btn-outline-danger btn-sm mb-3" id="btnSetDisburse">Set Disburse Selected</button>
                  <?php } ?>  
                  <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                      <tr>
                        <th class="check"><input type="checkbox" id="allCheck"/></th>
                        <th>No</th>
                        <th class="onlyAdmin">Mitra</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Count #1</th>
                        <th>Count #2</th>
                        <th>Amount #1</th>
                        <th>Amount #2</th>
                        <th class="onlyAdmin">Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot class="thead-light">
                      <tr>
                        <th class="check"></th>
                        <th>No</th>
                        <th class="onlyAdmin">Mitra</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Count #1</th>
                        <th>Count #2</th>
                        <th>Amount #1</th>
                        <th>Amount #2</th>
                        <th class="onlyAdmin">Status</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($data_rekon as $row) {
                        // echo json_encode($row) . "<br><br>"; 
                        if (!isset($row['is_proses']) || $row['is_proses'] == "") continue; 
                        
                        if($row['is_proses'] == "pending") {
                          // $status = '<p style="background: #ffc107;display: inline;padding: 4px 5px;border-radius: 5px;" type="button">Pending</p>';
                          $status = "<button class='btn btn-secondary btn-sm' type='button'>Pending</button>";
                          $btnAct = "-";
                          $btnActDetail = "-";
                        } else if($row['is_proses'] == "sukses") {
                          $status = "<button class='btn btn-success btn-sm' type='button'>Sukses</button>";
                          $btnAct = "<button class='btn btn-primary btn-sm btnShowResult mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResult' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail'><i class='fas fa-file-alt'></i></button>";
                          $btnActDetail = "<button class='btn btn-success btn-sm btnShowResultAmount mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResultAmount' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail Amount'><i class='fas fa-file-invoice-dollar'></i></button>";
                        } else if($row['is_proses'] == "proses") {
                          $status = "<button class='btn btn-warning btn-sm' type='button'>OnProses</button>";
                          $btnAct = "-";
                          $btnActDetail = "-";
                        } else if($row['is_proses'] == "gagal") {
                          $status = "<button class='btn btn-danger btn-sm' type='button'>Gagal</button>";
                          $btnAct = "-";
                          $btnActDetail = "-";
                        } else if($row['is_proses'] == "disburse") {
                          $status = "<button class='btn btn-info btn-sm' type='button'>Ready Disburse</button>";
                          $btnAct = "<button class='btn btn-primary btn-sm btnShowResult mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResult' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail'><i class='fas fa-file-alt'></i></button>";
                          $btnActDetail = "<button class='btn btn-success btn-sm btnShowResultAmount mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResultAmount' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail Amount'><i class='fas fa-file-invoice-dollar'></i></button>";
                        } else {
                          $status = "";
                          $btnAct = "-";
                          $btnActDetail = "-";
                        }

                        /* disburse */
                        if(isset($row['settlement_status'])) {
                          if($row['settlement_status'] == "00") {
                            $status = "<button class='btn btn-info btn-sm' type='button'>Sukses Disburse</button>";                           
                          } else if($row['settlement_status'] == "05") {
                            $status = "<button class='btn btn-secondary btn-sm' type='button'>Pending Disburse</button>";
                          } else {
                            $status = "<button class='btn btn-danger btn-sm' type='button'>Gagal Disburse</button>";
                          }

                          $btnAct = "<button class='btn btn-primary btn-sm btnShowResult mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResult' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail'><i class='fas fa-file-alt'></i></button>";
                          $btnActDetail = "<button class='btn btn-success btn-sm btnShowResultAmount mt-1' type='button' data-id='". $row['id_rekon'] ."' data-id_rekon_result='". $row['data_result1']['id_rekon_result'] ."' id='btnShowResultAmount' data-bs-toggle='tooltip' data-bs-placement='top' title='Detail Amount'><i class='fas fa-file-invoice-dollar'></i></button>";
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
                        $totalSumDataSatu = isset($row['data_result1']->sum_result->total_sum) ? $row['data_result1']->sum_result->total_sum : 0;
                        $sumMatchSatu = isset($row['data_result1']->sum_result->total_sum_match) ? $row['data_result1']->sum_result->total_sum_match : 0;
                        $sumUnMatchSatu = isset($row['data_result1']->sum_result->total_sum_unmatch) ? $row['data_result1']->sum_result->total_sum_unmatch : 0;
                        if($totalSumDataSatu != 0 || $sumMatchSatu != 0 || $sumUnMatchSatu != 0) {
                          $percentageSumSatu = ($sumMatchSatu / $totalSumDataSatu) * 100;
                        }

                        $percentageSumDua = "-";
                        $totalSumDataDua = isset($row['data_result2']->sum_result->total_sum) ? $row['data_result2']->sum_result->total_sum : 0;
                        $sumMatchDua = isset($row['data_result2']->sum_result->total_sum_match) ? $row['data_result2']->sum_result->total_sum_match : 0;
                        $sumUnMatchDua = isset($row['data_result2']->sum_result->total_sum_unmatch) ? $row['data_result2']->sum_result->total_sum_unmatch : 0;
                        if($totalSumDataDua != 0 || $sumMatchDua != 0 || $sumUnMatchDua != 0) {
                          $percentageSumDua = ($sumMatchDua / $totalSumDataDua) * 100;
                        }
                        
                      ?> 
                        <tr>
                            <td class="check"><input type="checkbox" value="<?= $row['id_rekon_result'] ?>" class="checkBoxHehe"/></td>
                            <td><?= $no++ ?></td>
                            <td class="onlyAdmin"><?= $row['nama_mitra'] ?></td>  
                            <td><?= $row['nama_rekon'] ?></td>     
                            <td><?= isset($row['tanggal_rekon']) ? $row['tanggal_rekon']  : "-" ?></td> 
                            <td><?= round($percentageSatu, 2) . "% ($totalDataSatu/$totalMatchSatu/$totalUnMatchSatu)" ?></td>                              
                            <td><?= round($percentageDua,2) ."% ($totalDataDua/$totalMatchDua/$totalUnMatchDua)" ?></td>  
                            <td><?= round($percentageSumSatu, 2) . "% (". rupiah($totalSumDataSatu) . "/" . rupiah($sumMatchSatu) . "/" . rupiah($sumUnMatchSatu) . ")" ?></td>
                            <td><?= round($percentageSumDua, 2) . "% (". rupiah($totalSumDataDua) . "/" . rupiah($sumMatchDua) . "/" . rupiah($sumUnMatchDua) . ")" ?></td>  
                            <td class="onlyAdmin"><?= $status; ?></td>  
                            <td><?= $btnAct . " " . $btnActDetail ?></td>                      
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

    
    $('#btnSetDisburse').on('click', function(event) {

        var id_rekon_result = [];
        $('input.checkBoxHehe:checkbox:checked').each(function () {
          id_rekon_result.push($(this).val());
        });
        
        var key = "<?= getenv("encryption_key") ?>";        
        var plaintext = JSON.stringify(id_rekon_result);
        let encryption = new Encryption();
        var encryptedData = encryption.encrypt(plaintext, key);

        $.ajax({
            url : "<?= base_url('add_disbursement') ?>",
            method : "POST",
            data : {'encryptedData': encryptedData},
            async : true,
            success: function(hasil){
              if(hasil == 'sukses') {
                Swal.fire('Success!', 'Successfully add Disburse', 'success').then((result) => {
                    location.reload();
                });
              } else {
                Swal.fire('Failed!', 'Failed add Disburse!', 'error')
              }
            }
        });
    });


    $(".check").hide();
    $("#btnSetDisburse").hide();    
    $("#btnCheckBox").on('click', function(event) {
      if($(".check").is(":visible")) {
        $(".check").hide();
      } else {
        $(".check").show();
      }

      if($("#btnSetDisburse").is(":visible")) {
        $("#btnSetDisburse").hide();
      } else {
        $("#btnSetDisburse").show();
      }
    });

    $("#allCheck").on('change', function(e) {
      $("input:checkbox[class=checkBoxHehe]").prop('checked', this.checked);
      
    });

    const isAdmin = "<?= isset($_SESSION['masukAdmin']) ?>";
    if(isAdmin != 1) {
      $(".onlyAdmin").hide();
    }
</script>