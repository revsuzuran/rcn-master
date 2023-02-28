<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mt-2"><?= $title ?></h1>
    </div>

    <div class="row mb-3">
        <?php
        $nominalmatch = 0;
        $nominalUnmatch = 0;
        $Settlement = 0;
        $transaksiMatch = 0;
        $totalAmount = 0;
        $totalCount = 0;
        foreach ($dataMitra as $mitra) {
            $mitra['totalCountMitraD'] = 0;
            $mitra['totalAmountMitraD'] = 0;
            $mitra['totalCountMitraM'] = 0;
            $mitra['totalAmountMitraM'] = 0;
        }
        foreach ($data_rekon as $row) {
            $date = date_create($row['tanggal_rekon']);
            // $cekBulan = date_format($date, "n");
            $cekTgl = date_format($date, "j");
            $cektanggal = date_format($date, "Y-m-d");
            $mounthNow = date('m');
            $tglkemarin = date('Y-m-d', strtotime('-1 day'));

            $percentageSatu = 0;
            $totalDataSatu = isset($row['data_result1']) ? $row['data_result1']->compare_result->total_data : "0";
            $totalMatchSatu = isset($row['data_result1']) ? $row['data_result1']->compare_result->total_match : "0";
            if ($totalDataSatu != "0" || $totalMatchSatu != "0" || $totalUnMatchSatu != "0") {
                $percentageSatu = ($totalMatchSatu / $totalDataSatu) * 100;
            }

            $percentageSumSatu = 0;
            $totalSumDataSatu = isset($row['data_result1']->sum_result->total_sum) ? $row['data_result1']->sum_result->total_sum : "0";
            $sumMatchSatu = isset($row['data_result1']->sum_result->total_sum_match) ? $row['data_result1']->sum_result->total_sum_match : "0";
            $sumUnMatchSatu = isset($row['data_result1']->sum_result->total_sum_unmatch) ? $row['data_result1']->sum_result->total_sum_unmatch : "0";
            if ($totalSumDataSatu != "0" || $sumMatchSatu != "0" || $sumUnMatchSatu != "0") {
                $percentageSumSatu = ($sumMatchSatu / $totalSumDataSatu) * 100;
            }

            if ($mounthNow == date_format($date, "m")) {
                //card
                $nominalmatch = $nominalmatch + $sumMatchSatu;
                $nominalUnmatch = $nominalUnmatch + $sumUnMatchSatu;
                $transaksiMatch = $transaksiMatch + $totalMatchSatu;
                $Settlement = $Settlement + $totalSumDataSatu;

                //footer
                $totalAmount = $totalAmount + $totalSumDataSatu;
                $totalCount = $totalCount + $totalDataSatu;
            }

            foreach ($dataMitra as $key => $mitra) {
                if ($mitra['id_mitra'] == $row['id_mitra']) {
                    if ($cektanggal == $tglkemarin) {
                        $mitra['totalCountMitraD'] = $mitra['totalCountMitraD'] + $totalMatchSatu;
                        $mitra['totalAmountMitraD'] = $mitra['totalAmountMitraD'] + $totalSumDataSatu;
                    }
                    $mitra['totalCountMitraM'] = $mitra['totalCountMitraM'] + $totalMatchSatu;
                    $mitra['totalAmountMitraM'] = $mitra['totalAmountMitraM'] + $totalSumDataSatu;
                }
            }
            //grafik
            $array1[$cekTgl] = $array1[$cekTgl] + $sumMatchSatu;
            $array2[$cekTgl] = $array2[$cekTgl] + $sumUnMatchSatu;
        }
        ?>
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger">
                    <h6 class="m-0 font-weight-bold text-light">Dashboard v2</h6>
                </div>
                <div class="col-md-12 p-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card shadow">
                                <div class="card-body" style="
                                        padding-top: 10px;
                                        padding-left: 20px;
                                        padding-bottom: 10px;
                                    ">
                                    <div class="row">
                                        <div class="col-md-3 bg-info rounded text-center text-light" style="height:80px">
                                            <i class="fa fa-cog fa-3x" aria-hidden="true" style="padding-top:16px"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="col-md-12 mt-1">
                                                Nominal Match
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <b><?php echo rupiah($nominalmatch) ?></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow">
                                <div class="card-body" style="
                                        padding-top: 10px;
                                        padding-left: 20px;
                                        padding-bottom: 10px;
                                    ">
                                    <div class="row">
                                        <div class="col-md-3 bg-danger rounded text-center text-light" style="height:80px">
                                            <i class="fa fa-thumbs-up fa-3x" aria-hidden="true" style="padding-top:16px"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="col-md-12 mt-1">
                                                Nominal Unmatch
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <b><?php echo rupiah($nominalUnmatch) ?></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow">
                                <div class="card-body" style="
                                        padding-top: 10px;
                                        padding-left: 20px;
                                        padding-bottom: 10px;
                                    ">
                                    <div class="row">
                                        <div class="col-md-3 bg-success rounded text-center text-light" style="height:80px">
                                            <i class="fa fa-shopping-cart fa-3x" aria-hidden="true" style="padding-top:16px"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="col-md-12 mt-1">
                                                Settlement
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <b><?php echo rupiah($Settlement) ?></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow">
                                <div class="card-body" style="
                                        padding-top: 10px;
                                        padding-left: 20px;
                                        padding-bottom: 10px;
                                    ">
                                    <div class="row">
                                        <div class="col-md-3 bg-warning rounded text-center text-light" style="height:80px">
                                            <i class="fa fa-users fa-3x" aria-hidden="true" style="padding-top:16px"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="col-md-12 mt-1">
                                                Transaksi Match
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <b><?php echo $transaksiMatch ?></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 p-3">
                    <div class="card shadow mb-3">
                        <div class="card-header">Mounthly Recap Report</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <canvas id="chartArea" style="max-height:320px"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center mt-2">
                                            <h4>Legends</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Match</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="float-end"><?php echo rupiah($nominalmatch); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Unmatch</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="float-end"><?php echo rupiah($nominalUnmatch); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Current Available Deposit</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="float-end"><?php echo rupiah($fromAPI->data->balance); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row p-4">
                                <div class="col-md-3 text-center p-4" style="border-right: 1px solid black; border-right-width: thin;">
                                    <!-- <span class="text-success">
                                        <i class="fa fa-caret-up" aria-hidden="true"></i>17%
                                    </span>
                                    <br> -->
                                    <b>
                                        <?php
                                        $dailyAmount = $totalAmount != 0 ? ($totalAmount / $maxdate) : 0;
                                        echo rupiah(round($dailyAmount));
                                        ?>
                                    </b>
                                    <br>
                                    Average Amount daily
                                </div>
                                <div class="col-md-3 text-center p-4" style="border-right: 1px solid black; border-right-width: thin;">
                                    <!-- <span class="text-warning">
                                        <i class="fa fa-caret-left" aria-hidden="true"></i>0%
                                    </span>
                                    <br> -->
                                    <b>
                                        <?php
                                        $dailyCount = $totalCount != 0 ? ($totalCount / $maxdate) : 0;
                                        echo round($dailyCount, 2);
                                        ?>
                                    </b>
                                    <br>
                                    Average Count daily
                                </div>
                                <div class="col-md-3 text-center p-4" style="border-right: 1px solid black; border-right-width: thin;">
                                    <!-- <span class="text-success">
                                        <i class="fa fa-caret-up" aria-hidden="true"></i>20%
                                    </span>
                                    <br> -->
                                    <b>
                                        <?php
                                        $dailyAmountPersen = (($nominalmatch / $totalAmount) * 100);
                                        echo round($dailyAmountPersen, 2) . '%';
                                        ?>
                                    </b>
                                    <br>
                                    Average Prosentase Amount daily
                                </div>
                                <div class="col-md-3 text-center p-4">
                                    <!-- <span class="text-danger">
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>18%
                                    </span>
                                    <br> -->
                                    <b>
                                        <?php
                                        $dailyCountPersen = (($transaksiMatch / $totalCount) * 100);
                                        echo round($dailyCountPersen, 2) . '%';
                                        ?>
                                    </b>
                                    <br>
                                    Average Prosentase Count daily
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mt-3">
                                <div class="card-header">Resume Mitra Daily</div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-10">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Nama Mitra</th>
                                                    <th scope="col">Jumlah count match daily</th>
                                                    <th scope="col"> Jumlah sum amount daily</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dataMitra as $mitra) {
                                                    if (isset($_SESSION['masukMitra']) && $idmitra == $mitra['id_mitra']) { ?>
                                                        <tr>
                                                            <td><?= $mitra['nama_mitra'] ?></td>
                                                            <td><?php echo $mitra['totalCountMitraD']; ?></td>
                                                            <td><?php echo rupiah($mitra['totalAmountMitraD']); ?></td>
                                                        </tr>
                                                    <?php }
                                                    if (isset($_SESSION['masukAdmin'])) { ?>
                                                        <tr>
                                                            <td><?= $mitra['nama_mitra'] ?></td>
                                                            <td><?php echo $mitra['totalCountMitraD']; ?></td>
                                                            <td><?php echo rupiah($mitra['totalAmountMitraD']); ?></td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mt-3">
                                <div class="card-header">Resume Mitra Monthly</div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-10">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Nama Mitra</th>
                                                    <th scope="col">Jumlah count match monthly</th>
                                                    <th scope="col"> Jumlah sum amount monthly</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dataMitra as $mitra) {
                                                    if (isset($_SESSION['masukMitra']) && $idmitra == $mitra['id_mitra']) { ?>
                                                        <tr>
                                                            <td><?= $mitra['nama_mitra'] ?></td>
                                                            <td><?php echo $mitra['totalCountMitraM']; ?></td>
                                                            <td><?php echo rupiah($mitra['totalAmountMitraM']); ?></td>
                                                        </tr>
                                                    <?php }
                                                    if (isset($_SESSION['masukAdmin'])) { ?>
                                                        <tr>
                                                            <td><?= $mitra['nama_mitra'] ?></td>
                                                            <td><?php echo $mitra['totalCountMitraM']; ?></td>
                                                            <td><?php echo rupiah($mitra['totalAmountMitraM']); ?></td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- tables-->
    </div>
    <!--Row-->
</div>

<script>
    new Chart("chartArea", {
        type: "line",
        data: {
            labels: [
                <?php foreach ($array1 as $key => $data) { ?> '<?php echo $key; ?>',
                <?php } ?>
            ],
            datasets: [{
                    label: 'Match',
                    fill: true,
                    backgroundColor: "rgba(23, 117, 206, 0.8)",
                    pointRadius: 1,
                    borderColor: "rgba(23,117,206,0.8)",
                    data: [
                        <?php foreach ($array1 as $item) { ?> '<?php echo $item; ?>',
                        <?php } ?>
                    ]
                },
                {
                    label: 'Unmatch',
                    fill: true,
                    backgroundColor: "rgba(242, 12, 15, 0.8)",
                    pointRadius: 1,
                    borderColor: "rgba(242, 12, 15, 0.8)",
                    data: [
                        <?php foreach ($array2 as $item) { ?> '<?php echo $item; ?>',
                        <?php } ?>
                    ]
                }
            ]
        },
        options: {
            legend: {
                display: true
            },
            title: {
                display: false,
                text: "Sales: 1 Jan, 2014 - 30 Jul, 2014",
                fontSize: 16
            }
        }
    });
</script>