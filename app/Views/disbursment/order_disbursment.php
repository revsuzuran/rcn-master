<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <a type="button" class="btn btn-success" href="<?= base_url('settlement/order_disbursment') ?>">Refresh</a>
    </div>

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Disburse</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Total Amount</th>
                        <th>Total Pay</th>
                        <th>Total Sukses/Gagal</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Tanggal Rekon</th>
                        <th>Total Amount</th>
                        <th>Total Pay </th>
                        <th>Total Sukses/Gagal</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($data_rekon as $row) {
                        
                        if(isset($row['data_disbursment'])) {
                          $totalAmount = $row['data_disbursment']->total_amount;
                          $totalPay = $row['data_disbursment']->total_pay;
                          $totalPaySukses = $row['data_disbursment']->total_sukses_pay;
                          $totalPayGagal = $row['data_disbursment']->total_gagal_pay;
                        } else {
                          $totalAmount = 0;
                          $totalPay = 0;
                          $totalPaySukses = 0;
                          $totalPayGagal = 0;
                        }
                        
                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_rekon'] ?></td>     
                            <td><?= isset($row['tanggal_rekon']) ? $row['tanggal_rekon']  : "-" ?></td> 
                            <td><?= rupiah($totalAmount) ?></td>  
                            <td><?= rupiah($totalPay) ?></td>  
                            <td><?= rupiah($totalPaySukses) . "/" . rupiah($totalPayGagal) ?></td>
                            <td><button class='btn btn-primary btn-sm btnDetail mt-1' type='button' data-id="<?= $row['id_rekon'] ?>" data-id_rekon_result="<?= $row['data_result1']['id_rekon_result'] ?>" data-bs-toggle='tooltip' data-bs-placement='top' title='Detail Disburse'><i class='fas fa-file-alt'></i></button></td>                      
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
  
    $('.btnDetail').on('click', function(event) {
        var id_rekon_result= $(this).data('id_rekon_result');
        
        var key = "<?= getenv("encryption_key") ?>";        
        var plaintext = JSON.stringify(id_rekon_result);
        let encryption = new Encryption();
        var encryptedData = encryption.encrypt(plaintext, key);
        
        $.ajax({
            url : "<?= base_url('settlement/detail_disbursment_temp') ?>",
            method : "POST",
            data : {'encryptedData': encryptedData},
            async : true,
            success: function(hasil){
                window.location.replace("<?= base_url('settlement/detail_disbursment') ?>");
            }
        });
    });
   

</script>