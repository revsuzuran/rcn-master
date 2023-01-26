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
                        $feeCompany = 0;
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
                        <select class="form-select mb-3" name="bank_opt">
                            <?php foreach ($data_bank as $row) { ?>
                                <option value="<?= $row['_id'] ?>"><?= $row['nama_bank'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary justify-content-md-end" id="prosesPay">Proses Pembayaran</button>
                    </div>
                    
                </div>
            </div>
        </div>
     
    </div>
    <!--Row-->
</div>

<script>
$('#prosesPay').on('click', function(event) {

    var dataRekon = {
        'id_rekon_result' : "<?= $data_rekon[0]->id_rekon_result ?>",
        'id_mitra' : "<?= $data_rekon[0]->id_mitra ?>"
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

            if(hasil == "sukses") {
                Swal.fire('Success!', 'Cek status Disbursment di menu Monitoring Disburse', 'success').then((result) => {
                    window.location.replace("<?= base_url('settlement/monit_disbursment') ?>");
                });
            }
            
        }
    });

});
</script>