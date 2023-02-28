<style>
#loading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
}

.spinner {
    position: absolute;
    top: 42%;
    left: 48.5%;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border-top: 3px solid #ffffff;
    border-right: 3px solid transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.textloading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #ffffff;
    font-size: 24px;
}
</style>
<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
    </div>

    <div class="row mb-3">


        <!-- tables -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Data Mutasi</h6>
                </div>
                <div class="row p-3">
                    <div class="col-md-2">
                        <select class="form-control" id="selectFilter">
                            <option value="0">--Filter--</option>
                            <option value="1">Date</option>
                            <option value="2">ID Transaksi</option>
                            <option value="3">Partner Reff</option>
                        </select>
                    </div>
                    <div class="col-md-6" style="display:none" id="dateFilter">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group date">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" value="01/06/2020" id="start_date">
                                </div>
                            </div>
                            <div class="col-md-1 pt-2 text-center">
                                S/D
                            </div>
                            <div class="col-md-4">
                                <div class="input-group date">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" value="01/06/2020" id="end_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-info" onclick="filterData()"><i class="fa fa-search" aria-hidden="true"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5" style="display:none" id="textFilter">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" placeholder="Cari Data..." id="inputText">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-info" onclick="filterData()"><i class="fa fa-search" aria-hidden="true"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="loading">
                    <div class="spinner"></div>
                    <div class="textloading">Loading...</div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive p-3">
                            <table class="table align-items-center table-flush" id="tabledata">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>ID Mutasi</th>
                                        <th>Tanggal</th>
                                        <th>ID Transaksi</th>
                                        <th>Tipe Mutasi</th>
                                        <th>Keterangan</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>Last Balance</th>
                                        <th>Current Balance</th>                                      
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="pagination p-3" id="paggination">
                            <!-- <li class="page-item"><a class="page-link" href="#" id="prev">Previous</a></li>
                            <li class="page-item"><a class="page-link" href="#"><span id="page">1</span></a></li>
                            <li class="page-item"><a class="page-link" href="#" id="next">Next</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- tables-->
    </div>
    <!--Row-->
</div>
<!---Container Fluid-->

