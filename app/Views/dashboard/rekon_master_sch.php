<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
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
                        <th>Jadwal Rekon</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>No</th>
                        <th>Nama Rekon</th>
                        <th>Jadwal Rekon</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php
                      $no = 1;
                      
                      foreach ($data_rekon as $row) { 
                        $btnAct = "<button class='btn btn-primary btn-sm btnShowData' type='button' data-id='" . $row['id_rekon'] . "' id='btnShowData'>Edit</button>";

                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_rekon'] ?></td>     
                            <td><?= "Jam " . $row['detail_schedule']['time']; ?></td>  
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

<script>
  
    $('.btnShowData').on('click', function(event) {
        var id_rekon= $(this).data('id');

        $.ajax({
            url : "<?= base_url('rekon/data_rekon_sch_temp') ?>",
            method : "POST",
            data : {id_rekon: id_rekon},
            async : true,
            success: function($hasil){
                window.location.replace("<?= base_url('rekon/data_rekon_sch') ?>");
            }
        });
    });

</script>