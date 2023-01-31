<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Setting SMTP</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">SMTP Host</label>
                        <input id="hostname" type="text" class="form-control" name="hostname" placeholder="SMTP Hostname" value="<?= $data_email[0]->host ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input id="port" type="text" class="form-control" name="port" placeholder="SMTP Port (465/25)" value="<?= $data_email[0]->port ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" value="<?= $data_email[0]->username ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="text" class="form-control" value="" placeholder="****************" required>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Mail Path</label>
                        <input id="path" type="text" class="form-control" value="<?= $data_email[0]->path ?>" placeholder="/usr/sbin/sendmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Encryption/Crypto</label>
                        <input id="crypto" type="text" class="form-control" value="<?= $data_email[0]->crypto ?>" placeholder="TLS/SSL">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Protocol</label>
                        <input id="protokol" type="text" class="form-control" value="<?= $data_email[0]->protokol ?>" placeholder="mail/sendmail/smtp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Agent</label>
                        <input id="useragent" type="text" class="form-control" value="<?= $data_email[0]->user_agent ?>" placeholder="User Agent">
                    </div>

                    <input id="id" type="text" class="form-control" value="<?= $data_email[0]->_id->__toString() ?>" hidden>
                    <button class="btn btn-primary" id="simpanSMTP">Update</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanSMTP').on('click', function(event) {

    var hostname = $('#hostname').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var port = $('#port').val();

    var path = $('#path').val();
    var crypto = $('#crypto').val();
    var protokol = $('#protokol').val();
    var useragent = $('#useragent').val();
    var id = $('#id').val();

    $.ajax({
        url : "<?= base_url('update_smtp') ?>",
        method : "POST",
        data : {hostname : hostname, username : username, password : password, port : port, path : path, crypto : crypto, protokol : protokol, useragent : useragent, id : id},
        async : true,
        dataType : 'html',
        success: function(hasil){
            if(hasil == 'sukses'){
                Swal.fire('Updated!', 'Successfully Updated SMTP Data', 'success' )
                location.reload();
            }
        }
    });

});
</script>