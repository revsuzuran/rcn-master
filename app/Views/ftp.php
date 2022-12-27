<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                  <h6 class="m-0 font-weight-bold text-light">Setting FTP</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Domain FTP</label>
                        <input id="domain" type="text" class="form-control" name="domain" placeholder="ex : sesuatudomain.com" value="<?= $data_ftp[0]->domain ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" value="<?= $data_ftp[0]->username ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="text" class="form-control" value="<?= $data_ftp[0]->password ?>" required>
                    </div>

                    <button class="btn btn-primary" id="simpanFTP">Update</button>
                </div>
            </div>
        </div>

     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanFTP').on('click', function(event) {

var username = $('#username').val();
var password = $('#password').val();
var domain = $('#domain').val();

$.ajax({
    url : "<?= base_url('update_ftp') ?>",
    method : "POST",
    data : {username: username,password: password, domain: domain},
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