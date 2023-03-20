<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>
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
                          <input name="namaRekon" type="text" class="form-control" placeholder="Rekon #MITRA#_#D#-#M#-#YYYY#" value="" id="nama_rekon" required>
                      </div>
                    </div>

                    <div class="form-group col-6 mt-2">
                      <label for="clockPicker2" class="form-label mt-2 clockPicker2">Jadwal Rekon</label>
                      <div class="input-group clockpicker clockPicker2" id="clockPicker2">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                        <input type="text" class="form-control" value="12:30" name="waktuRekon" id="waktu_rekon">                  
                      </div>
                    </div>  
                                   
                    <div class="form-group col-6 mt-2">
                        <label class="form-label">Pilih Channel</label>
                        <select class="form-select mb-3" name="opt_channel" id="opt_channel">
                          <option value="-">-</option>
                          <?php foreach ($data_channel as $rowData) { ?>
                            <option value="<?= $rowData['_id'] ?>"><?= "[".$rowData['nama_mitra']. "] " . $rowData['nama_channel'] ?></option>
                          <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-6 mt-2">
                        <label class="form-label">Pilih Collection</label>
                        <select class="form-select mb-3" name="opt_collection" id="opt_collection">      
                          <option value="-">-</option>             
                        </select>                     
                    </div>

                    <br>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                      <button class="btn btn-primary d-grid" type="button" id="btnProses">
                          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="btnProsesSpinner" hidden></span>
                          Next
                      </button>
                    </div>
                    
                    <hr>

                    <!-- INFORMATION HEHE -->
                    <p class="fst-bold"><code class="text-primary fw-bold">Information</code> :<br> Formater details </p> 
                    <p>Date Time :
                      <br><code class="highlighter-rouge">#D#</code> => Menampilkan tanggal dua digit (05)
                      <br><code class="highlighter-rouge">#DD#</code> => Menampilkan nama hari singkatan (Sun)
                      <br><code class="highlighter-rouge">#DDD#</code> => Menampilkan nama hari (Sunday)
                      <br><code class="highlighter-rouge">#M#</code> => Menampilkan bulan 2 digit (03)
                      <br><code class="highlighter-rouge">#MM#</code> => Menampilkan nama bulan singkatan (Jan)
                      <br><code class="highlighter-rouge">#MMM#</code> => Menampilkan nama bulan (January)
                      <br><code class="highlighter-rouge">#YYYY#</code> => Menampilkan tahun 4 digit (2023)
                      <br><code class="highlighter-rouge">#YY#</code> => Menampilkan tahun 2 digit (23)
                    </p> 
                    <p>Lain Lain : 
                      <br><code class="highlighter-rouge">#MITRA#</code> => Menampilkan nama mitra
                      <br><code class="highlighter-rouge">#CHANNEL#</code> => Menampilkan nama Channel
                    </p> 

                </div>
            </div>
          </div>

          <div class="col-6">
            <!-- FILE REKON 1 -->
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Upload File Rekon #1</h6>
                </div>
                <div class="card-body">
                  <div class="form-group mb-3">
                    <div class="row row-cols-auto">
                      <div class="custom-control custom-radio">
                        <input type="radio" id="radioFtp" name="radioUpload" class="form-check-input" value="ftp" checked="checked">
                        <label class="custom-control-label" for="radioFtp">FTP</label>
                      </div>
                      <div class="custom-control custom-radio">
                        <input type="radio" id="radioDb" name="radioUpload" class="form-check-input" value="db">
                        <label class="custom-control-label" for="radioDb">Database</label>
                      </div>
                    </div>
                  </div>                  

                  <div class="form-group form-ftp col-6">
                    <div class="custom-file">                      
                      <div class="mb-3 ">
                        <label class="form-label">FTP Connection</label>
                        <select class="form-select mb-3" name="ftp_option" id="ftp_option">
                          <?php foreach ($dataFtp as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['ftp_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Nama File</label>
                        <div class="custom-file">
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Data_Transaksi_#D#-#M#-#YYYY#.csv" aria-label="Recipient's username" aria-describedby="basic-addon2" name="nama_file" id="nama_file">
                            <div class="input-group-append">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group form-db">
                    <div class="custom-file">                      
                      <div class="mb-3 col-6">
                        <label class="form-label">Database Connection</label>
                        <select class="form-select mb-3" name="db_option" id="db_option">
                          <?php foreach ($dataDb as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['db_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Input Query</label>
                        <textarea class="form-control" name="query" id="query" rows="3"></textarea><br>
                        <p class="fst-italic"><code class="text-primary fw-bold">Information</code> :<br> Use single quotation marks</code></p> 
                        <p>Example : <br> SELECT * FROM data_transaksi WHERE date_insert = <code class="highlighter-rouge">'2022-12-30'</code></p> 
                      </div>
                    </div>
                  </div>


                  <div class="form-group col-6 mt-2">
                      <label class="form-label">Pilih Setting</label>
                      <select class="form-select mb-3" name="opt_setting" id="opt_setting">
                        <option value="0">-</option>
                        <?php foreach ($data_setting as $rowData) { ?>
                          <option value="<?= $rowData['_id'] ?>"><?= $rowData['nama_setting'] ?></option>
                        <?php } ?>
                      </select>
                  </div>
                  <hr>
                  <label class="form-label fw-bold" style="color:#0d6efd;">Setting Details</label>
                  <div class="form-group mt-2" id="datasetting"></div>
                  <input name="id_setting" type="text" class="form-control" id="id_setting" hidden>

              </div>
            </div>


            <!-- FILE REKON 2 -->
            <div class="card mb-4">
                <div class="card-header d-flex flex-row align-items-center justify-content-between bg-danger">
                      <h6 class="m-0 font-weight-bold text-light">Upload File Rekon #2</h6>
                </div>
                <div class="card-body">
                  <div class="form-group mb-3">
                    <div class="row row-cols-auto">
                      <div class="custom-control custom-radio2">
                        <input type="radio" id="radioFtp2" name="radioUpload2" class="form-check-input" value="ftp" checked="checked">
                        <label class="custom-control-label" for="radioFtp2">FTP</label>
                      </div>
                      <div class="custom-control custom-radio2">
                        <input type="radio" id="radioDb2" name="radioUpload2" class="form-check-input" value="db">
                        <label class="custom-control-label" for="radioDb2">Database</label>
                      </div>
                    </div>
                  </div>                  

                  <div class="form-group form-ftp2 col-6">
                    <div class="custom-file">                      
                      <div class="mb-3 ">
                        <label class="form-label">FTP Connection</label>
                        <select class="form-select mb-3" name="ftp_option2" id="ftp_option2">
                          <?php foreach ($dataFtp as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['ftp_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Nama File</label>
                        <div class="custom-file">
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Data_Transaksi_#D#-#M#-#YYYY#.csv" aria-label="Recipient's username" aria-describedby="basic-addon2" name="nama_file" id="nama_file2">
                            <div class="input-group-append">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group form-db2">
                    <div class="custom-file">                      
                      <div class="mb-3 col-6">
                        <label class="form-label">Database Connection</label>
                        <select class="form-select mb-3" name="db_option2" id="db_option2">>
                          <?php foreach ($dataDb as $row) { ?>
                            <option value="<?= $row['_id'] ?>"><?= $row['db_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Input Query</label>
                        <textarea class="form-control" name="query" id="query2" rows="3"></textarea><br>
                        <p class="fst-italic"><code class="text-primary fw-bold">Information</code> :<br> Use single quotation marks</code></p> 
                        <p>Example : <br> SELECT * FROM data_transaksi WHERE date_insert = <code class="highlighter-rouge">'2022-12-30'</code></p> 
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-6 mt-2">
                      <label class="form-label">Pilih Setting</label>
                      <select class="form-select mb-3" name="opt_setting2" id="opt_setting2">
                        <option value="0">-</option>
                        <?php foreach ($data_setting as $rowData) { ?>
                          <option value="<?= $rowData['_id'] ?>"><?= $rowData['nama_setting'] ?></option>
                        <?php } ?>
                      </select>
                  </div>
                  <hr>
                  <label class="form-label fw-bold" style="color:#0d6efd;">Setting Details</label>
                  <div class="form-group mt-2" id="datasetting2"></div>
                  <input name="id_setting2" type="text" class="form-control" id="id_setting2" hidden>

              </div>
            </div>
          </div>      
      </div>
    <!--Row-->
</div>
<!---Container Fluid--> 

<script>
  
  $("#opt_setting").on('change', function() {
    var id = $(this).find(":selected").val();
    $.ajax({
        url : "<?= base_url('get_setting') ?>",
        method : "POST",
        data : {id: id},
        success: function(hasil){
          
          if(hasil == "" || hasil == null) {
              $("#datasetting").html("-");
          } else {
              $("#id_setting").val(id);
          
              let contentHehe = "<table><tbody>";
              const anu = JSON.parse(hasil);
              contentHehe += `<tr>
                    <td class="fw-lighter"># Delimiter</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td><code class="highlighter-rouge"> "${anu.delimiter}" </code></td>
                  </tr>`;
              for (const row of anu.clean_rule) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Clean Rule</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>KOLOM ${parseInt(row.index_kolom)+1} <code class="highlighter-rouge"> [${row.rule}] </code> ${row.rule_value} </td>
                  </tr>`;
                  
              }

              for (const row of anu.kolom_compare) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Kolom to Compare</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>${row.kolom_name} </td>
                  </tr>`;
                  
              }

              for (const row of anu.kolom_sum) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Kolom to SUM</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>${row.kolom_name} </td>
                  </tr>`;
                  
              }
              
              contentHehe += "</tbody></table>";
              $("#datasetting").html(contentHehe);
          }

          
          
        }
    });
  });


  $("#opt_setting2").on('change', function() {
    var id = $(this).find(":selected").val();
    $.ajax({
        url : "<?= base_url('get_setting') ?>",
        method : "POST",
        data : {id: id},
        success: function(hasil){
          
          if(hasil == "" || hasil == null) {
              $("#datasetting2").html("-");
          } else {
              $("#id_setting2").val(id);
          
              let contentHehe = "<table><tbody>";
              const anu = JSON.parse(hasil);
              contentHehe += `<tr>
                    <td class="fw-lighter"># Delimiter</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td><code class="highlighter-rouge"> "${anu.delimiter}" </code></td>
                  </tr>`;
              for (const row of anu.clean_rule) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Clean Rule</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>KOLOM ${parseInt(row.index_kolom)+1} <code class="highlighter-rouge"> [${row.rule}] </code> ${row.rule_value} </td>
                  </tr>`;
                  
              }

              for (const row of anu.kolom_compare) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Kolom to Compare</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>${row.kolom_name} </td>
                  </tr>`;
                  
              }

              for (const row of anu.kolom_sum) {
                  contentHehe += `<tr>
                    <td class="fw-lighter"># Kolom to SUM</td>
                    <td class="fw-lighter">&nbsp;:&nbsp;</td>
                    <td>${row.kolom_name} </td>
                  </tr>`;
                  
              }
              
              contentHehe += "</tbody></table>";
              $("#datasetting2").html(contentHehe);
          }

          
          
        }
    });
  });

  $(".form-ftp").show();
  $(".form-db").hide();   
  $(".form-ftp2").show();
  $(".form-db2").hide();  

  $("#radioFtp").click(function() {
    $(".form-ftp").show();   
    $(".form-db").hide();    
  });

  $("#radioDb").click(function() {
    $(".form-ftp").hide(); 
    $(".form-db").show();    
  });

  $("#radioFtp2").click(function() {
    $(".form-ftp2").show();
    $(".form-db2").hide();    
  });

  $("#radioDb2").click(function() {
    $(".form-ftp2").hide();
    $(".form-db2").show();    
  });

  $('#clockPicker2').clockpicker({
      autoclose: true
  });


  $('#btnProses').on('click', function(event) {
      $('#btnProses').attr("disabled", true);
      $('#btnProsesSpinner').removeAttr("hidden");

      var idChannel = $('#opt_channel').find(":selected").val();
      var idCollection = $('#opt_collection').find(":selected").val();
      var namaRekon = $('#nama_rekon').val();
      var waktuRekon = $('#waktu_rekon').val();

      /* Data Rekon Satu */
      var radioUploadSatu = $('input[name="radioUpload"]:checked');
      var koneksiSatu = "";
      var inputSatu = "";
      var settingSatu =  $('#opt_setting').find(":selected").val();
      if(radioUploadSatu.val() == "ftp") {
          koneksiSatu = $('#ftp_option').find(":selected").val();
          inputSatu = $('#nama_file').val();
          console.log(inputSatu);
      } else {
          koneksiSatu = $('#db_option').find(":selected").val();
          inputSatu = $('#query').val();
      }

      var radioUploadDua = $('input[name="radioUpload2"]:checked');
      var koneksiDua = "";
      var inputDua = "";
      var settingDua =  $('#opt_setting2').find(":selected").val();
      if(radioUploadDua.val() == "ftp") {
          koneksiDua = $('#ftp_option2').find(":selected").val();
          inputDua = $('#nama_file2').val();
      } else {
        koneksiDua = $('#db_option2').find(":selected").val();
          inputDua = $('#query2').val();
      }

      /* Params Send */      
      var dataRekon = {
          'nama_rekon' : namaRekon,
          'opt_channel' : idChannel,
          'opt_collection' : idCollection,
          'waktu_rekon' : waktuRekon,
          'data_satu' : {
            'tipe' : radioUploadSatu.val(),
            'koneksi' : koneksiSatu,
            'input' : inputSatu,
            'setting' : settingSatu
          },
          'data_dua' : {
            'tipe' : radioUploadDua.val(),
            'koneksi' : koneksiDua,
            'input' : inputDua,
            'setting' : settingDua
          },
      };

      var key = "<?= getenv("encryption_key") ?>";        
      var plaintext = JSON.stringify(dataRekon);
      let encryption = new Encryption();
      var encryptedData = encryption.encrypt(plaintext, key);

      $.ajax({
          url : "<?= base_url('rekon_transaksi_sch/submit') ?>",
          method : "POST",
          data : {'encryptedData': encryptedData},
          async : true,
          dataType : 'html',
          success: function(hasil){
            
            $('#btnProses').removeAttr("disabled");
            $('#btnProsesSpinner').attr("hidden", true);

            if(hasil == 'sukses'){
                window.location.replace("<?= base_url('rekon_transaksi_sch/process_data_sch_cek') ?>");
            }
            
          }
      });

    });

    $("#opt_channel").on('change', function() {
      var id_channel = $(this).find(":selected").val();
      
      $.ajax({
          url : "<?= base_url('rekon_transaksi/get_collection_view') ?>",
          method : "POST",
          data : {id_channel: id_channel},
          success: function(hasil){
            $("#opt_collection").html(hasil)
          }
        });
    });

</script>
