

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
    <!-- <form method="post" enctype="multipart/form-data" action="<?php echo base_url('rekon_unmatch_bulanan/proses'); ?>" id="uploadForm"> -->
    <div class="row mb-3">
      
          <div class="col-6">
            <div class="card mb-4 pb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Input Detail Rekon</h6>
                </div>
                <div class="card-body">
                    <div class="col-lg-6">
                      <div class="form-group">
                          <label class="form-label">Nama Rekon</label>
                          <input name="namaRekon" type="text" class="form-control" placeholder="ex : Rekon Bank Indonesia" value="" id="nama_rekon" required>
                      </div>
                    </div>

                    <div class="form-group col-6 mt-2">
                        <label for="simpleDataInput" class="form-label">Tanggal Rekon</label>
                        <div class="input-group date">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                          <input type="date" class="form-control" value="01/06/2020" id="tanggal_rekon" name="tanggal_rekon" id="tanggal_rekon">
                        </div>
                    </div>

                    <hr>               
                    <div class="form-group col-6 mt-2">
                        <label class="form-label">Pilih Channel</label>
                        <select class="form-select mb-3" name="opt_channel" id="opt_channel">
                          <?php foreach ($data_channel as $rowData) { ?>
                            <option value="<?= $rowData['_id'] ?>"><?= "[".$rowData['nama_mitra']. "] " . $rowData['nama_channel'] ?></option>
                          <?php } ?>
                        </select>
                    </div>
                    
                </div>

            </div>
          </div>

          <div class="col-6">
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Data Rekon</h6>
                </div>
                <div class="card-body">
                    <div class="form-group col-8" id="daterange1">
                        <label class="form-label" for="dateRangePicker">Range Select Data Unmatch #1</label>
                        <div class="input-daterange input-group">
                            <input type="text" class="input-sm form-control" id="tanggal_rekon_awal_satu">
                            <div class="input-group-prepend">
                                <span class="input-group-text">to</span>
                            </div>
                            <input type="text" class="input-sm form-control" id="tanggal_rekon_akhir_satu">
                        </div>
                    </div>
                    <br>
                    <div class="form-group col-8" id="daterange2">
                        <label class="form-label" for="dateRangePicker">Range Select Data Unmatch #2</label>
                        <div class="input-daterange input-group">
                            <input type="text" class="input-sm form-control" id="tanggal_rekon_awal_dua">
                            <div class="input-group-prepend">
                                <span class="input-group-text">to</span>
                            </div>
                            <input type="text" class="input-sm form-control" id="tanggal_rekon_akhir_dua">
                        </div>
                    </div>
                    <!-- <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary me-md-2" type="submit">Next</button>
                    </div> -->
                    <br>
                    <button class="btn btn-primary justify-content-md-end" type="button" id="btnProses">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="btnProsesSpinner" hidden></span>
                        Proses Data
                    </button>
                </div>
            </div>
          </div>

        <input name="tipe" type="text" class="form-control" value="1" hidden>
      
    </div>
    <!-- </form> -->
    <!--Row-->
</div>
<!---Container Fluid--> 

<script>

  $('#btnProses').on('click', function(event) {
      $('#btnProses').attr("disabled", true);
      $('#btnProsesSpinner').removeAttr("hidden");

      var idChannel = $('#opt_channel').find(":selected").val();
      var namaRekon = $('#nama_rekon').val();
      var tanggalRekon = $('#tanggal_rekon').val();
      var tanggal_rekon_awal_satu = $('#tanggal_rekon_awal_satu').val();
      var tanggal_rekon_akhir_satu = $('#tanggal_rekon_akhir_satu').val();
      var tanggal_rekon_awal_dua = $('#tanggal_rekon_awal_dua').val();
      var tanggal_rekon_akhir_dua = $('#tanggal_rekon_akhir_dua').val();
      var dataRekon = {
          'nama_rekon' : namaRekon,
          'opt_channel' : idChannel,
          'tanggal_rekon' : tanggalRekon,
          'tanggal_rekon_awal_satu' : tanggal_rekon_awal_satu,
          'tanggal_rekon_akhir_satu' : tanggal_rekon_akhir_satu,
          'tanggal_rekon_awal_dua' : tanggal_rekon_awal_dua,
          'tanggal_rekon_akhir_dua' : tanggal_rekon_akhir_dua,
      };

      var key = "<?= getenv("encryption_key") ?>";        
      var plaintext = JSON.stringify(dataRekon);
      let encryption = new Encryption();
      var encryptedData = encryption.encrypt(plaintext, key);

      $.ajax({
          url : "<?= base_url('rekon_unmatch_bulanan/proses') ?>",
          method : "POST",
          data : {'encryptedData': encryptedData},
          async : true,
          dataType : 'html',
          success: function(hasil){

            const objHasil = JSON.parse(hasil);
            $('#btnProsesSpinner').attr("hidden", true);
            $('#btnProses').removeAttr("disabled");
            
            if(objHasil.response_code == "00") {

                Swal.fire({
                    title: 'Success!',
                    icon: 'success',
                    html:
                    'Data sukses di proses!' +
                    '<br> Total Data Unmatch #1 = ' + objHasil.data_unmatch_satu +
                    '<br> Total Data Unmatch #2 = ' + objHasil.data_unmatch_dua
                }).then((result) => {
                    window.location.replace("<?= base_url('rekon_unmatch_bulanan/cleansing_data') ?>");
                });

                
                              
            } else {
                Swal.fire('Gagal', 'Data Gagal Di Proses', 'warning').then((result) => {
                    // pending
                });
            }
          }
      });

  });


  $("#tanggal_rekon").on("change", function() {
    console.log($(this).val());
  });

  $('#clockPicker2').clockpicker({
      autoclose: true
  });

  $('#daterange1 .input-daterange').datepicker({        
        format: 'yyyy-mm-dd',        
        autoclose: true,     
        todayHighlight: true,   
        todayBtn: 'linked',
  });  

  $('#daterange2 .input-daterange').datepicker({        
        format: 'yyyy-mm-dd',        
        autoclose: true,     
        todayHighlight: true,   
        todayBtn: 'linked',
  });  

</script>
