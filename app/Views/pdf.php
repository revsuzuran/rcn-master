<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title;?></title>
        <style>

            body{
                font-family: 'Roboto', sans-serif;
                font-size: 11px;
                color:#4b4b4b;
            }

            #table {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
                font-size: 11px;
            }

            #table td, #table th {
                border: 0.1px solid #7f8c8d;
                padding: 5px;
            }

            .tablehehe  td, .tablehehe th {
                /* border: 0.1px solid #7f8c8d !important; */
                padding: 0px !important;
            }

            #table tr:nth-child(even){background-color: #f2f2f2;}

            /* #table tr:hover {background-color: #ddd;} */

            #table th {
                padding-top: 5px;
                padding-bottom: 5px;
                text-align: left;
                background-color: #95a5a6;
                color: white;
            }

            .label {
                font-weight: 600;
                color:#3d3d3d;
            }

            h3{
                color:#3d3d3d;
                text-align: center;
                font-size: 14px;
                font-weight: 700;
                /* background: #ededee; */
                margin: 0px 0px 10px 0px !important;
                padding: 10px;
                text-transform: uppercase;
            }

            .right {
                position: absolute;
                right: 0px;
            }

            .header-hehe {
                background:#dc3545;
                font-weight: 700;
                color: #fff;
                margin: 0px 0px 10px 0px !important;
                padding: 5px;
                margin:0px !important;
            }

            .header-hehe2 {
                border: 0.1px solid #7f8c8d;
                background:#95a5a6;
                font-weight: 700;
                color: #fff;
                margin: 0px 0px 10px 0px !important;
                padding: 5px;
                
                margin:0px !important;
            }

            .page_break { page-break-before: always; }
        </style>
    </head>

<?php

    function rupiah($angka){      
        
        if (!is_numeric($angka)) {
            return '-';
        }
        
        $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
        return $hasil_rupiah;    
    }

?>

    <body>
        <h3><?=$title;?></h3>
        <div style="margin-bottom:10px;">
            <table class="tablehehe">
                <tbody>
                    <tr>
                        <td>Time Submit</td>
                        <td>:</td>
                        <td><?= $data_rekon->timestamp; ?></td>
                    </tr>
                    <tr>
                        <td>Time Completed</td>
                        <td>:</td>
                        <td><?= isset($data_rekon->timestamp_complete) ? $data_rekon->timestamp_complete : "-"; ?></td>
                    </tr>
                </tbody>
            </table>            
        </div>
        <p class="header-hehe">Data Rekon #1</p>
        <table width="100%" class="tablehehe">
            <tr>
                <td class="text-align: left">
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2" class="label">Result Summary</td>
                            </tr>
                            <tr>
                                <td>Total Data</td>
                                <td>: <?= $data_rekon_satu[0]->compare_result->total_data; ?></td>
                            </tr>
                            <tr>
                                <td>Total Match</td>
                                <td>: <?= $data_rekon_satu[0]->compare_result->total_match; ?></td>
                            </tr>
                            <tr>
                                <td>Total UnMatch</td>
                                <td>: <?= $data_rekon_satu[0]->compare_result->total_unmatch; ?></td>
                            </tr>
                        </tbody>
                    </table>  
                </td>
                <td style="vertical-align:top">
                    <table align="right">
                        <tbody>
                            <tr>
                                <td colspan="2" class="label">Summarize Result </td>
                            </tr>
                            <tr>
                                <td><?= "Total "?></td>
                                <td><?= ": " .rupiah((isset($data_rekon_satu[0]->sum_result->total_sum) ? $data_rekon_satu[0]->sum_result->total_sum : 0)); ?></td>
                            </tr>
                            <tr>
                                <td><?= "Total Match" ?></td>
                                <td><?= ": " .rupiah((isset($data_rekon_satu[0]->sum_result->total_sum_match) ? $data_rekon_satu[0]->sum_result->total_sum_match : 0)); ?></td>
                            </tr>
                            <tr>
                                <td><?= "Total UnMatch" ?></td>
                                <td><?= ": " .rupiah((isset($data_rekon_satu[0]->sum_result->total_sum_unmatch) ? $data_rekon_satu[0]->sum_result->total_sum_unmatch : 0)); ?></td>
                            </tr>
                            
                        </tbody>
                    </table>  
                </td>
            </tr>
        </table><br>
        <p class="header-hehe2">Data Unmatch</p>    
        <table id="table">
            <thead>
                <tr>
                    <th>No</th>
                    <?php foreach ($data_rekon_unmatch_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                    <?php break;} ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_rekon_unmatch_satu as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_satu)) continue; ?>
                          <td><?= $rowData; ?></td>
                        <?php  } ?>
                  </tr>
                <?php  } ?>
            </tbody>
        </table>
        <br>
        <p class="header-hehe2">Data Match</p>    
        <table id="table">
            <thead>
                <tr>
                    <th>No</th>
                    <?php foreach ($data_rekon_match_satu as $row) { ?>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                            if (!in_array($key, $kolom_filter_satu)) continue; ?>
                            <th><?= "KOLOM " . ($key+1) ?> </th>
                        <?php } ?>
                    <?php break;} ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_rekon_match_satu as $key => $row) { ?> 
                    <tr>
                        <td><?= ($key+1) ?></td>
                        <?php foreach ($row['row_data'] as $key => $rowData) { 
                          if (!in_array($key, $kolom_filter_satu)) continue; ?>
                          <td><?= $rowData; ?></td>
                        <?php  } ?>
                  </tr>
                <?php  } ?>
            </tbody>
        </table>
    </body>
</html>