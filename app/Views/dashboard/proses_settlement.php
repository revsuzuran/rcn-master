<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title . " " .  $nama_rekon; ?></h1>
    </div> 

    <div class="row mb-3">

        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                  <h6 class="m-0 font-weight-bold text-light">Detail Settlement</h6>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <?php
                        $feeCompany = (int) $data_rekon_satu->fee_detail->fee_admin->total;
                        $totalFee = (int) $data_rekon_satu->fee_detail->fee1->total + $data_rekon_satu->fee_detail->fee2->total + (int) $data_rekon_satu->fee_detail->fee3->total + (int) $data_rekon_satu->fee_detail->fee4->total  + (int) $data_rekon_satu->fee_detail->fee5->total + $feeCompany ; ?>

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
                                            <td><?= rupiah($data_rekon_satu->fee_detail->fee1->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 2 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_satu->fee_detail->fee2->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 3 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_satu->fee_detail->fee3->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 4 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_satu->fee_detail->fee4->total); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fee 5 </td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_satu->fee_detail->fee5->total); ?></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total Fee</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($totalFee) ?></td>
                                        </tr>
                                        <tr class="fs-6 fw-bolder text-primary">
                                            <td>Nett Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?=  rupiah((int)$data_rekon_satu->sum_result->total_sum_match - (int) $totalFee) ?></td>
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
                                            <td><?= rupiah((isset($data_rekon_satu->sum_result->total_sum_unmatch) ? $data_rekon_satu->sum_result->total_sum_unmatch : 0 )); ?></td>
                                        </tr>                                    
                                        <tr>
                                            <td>Match Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah((isset($data_rekon_satu->sum_result->total_sum_match) ? $data_rekon_satu->sum_result->total_sum_match : 0)); ?></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total Amount</td>
                                            <td>&nbsp;:&nbsp;</td>
                                            <td><?= rupiah($data_rekon_satu->sum_result->total_sum); ?></td>
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

                    <div class="mb-3">
                        <label class="form-label">Pilih Bank Pembayaran</label>
                        <select class="form-select mb-3" name="bank_opt" id="bank_opt">
                            <?php foreach ($data_bank as $row) { ?>
                                <option value="<?= $row['_id'] ?>"><?= $row['nama_bank'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary justify-content-md-end" id="prosesInqBtn">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="prosesInqSpinner" hidden></span>
                            Proses Inquiry
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
     
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

    /* Fungsi formatRupiah */
    function convertToRupiah(angka)
    {
        var rupiah = '';		
        var angkarev = angka.toString().split('').reverse().join('');
        for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
        return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
    }

});
</script>