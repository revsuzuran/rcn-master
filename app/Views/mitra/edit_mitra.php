<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Mitra</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Mitra</label>
                        <input id="nama_mitra" type="text" class="form-control" placeholder="Mitra LinkQu" value="<?= $data_mitra->nama_mitra ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" value="<?= $data_mitra->email ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Hp</label>
                        <input id="phone" type="number" class="form-control" value="<?= $data_mitra->phone ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>                        
                        <textarea class="form-control" rows="3" id="alamat"><?= $data_mitra->alamat ?></textarea>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="uname" type="text" class="form-control" value="<?= $data_mitra->uname ?>">
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="pass" type="text" class="form-control" value="">
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

    var namaMitra = $('#nama_mitra').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var alamat = $('#alamat').val();
    var uname = $('#uname').val();
    var pass = $('#pass').val();

    $.ajax({
        url : "<?= base_url('mitra/update_mitra') ?>",
        method : "POST",
        data : {namaMitra : namaMitra, email : email, phone : phone, alamat : alamat, uname : uname, pass : pass },
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                window.location.replace("<?= base_url('mitra') ?>")
            }
        }
    });

});
</script>