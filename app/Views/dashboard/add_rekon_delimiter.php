

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon/save_delimiter'); ?>">
    <div class="row mb-3">
      
      <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Preview Sample Data</h6>
                </div>
                <div class="card-body m-1">
                    <!-- <label>Rekening Pembayaran</label> -->
                    <div class="form-group pt-0">
                        <label class="form-label">Data CSV</label>
                        <textarea rows="6" id="sample_rekon" type="text" class="form-control" placeholder="" name="sampleCsv"><?= $csv_preview ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Options</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">Input Delimiter</label>
                        <input class="form-control mb-3" name="delimiter" placeholder="Example | ; , -" required></input>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <?php if ($_SESSION['tipe'] == "2") { ?>
                        <a class="btn btn-danger me-md-2" href="<?php echo base_url('rekon/add_rekon_next');?>">Kembali</a>
                        <?php } else {  ?>
                        <a class="btn btn-danger me-md-2"  href="<?php echo base_url('rekon/add');?>">Kembali</a>
                        <?php }  ?>
                        <button class="btn btn-primary me-md-2" type="submit">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <!--Row-->
</div>
<!---Container Fluid-->

<script>
  

    $('.hapus').on('click', function (event) {
        var iduser = $(this).data('id');
        $(".modal-body #iduser").val( iduser );
    });

    $('#hapusBtn').on('click', function(event) {

        var id = $('#iduser').val();
    
        $.ajax({
            url : "<?= base_url('admin/hapus_user') ?>",
            method : "POST",
            data : {id: id},
            async : true,
            dataType : 'html',
            success: function($hasil){
               if($hasil == 'sukses'){
                location.reload();
               }
            }
        });
    });

</script>