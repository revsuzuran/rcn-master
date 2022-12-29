<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Setting FTP</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">FTP Name</label>
                        <input id="ftp_name" type="text" class="form-control" name="domain" placeholder="FTP SERVER LINKQU" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Domain FTP</label>
                        <input id="domain" type="text" class="form-control" name="domain" placeholder="ex : ftp.linkqu.com" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="text" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Folder Path</label>
                        <input id="path" type="text" class="form-control" value="" placeholder="Example : /folderaman/rekon/ or leave it empty">
                    </div>

                    <button class="btn btn-primary" id="simpanFTP">Simpan Data</button>
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
    var path = $('#path').val();
    var ftp_name = $('#ftp_name').val();


    $.ajax({
        url : "<?= base_url('save_ftp') ?>",
        method : "POST",
        data : {username: username,password: password, domain: domain, path:path, ftp_name:ftp_name},
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                window.location.replace("<?= base_url('ftp') ?>")
            }
        }
    });

});
</script>