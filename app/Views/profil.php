<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div> 

    <div class="row mb-3">

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Profil User</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input id="username" type="text" class="form-control" placeholder="Contoh : reydinda" value="<?= $user['username'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input id="password" type="password" class="form-control" placeholder="********" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input id="nama" type="nama" class="form-control" placeholder="Contoh : reydinda" value="<?= $user['name'] ?>" required>
                    </div>

                    <button class="btn btn-primary" id="simpanUser">Update</button>
                </div>
            </div>
        </div>

     
    </div>
    <!--Row-->
</div>

<!-- Modal -->
<!-- <div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Peringatan</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah kamu yakin ingin menyimpan perubahan ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" id="simpanUser">Ya</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div> -->

<script>
$('#simpanUser').on('click', function(event) {

var username = $('#username').val();
var password = $('#password').val();
var nama = $('#nama').val();
var email = $('#email').val();

$.ajax({
    url : "<?= base_url('update_user') ?>",
    method : "POST",
    data : {username: username,password: password, name: nama},
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