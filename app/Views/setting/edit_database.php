<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">


    <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Setting Database</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Database Config Name</label>
                        <input id="db_name" type="text" class="form-control" name="db_name" placeholder="Database LINKQU" value="<?= $data_db->db_name ?>" required>
                    </div>
                    <?php $dbDriver = $data_db->driver; ?>
                    <div class="form-group mt-2 col-6">
                        <label class="form-label">Database Driver</label>
                        <select class="form-select mb-3" id="driver">
                            <option value="Postgre">Postgre</option>
                            <option value="MySQLi">MySQLi</option>
                        </select>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Hostname DB</label>
                        <input id="hostname" type="text" class="form-control" name="hostname" placeholder="ex : db.linkqu.com" value="<?= $data_db->hostname ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Database</label>
                        <input id="database" type="text" class="form-control" value="<?= $data_db->database ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" value="<?= $data_db->username ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="text" class="form-control" value="<?= $data_db->password ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Port</label>
                        <input id="port" type="text" class="form-control" value="<?= $data_db->port ?>" required>
                    </div>

                    <input id="id" type="text" class="form-control" value="<?= $data_db->_id->__toString() ?>" hidden>
                    <button class="btn btn-primary" id="simpanDB">Update</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanDB').on('click', function(event) {

    var dbName = $('#db_name').val();
    var driver = $('#driver').val();
    var database = $('#database').val();
    var hostname = $('#hostname').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var port = $('#port').val();
    var id = $('#id').val();

    $.ajax({
        url : "<?= base_url('mitra/update_database') ?>",
        method : "POST",
        data : {dbName: db_name, driver:driver, username:username, password: password, hostname: hostname, port:port, id:id},
        async : true,
        dataType : 'html',
        success: function($hasil){
            if($hasil == 'sukses'){
                Swal.fire('Updated!', 'Successfully Updated Database Data', 'success' )
                location.reload();
            }
        }
    });

});

const dbDriver = "<?= $dbDriver ?>";
$("#driver").val(dbDriver).change();
</script>