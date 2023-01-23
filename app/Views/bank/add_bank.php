<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Bank</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input id="nama_bank" type="text" class="form-control" placeholder="Bank Mandiri" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input id="norek" type="text" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Bank</label>
                        <input id="kode_bank" type="text" class="form-control" value="" required>
                    </div>

                    <button class="btn btn-primary" id="simpanBank">Simpan Data</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanBank').on('click', function(event) {

    var nama_bank = $('#nama_bank').val();
    var norek = $('#norek').val();
    var kode_bank = $('#kode_bank').val();

    $.ajax({
        url : "<?= base_url('mitra/bank/save') ?>",
        method : "POST",
        data : {nama_bank : nama_bank, norek : norek, kode_bank : kode_bank},
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                window.location.replace("<?= base_url('mitra/bank') ?>")
            }
        }
    });

});
</script>