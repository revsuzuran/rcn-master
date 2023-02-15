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

                    <?php $tipe = isset($data_ftp->tipe_ftp) ? $data_ftp->tipe_ftp : "ftp"; ?>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">Tipe</label>
                        <select class="form-select mb-3" id="tipe">
                            <option value="ftp">FTP</option>
                            <option value="sftp">SFTP</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">FTP Name</label>
                        <input id="ftp_name" type="text" class="form-control" name="domain" placeholder="FTP SERVER LINKQU" value="<?= $data_ftp->ftp_name ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Domain FTP</label>
                        <input id="domain" type="text" class="form-control" name="domain" placeholder="ex : ftp.linkqu.com" value="<?= $data_ftp->domain ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Port FTP</label>
                        <input id="port" type="text" class="form-control" name="port" placeholder="ex : 21" value="<?= isset($data_ftp->port) ? $data_ftp->port : "";  ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" value="<?= $data_ftp->username ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="text" class="form-control" value="<?= $data_ftp->password ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Folder Path</label>
                        <input id="path" type="text" class="form-control" value="<?= $data_ftp->path ?>" placeholder="Example : /folderaman/rekon/ or leave it empty">
                    </div>

                    <input id="id" type="text" class="form-control" value="<?= $data_ftp->_id->__toString() ?>" hidden>
                    <button class="btn btn-primary" id="simpanFTP">Update</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanFTP').on('click', function(event) {

    var ftp_name = $('#ftp_name').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var domain = $('#domain').val();
    var path = $('#path').val();
    var port = $('#port').val();
    var id = $('#id').val();
    var tipe = $('#tipe').find(":selected").val();

    $.ajax({
        url : "<?= base_url('update_ftp') ?>",
        method : "POST",
        data : {username: username,password: password, domain: domain, path:path, ftp_name:ftp_name, id:id, port:port, tipe:tipe},
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                Swal.fire('Updated!', 'Successfully Updated FTP Data', 'success' )
               window.location.replace("<?= base_url('ftp') ?>")
            }
        }
    });

});

const tipe = "<?= $tipe ?>";
$("#tipe").val(tipe).change();
</script>
