<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title . " " . $data_mitra->nama_mitra; ?> </h1>
        <a type="button" class="btn btn-primary" href="<?= base_url('channel/add') ?>">Add New</a>
    </div> 

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Channel</h6>
                   
                </div>
                <div class="table-responsive p-3">
                <table class="table align-items-center table-flush" id="dataTable">
                    <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Channel</th>
                        <th>Fee</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama Channel</th>
                        <th>Fee</th>
                        <th>Aksi</th>
                    </tr>
                    </tfoot>
                    <tbody>
                        
                            <?php

                            foreach($data_channel as $index => $row) {

                                $dataFee = "";
                                $dataFee .= "(";
                                $dataFee .= convert($row['fee1']);
                                $dataFee .= "/";
                                $dataFee .= convert($row['fee2']);
                                $dataFee .= "/";
                                $dataFee .= convert($row['fee3']);
                                $dataFee .= "/";
                                $dataFee .= convert($row['fee4']);
                                $dataFee .= "/";
                                $dataFee .= convert($row['fee5']);
                                $dataFee .= ")";

                                ?> 
                                <tr>
                                <td><?= ($index+1) ?></td>
                                <td><?= $row['nama_channel'] ?></td>    
                                <td><?= $dataFee ?></td>   
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm editBtn" data-id="<?= $row->_id->__toString() ?>" >Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row->_id->__toString() ?>" >Delete</button>
                                </td>  
                                </tr>
                            <?php } 
                            
                            function convert($data) {
                                if ($data['is_prosentase']) {
                                   return  $data['nilai'] . '%';
                                } else {
                                    return $data['nilai'];
                                }
                            }
                            
                            ?>                    
                        
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
$('.editBtn').on('click', function(e) {
    const idChannel = $(this).data("id");

    $.ajax({
        url : "<?= base_url('channel/temp') ?>",
        method : "POST",
        data : {id:idChannel},
        async : true,
        dataType : 'html',
        success: function($hasil){
            window.location.replace("<?= base_url('channel/edit') ?>")
        }
    });

});

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
                    url : "<?= base_url('channel/rm') ?>",
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