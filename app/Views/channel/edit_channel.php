<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Channel</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Channel</label>
                        <input id="nama_channel" type="text" class="form-control" placeholder="Mitra LinkQu" value="<?= $data_channel->nama_channel ?>" required>
                    </div>

                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Fee Admin</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen_admin" name="is_persen_admin">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee_admin" type="email" class="form-control mt-2" value="<?= $data_channel->fee_admin->nilai ?>" required>                       
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee 1</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen1" name="is_persen1">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee1" type="number" class="form-control mt-2" value="<?= $data_channel->fee1->nilai ?>" required>                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee 2</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen2" name="is_persen2">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee2" type="number" class="form-control mt-2" value="<?= $data_channel->fee2->nilai ?>" required>                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee 3</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen3" name="is_persen3">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee3" type="number" class="form-control mt-2" value="<?= $data_channel->fee3->nilai ?>" required>                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee 4</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen4" name="is_persen4">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee4" type="number" class="form-control mt-2" value="<?= $data_channel->fee4->nilai ?>" required>                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee 5</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_persen5" name="is_persen5">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Is Percentage</label>
                        </div>
                        <input id="fee5" type="number" class="form-control mt-2" value="<?= $data_channel->fee5->nilai ?>" required>                        
                    </div>
                  

                    <button class="btn btn-primary" id="simpanMitra">Simpan Data</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanMitra').on('click', function(event) {

    var namaChannel = $('#nama_channel').val();
    var feeAdmin = $('#fee_admin').val();
    var fee1 = $('#fee1').val();
    var fee2 = $('#fee2').val();
    var fee3 = $('#fee3').val();
    var fee4 = $('#fee4').val();
    var fee5 = $('#fee5').val();

    const is_persen_admin = $('#is_persen_admin').is(":checked");
    const is_persen1 = $('#is_persen1').is(":checked");
    const is_persen2 = $('#is_persen2').is(":checked");
    const is_persen3 = $('#is_persen3').is(":checked");
    const is_persen4 = $('#is_persen4').is(":checked");
    const is_persen5 = $('#is_persen5').is(":checked");

    $.ajax({
        url : "<?= base_url('mitra/channel/update') ?>",
        method : "POST",
        data : {namaChannel : namaChannel, feeAdmin : feeAdmin, fee1 : fee1, fee2 : fee2, fee3 : fee3, fee4 : fee4, fee5 : fee5, is_persen_admin : is_persen_admin, is_persen1 : is_persen1, is_persen2 : is_persen2, is_persen3 : is_persen3, is_persen4 : is_persen4, is_persen5 : is_persen5},
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                window.location.replace("<?= base_url('mitra/channel') ?>")
            }
        }
    });

});


if("<?= $data_channel->fee1->is_prosentase ?>" == 1) {
    $("#is_persen1").prop("checked", true);
}

if("<?= $data_channel->fee2->is_prosentase ?>" == 1) {
    $("#is_persen2").prop("checked", true);
}

if("<?= $data_channel->fee3->is_prosentase ?>" == 1) {
    $("#is_persen3").prop("checked", true);
}

if("<?= $data_channel->fee4->is_prosentase ?>" == 1) {
    $("#is_persen4").prop("checked", true);
}

if("<?= $data_channel->fee5->is_prosentase ?>" == 1) {
    $("#is_persen5").prop("checked", true);
}

if("<?= $data_channel->fee_admin->is_prosentase ?>" == 1) {
    $("#is_persen_admin").prop("checked", true);
}

</script>