<script>
    $(document).ready(function() {
        var span = $("#page").text()
        console.log(parseInt(span)+1)
        getData(1,null,null,null,null).then(()=>{
            paggination(totaldata,1)
        })
        $('#selectFilter').change(function() {
            var selectedOption = $(this).val();
            filter = selectedOption
            // clear filter
            $("#inputText").val(null)
            $("#start_date").val(null)
            $("#end_date").val(null)
            if(selectedOption == 1){
                $("#textFilter").css('display','none'); 
                $("#dateFilter").css('display','block'); 
            }else if(selectedOption == 2 || selectedOption == 3){
                $("#dateFilter").css('display','none'); 
                $("#inputText").attr('placeholder',(selectedOption == 2 ? 'Cari ID Transaksi...' : 'Cari Partner Reff...'))
                $("#textFilter").css('display','block'); 
            }else{
                $("#dateFilter").css('display','none'); 
                $("#textFilter").css('display','none'); 
                getData(1,null,null,null,null).then(()=>{
                    paggination(totaldata,1)
                })
            }
        });
    });
    //loading control
    $(document).ajaxStart(function() {
        $('#loading').show();
    });

    $(document).ajaxStop(function() {
        $('#loading').hide();
    });

    var totaldata = 0
    var totalPage = 1
    var pagenow = 1;
    var filter = $("#selectFilter").val()
    var text = null
    var start = null
    var end = null
    function paggination(total,crpage){
        $('#paggination').html('')
        page = '<li class="page-item" onclick="prevOrNext(0)"><a class="page-link" id="prev">Previous</a></li>';
        let p = 0; 
        var no = 1;
        while(p < total){
            if(no == crpage){
                page = page + '<li class="page-item active" onclick="linkPage('+no+')"><a class="page-link" id="page-'+no+'">'+(no).toString() + '</a></li>'
            }else{
                page = page + '<li class="page-item" onclick="linkPage('+no+')"><a class="page-link" id="page-'+no+'">'+(no).toString() + '</a></li>'
            }
            no++;
            p = p + 20
        }
        totalPage = (no-1)
        pagenow = crpage
        page = page + '<li class="page-item" onclick="prevOrNext(1)"><a class="page-link" id="next">Next</a></li>'
        $('#paggination').append(page)
    }
    function linkPage(no){
        paggination(totaldata,no)
        getData(no,start,end,text,text)
    }
    function prevOrNext(direction){
        if(direction == 0){
            if(pagenow != 1 ){
                paggination(totaldata,(pagenow - 1))
                getData((pagenow - 1),start,end,text,text)
            }
        }else{
            if(pagenow !=  totalPage){
                paggination(totaldata,(pagenow + 1))
                getData((pagenow),start,end,text,text)
            }
        }
    }
    function filterData(){
        filter = $("#selectFilter").val()
        text = $("#inputText").val()
        start = $("#start_date").val()
        end = $("#end_date").val()
        if(filter != "0"){
            getData(1,start,end,text,text).then(()=>{
                paggination(totaldata,1)
            })
        }
    }

    function getData(page,start_date,end_date,id_transaksi,partner){
        return new Promise((resolve,reject)=>{
            let setOffset = page == 1 || page == 0 ? 0 : (page * 20) - 20
            let setLimit = 20
            $.ajax({
                    url: '<?php echo base_url('mutasiSaldo/getData') ?>',
                    method : "POST",
                    data : {
                        start_date: start_date,
                        end_date: end_date,
                        offset: setOffset,
                        limit: setLimit,
                        id_transaksi: id_transaksi,
                        partner: partner,
                    },
                    async : true,
                    success: function(response) {
                        totaldata = response.total
                        var data = response.data;
                        var tr_str = '';
                        if(data.length != 0 && data.length != undefined){
                            data.forEach((n,idx)=>{
                                if(idx == 0){
                                    tr_str = "<tr>" +
                                    "<td>" + (idx+setOffset+1) + "</td>" +
                                    "<td>" + n.idMutasi + "</td>" +
                                    "<td>" + n.tanggalMutasi + "</td>" +
                                    "<td>" + n.idTransaksi + "</td>" +
                                    "<td>" + n.tipeMutasi + "</td>" +
                                    "<td>" + n.keterangan + "</td>" +
                                    "<td>" + numberFormat(n.debet,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.kredit,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.lastBalance,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.currentBalannce,2,',','.') + "</td>" +
                                    "</tr>";
                                }else{
                                    tr_str = tr_str + "<tr>" +
                                    "<td>" + (idx+setOffset+1) + "</td>" +
                                    "<td>" + n.idMutasi + "</td>" +
                                    "<td>" + n.tanggalMutasi + "</td>" +
                                    "<td>" + n.idTransaksi + "</td>" +
                                    "<td>" + n.tipeMutasi + "</td>" +
                                    "<td>" + n.keterangan + "</td>" +
                                    "<td>" + numberFormat(n.debet,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.kredit,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.lastBalance,2,',','.') + "</td>" +
                                    "<td>" + numberFormat(n.currentBalannce,2,',','.') + "</td>" +
                                    "</tr>";
                                }
                            })
                        }else{
                            tr_str = "<tr>" +
                                    "<td colsapn='10'>Tidak ada data</td>" +
                                    "</tr>";
                        }
                        $("#tabledata tbody").html('');
                        $("#tabledata tbody").append(tr_str);
                        resolve()
                    }
                });
        })
    }
    function numberFormat(number, decimals, dec_point, thousands_sep) {
    decimals = typeof decimals !== 'undefined' ? decimals : 0;
    dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
    thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';
    
    var parts = number.toFixed(decimals).toString().split('.');
    
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
    
    return parts.join(dec_point);
}
</script>