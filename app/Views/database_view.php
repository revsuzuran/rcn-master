<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <a type="button" class="btn btn-primary" href="<?= base_url('add_database') ?>">Add New</a>
    </div> 

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data FTP</h6>
                   
                </div>
                <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Config</th>
                        <th>Hostname</th>
                        <th>Database</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama Config</th>
                        <th>Hostname</th>
                        <th>Database</th>
                        <th>Aksi</th>
                    </tr>
                    </tfoot>
                    <tbody>
                        
                            <?php foreach($dataDb as $index => $row) {?> 
                                <tr>
                                <td><?= ($index+1) ?></td>
                                <td><?= $row['db_name'] ?></td>     
                                <td><?= $row['hostname'] ?></td>     
                                <td><?= $row['database'] ?></td>   
                                <td>
                                    <a type="button" class="btn btn-primary btn-sm editBtn" href="<?= base_url('edit_database/' . $row->_id->__toString()) ?>">Edit</a>
                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row->_id->__toString() ?>" >Delete</button>
                                </td>  
                                </tr>
                            <?php } ?>                    
                        
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <!-- tables-->



     
    </div>
    <!--Row-->
</div>

<script>
$('.deleteBtn').on('click', function(event) {

    const id = $(this).data("id");

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url : "<?= base_url('rm_database') ?>",
                    method : "POST",
                    data : {id:id},
                    async : true,
                    dataType : 'html',
                    success: function($hasil){
                        if($hasil == 'sukses'){

                            Swal.fire('Deleted!', 'Successfully deleted Database', 'success' )

                            location.reload();
                        }
                    }
                });

                
            }
    })
   
    

});
</script>