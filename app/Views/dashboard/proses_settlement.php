<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title . " " .  $nama_rekon; ?></h1>
    </div> 

    <div class="row mb-3">

        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Detail Settlement #<?= isset($_SESSION['data_settlement_choosen']) ? $_SESSION['data_settlement_choosen'] : "1" ?></h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <?php
                        $feeCompany = (int) $data_rekon_settlement->fee_detail->fee_admin->total;
                        $totalFee = (int) $data_rekon_settlement->fee_detail->fee1->total + $data_rekon_settlement->fee_detail->fee2->total + (int) $data_rekon_settlement->fee_detail->fee3->total + (int) $data_rekon_settlement->fee_detail->fee4->total  + (int) $data_rekon_settlement->fee_detail->fee5->total + $feeCompany ; ?>

                        <div class="row">
                            <div class="col-6">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Fee Company </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($feeCompany)?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 1 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->fee_detail->fee1->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 2 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->fee_detail->fee2->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 3 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->fee_detail->fee3->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 4 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->fee_detail->fee4->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 5 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->fee_detail->fee5->total); ?></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total Fee</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($totalFee) ?></td>
                                        </tr>
                                        <tr class="fs-6 fw-bolder text-primary">
                                            <td>Nett Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?=  rupiah((int)$data_rekon_settlement->sum_result->total_sum_match - (int) $totalFee) ?></td>
                                        </tr>
                                    </tbody>
                                </table>  
                            </div>
                            <div class="col-6" style="display: grid; align-content: baseline;">
                                <table style="text-align:right;">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" colspan="3">AMOUNT DATA</td>
                                        </tr>    
                                        <tr>
                                            <td>Unmatch Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah((isset($data_rekon_settlement->sum_result->total_sum_unmatch) ? $data_rekon_settlement->sum_result->total_sum_unmatch : 0 )); ?></td>
                                        </tr>                                    
                                        <tr>
                                            <td>Match Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah((isset($data_rekon_settlement->sum_result->total_sum_match) ? $data_rekon_settlement->sum_result->total_sum_match : 0)); ?></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_settlement->sum_result->total_sum); ?></td>
                                        </tr>
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-5 mb-4">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Data Pembayaran</h6>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="form-label">Pilih Data Rekon</label>
                        <select class="form-select mb-3" name="opt_settlement_data" id="opt_settlement_data">      
                          <option value="1">AMOUNT DATA REKON #1</option>    
                          <option value="2">AMOUNT DATA REKON #2</option>             
                        </select>                     
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Bank Pembayaran</label>
                        <select class="form-select mb-3" name="bank_opt" id="bank_opt">
                            <?php foreach ($data_bank as $row) { ?>
                                <option value="<?= $row['_id'] ?>"><?= $row['nama_bank'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <!-- <button class="btn btn-primary justify-content-md-end" id="prosesInqBtn">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="prosesInqSpinner" hidden></span>
                            Proses Inquiry
                        </button> -->
                        <button class="btn btn-primary justify-content-md-end" id="prosesCekSplit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="prosesCekSplitSpinner" hidden></span>
                            Proses Cek
                        </button>
                        <button class="btn btn-danger justify-content-md-end" type="button" id="prosesManualBtn">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="prosesManualSpinner" hidden></span>
                            Proses Manual
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>

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
                        <th>Ref Number</th>
                        <th>Nominal</th>
                        <th>Admin</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>No</th>
                        <th>Ref Number</th>
                        <th>Nominal</th>
                        <th>Admin</th>
                        <th>Total</th>
                        <th>Statu</th>
                        <th>Aksi</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php $no = 1;?> 
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>-</td>    
                            <td>-</td>    
                            <td>-</td>     
                            <td>-</td>  
                            <td>-</td>                      
                        </tr>
                    </tbody>
                  </table>
                </div>
              </div>
        </div>
        <!-- tables-->
     
    </div>
</div>


<!-- Modal Inquiry -->
<div class="modal fade" id="modalInq" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalInqLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        
      <div class="modal-header">
        <h5 class="modal-title" id="modalInqLabel">Result Inquiry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <div class="d-flex justify-content-between">
                <span class="fw-bolder">Inquiry REFF</span>
                <span id="inqref" class="fw-bolder fs-5"></span>
            </div><br>             
            <div class="d-flex justify-content-between">
                <span>Nama Bank</span>
                <span id="bankname"></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Nomor Rekening</span>
                <span id="norek"></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Nama Pemilik</span>
                <span id="namapemilik"></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Amount</span>
                <span id="amount"></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Additional Fee</span>
                <span id="fee"></span>
            </div>
            <br>
            <div class="form-group mt-2 d-flex align-items-end flex-column">
                <span class="fw-bolder text-primary">TOTAL AMOUNT</span>
                <span class="fw-bolder fs-5" id="total_amount"></span>
            </div>
        </div>
      </div>

      <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary justify-content-md-end" type="button" id="btnProsesPay">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="btnProsesPaySpinner" hidden></span>
                Proses Payment
            </button>
    </div>
    
    </div>
  </div>
</div>

<script>
const settingSatu = "<?= isset($_SESSION['data_settlement_choosen']) ? $_SESSION['data_settlement_choosen'] : "1" ?>";
$("#opt_settlement_data").val(settingSatu).change();

$(".table-detail-settlement").hide();

$("#opt_settlement_data").on('change', function() {
    var id_settlement = $(this).find(":selected").val();

    $.ajax({
        url : "<?= base_url('settlement/proses_settlement_choosen') ?>",
        method : "POST",
        data : {id: id_settlement},
        success: function(hasil){
            location.reload();
        }
    });    
});

$('#prosesManualBtn').on('click', function(event) {

    Swal.fire({
        title: 'Warning',
        text: "Anda yakin ingin memproses data manual?",
        showDenyButton: true,
        icon: 'warning',
        confirmButtonColor: '#0d6efd',
    }).then((result) => {
        if (result.isConfirmed) {
            prosesManual();
        }        
    });
    
});

function prosesManual() {
    $('#prosesManualBtn').attr("disabled", true);
    $('#prosesManualSpinner').removeAttr("hidden");
    var idBank = $('#bank_opt').find(":selected").val();
    // console.log(idBank)
    var dataRekon = {
        'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
        'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>",
        'id_bank' : idBank
    };

    var key = "<?= getenv("encryption_key") ?>";        
    var plaintext = JSON.stringify(dataRekon);
    let encryption = new Encryption();
    var encryptedData = encryption.encrypt(plaintext, key);

    $.ajax({
        url : "<?= base_url('settlement/manual_action') ?>",
        method : "POST",
        data : {'encryptedData': encryptedData},
        async : true,
        dataType : 'html',
        success: function(hasil){
            const objHasil = JSON.parse(hasil);
            $('#prosesManualSpinner').attr("hidden", true);
            $('#prosesManualBtn').removeAttr("disabled");

            if(objHasil.response_code == "00") {
                Swal.fire('Success!', 'Cek status Disbursment di menu Monitoring Disburse', 'success').then((result) => {
                    window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                });
            } else {
                Swal.fire('Failed!', objHasil.response_desc, 'error');
            }
        }
    });
}

$('#prosesCekSplit').on('click', function(event) {
    $('#prosesCekSplit').attr("disabled", true);
    $('#prosesCekSplitSpinner').removeAttr("hidden");
    var idBank = $('#bank_opt').find(":selected").val();
    var idAmount = $('#opt_settlement_data').find(":selected").val();
    
    var dataRekon = {
        'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
        'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>",
        'id_bank' : idBank,
        'id_amount' : idAmount
    };

    var key = "<?= getenv("encryption_key") ?>";        
    var plaintext = JSON.stringify(dataRekon);
    let encryption = new Encryption();
    var encryptedData = encryption.encrypt(plaintext, key);

    $.ajax({
        url : "<?= base_url('settlement/proses_cek_split') ?>",
        method : "POST",
        data : {'encryptedData': encryptedData},
        async : true,
        dataType : 'html',
        success: function(hasil){

            const totalData = hasil;

            $('#prosesCekSplitSpinner').attr("hidden", true);
            $('#prosesCekSplit').removeAttr("disabled");

            Swal.fire({
                title: 'Peringatan',
                text: "Data Settlement akan di split menjadi " + totalData + " data disbursment, Anda yakin ingin melanjutkan (Proses Inquiry) ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes,'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.loading').show();
                        $.ajax({
                            url : "<?= base_url('settlement/proses_split') ?>",
                            method : "POST",
                            data : {'encryptedData': encryptedData},
                            async : true,
                            dataType : 'html',
                            success: function(hasil){
                                $('.loading').hide();
                                const objHasilPay = JSON.parse(hasil);            
                                if(objHasilPay.response_code == "00") {
                                    Swal.fire('Success!', 'Data berhasil di proses', 'success').then((result) => {
                                        window.location.replace("<?= base_url('settlement/detail_disbursment') ?>");
                                    });
                                } else if(objHasilPay.response_code == "01") {
                                    Swal.fire('Failed!', objHasilPay.response_desc, 'warning').then((result) => {
                                        window.location.replace("<?= base_url('settlement/detail_disbursment') ?>");
                                    });
                                } else {
                                    Swal.fire('Failed!', objHasilPay.response_desc, 'warning').then((result) => {
                                        
                                    });
                                }

                                
                            }
                        });

                        
                    }
            })

            // const objHasil = JSON.parse(hasil);
            
            // $('#prosesCekSplitSpinner').attr("hidden", true);
            // $('#prosesCekSplit').removeAttr("disabled");

            // $(".table-detail-settlement").show();

            // // Clear the existing table rows
            // $('.table-detail-settlement tbody').empty();
            // // Loop through the data and append new table rows

            // Swal.fire('Success!', 'Nominal Pembayaran berhasil di Split menjadi ' + objHasil.length + ' buah', 'success');

            // var no = 1;
            // $.each(objHasil, function(index, item) {
            //     console.log(item);
            //     const totalNominal = parseInt(item.data.amount) + parseInt(item.data.additionalfee);

            //     var key = "<?= getenv("encryption_key") ?>";        
            //     var plaintext = JSON.stringify(item);
            //     let encryption = new Encryption();
            //     var encryptedData = encryption.encrypt(plaintext, key);

            //     let dataBtn = "-";
                
            //     if(item.data.status == "SUCCESS") {
            //         dataBtn = '<button class="btn btn-primary justify-content-md-end btnProsesPayment" id="btnProsesPayment' + no +'" data-id="'+no+'" data-enc="' + encryptedData +'">' +
            //              '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="btnProsesPaymentSpinner' + no +'" hidden></span>' +
            //                 'Proses Payment' +
            //            '</button></td>';
            //     }

            //     $('.table-detail-settlement tbody').append(
            //         '<tr>' +
            //         '<td>' + no + '</td>' +
            //         '<td> #' + item.data.inquiry_reff +'</td>' +
            //         '<td>' + convertToRupiah(item.data.amount) + '</td>' +
            //         '<td>' + convertToRupiah(item.data.additionalfee)+'</td>' +
            //         '<td>' + convertToRupiah(totalNominal) + '</td>' +
            //         '<td>' + item.data.response_desc +'</td>' +
            //         '<td>' + dataBtn +'</td>' +
            //         '</tr>'
            //     );
            //     no++;
            // });
        }
    });
});


$('#prosesInqBtn').on('click', function(event) {

    $('#prosesInqBtn').attr("disabled", true);
    $('#prosesInqSpinner').removeAttr("hidden");
    var idBank = $('#bank_opt').find(":selected").val();
    // console.log(idBank)
    var dataRekon = {
        'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
        'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>",
        'id_bank' : idBank
    };

    var key = "<?= getenv("encryption_key") ?>";        
    var plaintext = JSON.stringify(dataRekon);
    let encryption = new Encryption();
    var encryptedData = encryption.encrypt(plaintext, key);

    $.ajax({
        url : "<?= base_url('settlement/proses_inq') ?>",
        method : "POST",
        data : {'encryptedData': encryptedData},
        async : true,
        dataType : 'html',
        success: function(hasil){
            const objHasil = JSON.parse(hasil);
            // console.log(objHasil);

            $('#prosesInqSpinner').attr("hidden", true);
            $('#prosesInqBtn').removeAttr("disabled");
            
            if(objHasil.response_code == "00") {
                
                const totalAmount = parseInt(objHasil.amount) + parseInt(objHasil.additionalfee);
                
                $('#modalInq').modal('show');
                $('#total_amount').text(convertToRupiah(totalAmount));
                $('#bankname').text(objHasil.bankname)
                $('#norek').text(objHasil.accountnumber)
                $('#namapemilik').text(objHasil.accountname)
                $('#dn').text(objHasil.serialnumber)
                $('#amount').text(convertToRupiah(objHasil.amount))
                $('#fee').text(convertToRupiah(objHasil.additionalfee))
                $('#inqref').text("#" + objHasil.inquiry_reff)

                /* PROSES PAYMENT */
                $('#btnProsesPay').on('click', function(event) {

                    $('#btnProsesPay').attr("disabled", true);
                    $('#btnProsesPaySpinner').removeAttr("hidden");

                    var dataInq = {
                        'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
                        'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>",
                        'id_inq_reff' : objHasil.inquiry_reff,
                        'id_bank' : idBank
                    };
                    
                    var key = "<?= getenv("encryption_key") ?>";        
                    var dataPay = JSON.stringify(dataInq);
                    let encryptionPay = new Encryption();
                    var encryptedDataPay = encryptionPay.encrypt(dataPay, key);

                    $.ajax({
                        url : "<?= base_url('settlement/proses_pay') ?>",
                        method : "POST",
                        data : {'encryptedData': encryptedDataPay},
                        async : true,
                        dataType : 'html',
                        success: function(hasilPay){
                            
                            const objHasilPay = JSON.parse(hasilPay);
                            $('#btnProsesPaySpinner').attr("hidden", true);
                            $('#btnProsesPay').removeAttr("disabled");
                            
                            if(objHasilPay.response_code == "00") {
                                Swal.fire('Success!', 'Cek status Disbursment di menu Monitoring Disburse', 'success').then((result) => {
                                    window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                                });
                            }else {
                                Swal.fire('Pending', 'Cek status Disbursment di menu Monitoring Disburse', 'warning').then((result) => {
                                    window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                                });
                            }
                        }
                    });

                });
                

            } else if(objHasil.response_code == "XX") {
                Swal.fire('Inquiry Gagal', objHasil.response_desc, 'warning').then((result) => {
                    // pending
                });
            }
            
        }
    });

    

});

/* PROSES PAYMENT */
$(document).on('click', '.btnProsesPayment', function() {
    console.log('ok');
    var idButton = $(this).attr("data-id");

    $('#btnProsesPayment' + idButton).attr("disabled", true);
    $('#btnProsesPaymentSpinner'+ idButton).removeAttr("hidden");

    var encryptedDataPay = $(this).attr("data-enc");

    // var dataInq = {
    //     'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
    //     'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>",
    //     'id_inq_reff' : objHasil.inquiry_reff,
    //     'id_bank' : idBank
    // };

    // var key = "<?= getenv("encryption_key") ?>";        
    // var dataPay = JSON.stringify(dataInq);
    // let encryptionPay = new Encryption();
    // var encryptedDataPay = encryptionPay.encrypt(dataPay, key);

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
                Swal.fire('Success!', 'Cek status Disbursment di menu Monitoring Disburse', 'success').then((result) => {
                    // window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                });
            }else {
                Swal.fire('Pending', 'Cek status Disbursment di menu Monitoring Disburse', 'warning').then((result) => {
                    // window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                });
            }
        }
    });

});

/* Fungsi formatRupiah */
function convertToRupiah(angka)
    {
        if(angka) {
            var rupiah = '';		
            var angkarev = angka.toString().split('').reverse().join('');
            for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
            return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
        } else {
            return 'Rp. 0';
        }
        
    }
</script>