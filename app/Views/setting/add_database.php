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
                        <input id="db_name" type="text" class="form-control" name="db_name" placeholder="Database LINKQU" value="" required>
                    </div>
                    
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
                        <input id="hostname" type="text" class="form-control" name="hostname" placeholder="ex : db.linkqu.com" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Database</label>
                        <input id="database" type="text" class="form-control" value="" required>
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
                        <label class="form-label">Port</label>
                        <input id="port" type="text" class="form-control" value="" required>
                    </div>

                    <button class="btn btn-primary" id="simpanDB">Simpan Data</button>
                </div>
            </div>
        </div>


     
    </div>
    <!--Row-->
</div>

<script>
$('#simpanDB').on('click', function(event) {

    var dbName = $('#db_name').val();
    var driver = $('#driver').find(":selected").val();
    var database = $('#database').val();
    var hostname = $('#hostname').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var port = $('#port').val();

    $.ajax({
        url : "<?= base_url('mitra/save_database') ?>",
        method : "POST",
        data : {dbName: dbName, driver:driver, username:username, password: password, hostname: hostname, port:port, database:database},
        success: function($hasil){
            if($hasil == 'sukses'){
                window.location.replace("<?= base_url('mitra/database') ?>")
            }
        }
    });

});

</script>