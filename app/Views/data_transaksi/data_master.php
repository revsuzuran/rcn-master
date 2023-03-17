<style>
    .table td, .table th {
        font-size: 90%;
    }
</style>

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <div>
          <a type="button" class="btn btn-success" href="<?= base_url('data_transaksi') ?>">Refresh</a>
          <a type="button" class="btn btn-warning" href="<?= base_url('data_transaksi/add') ?>">Add Data</a>
        </div>
    </div>

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Transaksi</h6>
                </div>
                <div class="table-responsive p-3">                 
                  <table class="table align-items-center table-flush" id="dataTableMaster" style="font-size:70% important!;">
                    <thead class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Mitra</th>
                        <th>Nama Transaksi</th>
                        <th>Tanggal Transaksi</th>
                        <th>Found/Not Found</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Mitra</th>
                        <th>Nama Transaksi</th>
                        <th>Tanggal Transaksi</th>
                        <th>Found/Not Found</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($data_transaksi as $row) {                        
                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_mitra'] ?></td>  
                            <td><?= $row['nama_transaksi'] ?></td>   
                            <td><?= $row['tanggal_transaksi'] ?></td>  
                            <td><?= $row['data_found'] ?> / <?= $row['data_not_found'] ?></td>  
                            <td>-</td>                       
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


</script>