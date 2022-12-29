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
                  <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Time Submit</th>
                        <th>Time Completed</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Time Submit</th>
                        <th>Time Completed</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($data_rekon as $row) {
                        if (!isset($row['is_proses']) || $row['is_proses'] == "") continue; 
                        
                        if($row['is_proses'] == "pending") {
                          $status = '<p style="background: #ffc107;display: inline;padding: 4px 10px;border-radius: 5px;" type="button">Pending</p>';
                          $btnAct = "-";
                        } else if($row['is_proses'] == "sukses") {
                          $status = '<p style="background: #66bb6a;display: inline;padding: 4px 10px;border-radius: 5px;color:#fff;" type="button">Sukses</p>';
                          $btnAct = "<button class='btn btn-primary btn-sm btnShowResult' type='button' data-id='". $row['id_rekon'] ."' id='btnShowResult'>Detail</button>";
                        } else if($row['is_proses'] == "proses") {
                          $status = '<p style="background: #6777ef;display: inline;padding: 4px 10px;border-radius: 5px;color:#fff;" type="button">Sedang Proses</p>';
                          $btnAct = "-";
                        } else if($row['is_proses'] == "gagal") {
                          $status = '<p style="background: #fc544b;display: inline;padding: 4px 10px;border-radius: 5px;color:#fff;" type="button">Gagal</p>';
                          $btnAct = "-";
                        } else {
                          $status = "";
                          $btnAct = "-";
                        }
                        
                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_rekon'] ?></td>     
                            <td><?= $row['timestamp'] ?></td>      
                            <td><?= isset($row['timestamp_complete']) ? $row['timestamp_complete'] : "-" ?></td> 
                            <td><?= $status; ?></td>  
                            <td><?= $btnAct ?></td>                      
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

<!-- Modal -->
<!-- <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Peringatan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah kamu yakin ingin menghapus pengguna ini ?<br>
        <strong>SEMUA DATA PENGGUNA TERMASUK WEBSITE AKAN TERHAPUS!!</strong>
        <input type="hidden" id="iduser" value=""/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm text-danger" id="hapusBtn">Hapus</button>
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div> -->

<script>
  
    $('.btnShowResult').on('click', function(event) {
        var id_rekon= $(this).data('id');

        $.ajax({
            url : "<?= base_url('rekon/rekon_result_post') ?>",
            method : "POST",
            data : {id_rekon: id_rekon},
            async : true,
            success: function($hasil){
                window.location.replace("<?= base_url('rekon/rekon_result') ?>");
            }
        });
    });

</script>