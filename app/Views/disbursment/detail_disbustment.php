<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div> 

    <div class="row mb-3">

         <!-- tables -->
         <div class="col-lg-12 table-detail-settlement">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Settelement</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush" >
                    <thead class="thead-light">
                      <tr>
                        <th>No</th>
                        <th>Reff Number</th>
                        <th>Nominal</th>
                        <th>Admin</th>
                        <th>Total</th>
                        <th>Status Inquiry</th>
                        <th>Status Payment</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>No</th>
                        <th>Reff Number</th>
                        <th>Nominal</th>
                        <th>Admin</th>
                        <th>Total</th>
                        <th>Status Inquiry</th>
                        <th>Status Payment</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php $no = 1;
                        foreach($data_detail as $row) {

                          if($row->is_payment == 0) {
                            $noReff = $row->data_inquiry->partner_reff;
                            $nominal = $row->data_inquiry->amount;
                            $nominalAdmin = $row->data_inquiry->additionalfee;
                            $totalNominal = (int) $nominalAdmin + (int) $nominal;
                            $responseDescInq =  $row->data_inquiry->response_desc;
                            $responseDescPay =  "-";
                            $dataBtn = '<button class="btn btn-primary justify-content-md-end btnProsesPayment" id="btnProsesPayment' . $no .'" data-id="'.$no.'" data-disburst="'. $row->_id->__toString() .'">' .
                         '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="btnProsesPaymentSpinner' . $no .'" hidden></span>Proses Payment</button></td>';
                          } else {
                            $noReff = $row->data_payment->partner_reff;
                            $nominal = $row->data_payment->amount;
                            $nominalAdmin = $row->data_payment->additionalfee;
                            $totalNominal = (int) $nominalAdmin + (int) $nominal;
                            $responseDescInq =  $row->data_inquiry->response_desc;
                            $responseDescPay =  $row->data_payment->response_desc;
                            $dataBtn = "-";
                          }                          
                      ?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>#<?= $noReff ?> </td>    
                            <td><?= rupiah($nominal) ?></td>    
                            <td><?= rupiah($nominalAdmin) ?></td>    
                            <td><?= rupiah($totalNominal) ?></td>    
                            <td><?= $responseDescInq ?></td>        
                            <td><?= $responseDescPay ?></td>                           
                            <td><?= $dataBtn ?></td>                      
                        </tr>
                        <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
        </div>
        <!-- tables-->
     
    </div>
</div>

<script>


/* PROSES PAYMENT */
$(document).on('click', '.btnProsesPayment', function() {
    // console.log('ok');
    var idButton = $(this).attr("data-id");

    $('#btnProsesPayment' + idButton).attr("disabled", true);
    $('#btnProsesPaymentSpinner'+ idButton).removeAttr("hidden");

    var idDisburst = $(this).attr("data-disburst");

    var dataSend = {
        'id_disbursment_detail' : idDisburst
    };

    var key = "<?= getenv("encryption_key") ?>";        
    var dataPay = JSON.stringify(dataSend);
    let encryptionPay = new Encryption();
    var encryptedDataPay = encryptionPay.encrypt(dataPay, key);

    $.ajax({
        url : "<?= base_url('settlement/proses_payment') ?>",
        method : "POST",
        data : {'encryptedData': encryptedDataPay},
        async : true,
        dataType : 'html',
        success: function(hasilPay){
            
            const objHasilPay = JSON.parse(hasilPay);
            $('#btnProsesPaymentSpinner').attr("hidden", true);
            $('#btnProsesPayment').removeAttr("disabled");
            
            if(objHasilPay.response_code == "00") {
                Swal.fire('Success!', 'Transaksi Sukses', 'success').then((result) => {
                    window.location.replace("<?= base_url('settlement/detail_disbursment') ?>");
                });
            }else {
                Swal.fire('Pending', 'Transaksi Pending!', 'warning').then((result) => {
                    window.location.replace("<?= base_url('settlement/detail_disbursment') ?>");
                });
            }
        }
    });

});


</script>