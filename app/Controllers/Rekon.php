<?php

namespace App\Controllers;
use App\Models\RekonBuff;
use App\Models\RekonBuffDetail;
use App\Models\RekonResult;
use App\Models\RekonUnmatch;

class Rekon extends BaseController
{
    public function __construct() {
        //mengisi variable global dengan data
        $this->session = session();
        $this->rekon_buff = new RekonBuff();
        $this->rekon_buff_detail = new RekonBuffDetail();
        $this->rekon_result = new RekonResult();
        $this->rekon_unmatch = new RekonUnmatch();
    }

    public function index()
    {
        return view('upload');
    }

    public function data_rekon_master()
    {
        // $this->session->set(array("uname" => "AQIL PRAKOSO"));
        $data['title'] = 'Data Rekon Master';
        $data['view'] = 'dashboard/rekon_master';

        $dataRekon = $this->rekon_buff->getRekonAll(0);

        $data['data_rekon'] = $dataRekon;
        return view('dashboard/layout', $data);
    }

    public function add_rekon_master()
    {
        // $this->session->set(array("uname" => "AQIL PRAKOSO"));
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon'; 

        return view('dashboard/layout', $data);
    }

    public function upload_data_rekon() {
        $csv = $this->request->getFile('csvFile');
        $namaRekon =$this->request->getPost('namaRekon');
        $tipe = $this->request->getPost('tipe');

        if($tipe == 1) {
            /* Create New Rekon and Save Id to Sessions */
            $id_rekon = $this->rekon_buff->getNextSequenceRekon(); // get id sequence
            $timestamp = date("Y-m-d h:i:sa");
            $this->rekon_buff->insertRekon($namaRekon, $id_rekon);
            $this->session->set('id_rekon', $id_rekon); // save id_rekon to session untuk nanti (tipe 2)
        } else {
            $id_rekon = $this->session->get('id_rekon');
        }       
        
        /* Save Tipe */
        $this->session->set('tipe', $tipe);

        $file = file($csv);
        $arrData = array();
        $strDataPreview = "";
        foreach($file as $key => $hehe) {
            // $dataArr = explode("\n",$hehe);
            // echo $hehe . "================";
            // echo json_encode(str_getcsv($hehe, ","));
            $drow = array(
                "data_asli" => $hehe,
                "data_string" => $hehe,
                "tipe" => $tipe,
                "id_rekon" => $id_rekon
            );
            array_push($arrData, $drow);
            if($key < 20) {
                $strDataPreview .= $hehe . "\r\n";
            }
        }
        // die();

        

        /* insert _buff data to Mongo DB  */
        // $handle = fopen($csv,"r");
        // $arrData = array();
        // $strDataPreview = ""; // save data string to preview
        // $countPreview = 0;
        // while (($row = fgetcsv($handle)) != FALSE) //get row vales
        // {
        //     var_dump($row) . "\n";
        //     $drow = array(
        //         "data_asli" => implode(",", $row),
        //         "data_string" => implode(",", $row),
        //         "tipe" => $tipe,
        //         "id_rekon" => $id_rekon
        //     );
        //     array_push($arrData, $drow);

        //     if($countPreview < 20) {
        //         $strDataPreview .= implode(",", $row) . "\r\n";
        //     }
        //     $countPreview++;

        // }
        // die();
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($arrData);
        log_message('info', 'DONE.. Writes To DATABASE...');

        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_delimiter';
        $data['csv_preview'] = $strDataPreview;
        return view('dashboard/layout', $data);
    }

    public function save_delimiter() {
        $delimiter =$this->request->getPost('delimiter');
        $id_rekon = $this->session->get('id_rekon');
        // $sampleCsv = $this->request->getPost('sampleCsv');
        $tipe = $this->session->get('tipe');

        $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        // echo json_encode($sampleCsv);
        /* Split data and save to Array to preview in tables */
        $dataCsvArr = array();
        $dataCsvSample = array();
        // $arrRow = explode("\r\n", $sampleCsv);
        foreach ($sampleCsv as $key => $valueRow) {
            $dataObj = str_getcsv($valueRow->data_asli, $delimiter);

            /* untuk diinsert ulang */
            $drow = array(
                "row_index" => $key,
                "data_asli" => $valueRow->data_asli,
                "data_row" => $dataObj,
                "tipe" => $valueRow->tipe,
                "id_rekon" => $valueRow->id_rekon,
            );
            array_push($dataCsvArr, $drow);

            /* untuk preview sample */
            if($key < 20) {
                array_push($dataCsvSample, $drow);
            }
            
        }
        
        /* delete all rekon to detail */
        log_message('info', 'DO Remove from DATABASE...');
        $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
        log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
        
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($dataCsvArr);
        log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');

        /* Save data delimiter to DB */
        $data = array(
            "delimiter" => $delimiter
        );
        $this->rekon_buff->updateRekon($id_rekon, $data);

        /* Prepare Preview */
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_cleaning';
        $data['data_csv'] = $dataCsvSample;

        return view('dashboard/layout', $data);
    }

    public function save_cleansing() {
        $radioSelect = $this->request->getPost('customRadio');
        $id_rekon = $this->session->get('id_rekon'); 

        if($radioSelect == "radioRowRemove") {
            $indexKolom = 0;
            $rule = "removeRow";
            $ruleVal = $this->request->getPost('rowRemoveStart') . "=>" . $this->request->getPost('rowRemoveEnd');
        } else if ($radioSelect == "radioReplace") {
            $indexKolom = $this->request->getPost('rowReplaceKolomIndex');
            $rule = "replace";
            $ruleVal = $this->request->getPost('rowReplaceOld') . "=>" . $this->request->getPost('rowReplaceNew');
        } else if ($radioSelect == "radioUpper") {
            $indexKolom = $this->request->getPost('rowUpperKolomIndex');
            $rule = "uppercase";
            $ruleVal = "";
        } else if ($radioSelect == "radioLower") {
            $indexKolom = $this->request->getPost('rowLowerKolomIndex');
            $rule = "lowercase";
            $ruleVal = "";
        }

        $objData = array(
            "index_kolom" => $indexKolom-1,
            "rule" => $rule,
            "rule_value" => $ruleVal
        );

        $dataSave = array(
            "clean_rule" => $objData
        );

        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);
        return $this->generate_clean_data($objData);        
    }


    public function generate_clean_data($dataClean) {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);

        /* Load Rule */
        $ruleOptions = $dataClean["rule"];
        $indexKolom = $dataClean["index_kolom"];
        $ruleValue = $dataClean["rule_value"];
        if($ruleOptions == "removeRow") {
            $ruleRemove = [];
            $rowRemoveArr = explode("=>", $ruleValue); 
            for($i=$rowRemoveArr[0];$i<=$rowRemoveArr[1];$i++) {
                array_push($ruleRemove, $i);
            }
        }
        

        $newDataCsv = array();
        $sampleDataCsv = array();
        $dataDb = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        $noIndex = 0;
        foreach($dataDb as $rowDB) {

            /* Cleaning Process */
            $newData = $rowDB->data_row;
            
            if($ruleOptions == "replace") {
                $ruleValue = explode("=>" ,$dataClean["rule_value"]); // rule values di split dulu khusus replace
                $valFind = $ruleValue[0];
                $valReplace = $ruleValue[1];
                $newData[$indexKolom] = str_replace($valFind, $valReplace, $newData[$indexKolom]);
            } else if ($ruleOptions == "uppercase") {
                $newData[$indexKolom] = strtoupper($newData[$indexKolom]); // replace with new rule
            } else if ($ruleOptions == "lowercase") {
                $newData[$indexKolom] = strtolower($newData[$indexKolom]); // replace with new rule
            } else if ($ruleOptions == "removeRow") {
                if (in_array($rowDB->row_index, $ruleRemove)) continue;
            }

            /* untuk diinsert ulang */
            $drow = array(
                "row_index" => $noIndex,
                "data_asli" => $rowDB->data_asli,
                "data_row" => $newData,
                "tipe" => $rowDB->tipe,
                "id_rekon" => $rowDB->id_rekon,
            );
            array_push($newDataCsv, $drow);
            
            /* untuk preview sample */
            if($noIndex < 20) {
                array_push($sampleDataCsv, $drow);
            }

            $noIndex++;
        }

        /* Khusus Untuk remove row => remove indexnya sebelum insert ke db */
        
        
        /* delete all rekon to detail */
        log_message('info', 'DO Remove from DATABASE...');
        $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
        log_message('info', 'DONE.. Remove ' .count($newDataCsv).' from DATABASE...');
        
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($newDataCsv);
        log_message('info', 'DONE.. Writes ' .count($newDataCsv).'  To DATABASE...');
        
        /* Prepare Preview */
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_cleaning';
        $data['data_csv'] = $sampleDataCsv;

        return view('dashboard/layout', $data);
    }

    public function add_rekon_data_to_compare() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

        if(!isset($id_rekon) || !isset($tipe)) {
            return $this->data_rekon_master();
        }

        $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 20);
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);

        /* Get Data Kolom Compare DB */
        $dataKolomDB = array();
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->tipe == $tipe) {
                array_push($dataKolomDB, $dataRow);
            }
        }

        /* Get Data Kolom SUM DB */
        $dataKolomSumDB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            if($dataRow->tipe == $tipe) {
                array_push($dataKolomSumDB, $dataRow);
            }
        }

        /* count and get collumn compare lenght */
        $dataKolomArr = array();
        foreach($sampleCsv as $rowCsv) {
            foreach($rowCsv->data_row as $keyIndex => $rowDataCsv) {

                /* if contain data already save.. then skip */
                $continue = true;
                foreach($dataKolomDB as $rowKolom) {
                    if($rowKolom->kolom_index == $keyIndex) $continue = false;
                }

                if (!$continue)
                    continue;

                $drow = array(
                    "kolom_index" => $keyIndex,
                    "kolom_name" => "KOLOM " . ($keyIndex + 1), 
                );
                array_push($dataKolomArr, $drow);
            }
            break; // only get first data;
        }

        
        /* count and get collumn SUM lenght */
        $dataKolomSumArr = array();
        foreach($sampleCsv as $rowCsv) {
            foreach($rowCsv->data_row as $keyIndex => $rowDataCsv) {

                /* if contain data already save.. then skip */
                $continue = true;
                foreach($dataKolomSumDB as $rowKolom) {
                    if($rowKolom->kolom_index == $keyIndex) $continue = false;
                }

                if (!$continue)
                    continue;

                $drow = array(
                    "kolom_index" => $keyIndex,
                    "kolom_name" => "KOLOM " . ($keyIndex + 1), 
                );
                array_push($dataKolomSumArr, $drow);
            }
            break; // only get first data;
        }

        /* Prepare Preview */
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_data_tocompare';
        $data['data_csv'] = $sampleCsv;
        $data['data_kolom'] = $dataKolomArr;
        $data['data_kolom_db'] = $dataKolomDB;
        $data['data_kolom_sum'] = $dataKolomSumArr;
        $data['data_kolom_sum_db'] = $dataKolomSumDB;
        
        return view('dashboard/layout', $data);
    }


    public function add_kolom_compare() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $objData = array(
            "kolom_index" => $rekonIndex,
            "kolom_name" => $rekonName,
            "tipe" => $tipe,
            "rule" => "equal",
            "rule_value" => "",
            "to_compare_index" => "",
            "to_compare_name" => "null",
        );

        $dataSave = array(
            "kolom_compare" => $objData
        );

        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);

        return "sukses";
    }

    public function rm_kolom_compare() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $this->rekon_buff->deleteKolomCompare($id_rekon, $tipe, $rekonIndex, $rekonName);

        return "sukses";
    }

    public function add_kolom_sum() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $objData = array(
            "kolom_index" => $rekonIndex,
            "kolom_name" => $rekonName,
            "tipe" => $tipe,
            "rule" => "equal",
            "rule_value" => "",
            "to_compare_index" => "",
            "to_compare_name" => "",
            "total" => 0
        );

        $dataSave = array(
            "kolom_sum" => $objData
        );

        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);

        return "sukses";
    }

    public function rm_kolom_sum() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $this->rekon_buff->deleteKolomSum($id_rekon, $tipe, $rekonIndex, $rekonName);

        return "sukses";
    }

    public function add_rekon_next() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

        if($tipe == 1) {
            $this->session->set('tipe', 2);
        }

        $data['title'] = 'Add New Rekon #2';
        $data['view'] = 'dashboard/add_rekon_two'; 

        return view('dashboard/layout', $data);
    }

    public function add_rekon_preview() {

        log_message('info', 'Prepare Preview..');
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

        if(!isset($id_rekon) || !isset($tipe)) {
            return $this->data_rekon_master();
        }

        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);

        /* get All rekons limit 5 */
        $dataRekon1DB = $this->rekon_buff_detail->getRekons($id_rekon, "1", 5);
        $dataRekon2DB = $this->rekon_buff_detail->getRekons($id_rekon, "2", 5);

        /* Get Data Index Compare DB */
        $dataIndexCompare1DB = array();
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->tipe == "1") array_push($dataIndexCompare1DB, $dataRow);
        }
        $dataIndexCompare2DB = array();
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->tipe == "2") array_push($dataIndexCompare2DB, $dataRow);
        }

        /* Get Data Index SUM DB */
        $dataKolomSumDB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            array_push($dataKolomSumDB, $dataRow);
        }

        /* collect data kolom compare */
        $dataKolomCompareArr = array();
        foreach($dataIndexCompare1DB as $rowCompare) {
            $dataRow = array();
            foreach ($dataRekon1DB as $rowDataRekon) {

                array_push($dataRow, $rowDataRekon->data_row[$rowCompare->kolom_index]);

            }
            $rowCompare["data_row"] = $dataRow;
            array_push($dataKolomCompareArr, $rowCompare);
        }

        /* collect data kolom compare #2 */
        $dataKolomCompareArr2 = array();
        foreach($dataIndexCompare2DB as $rowCompare) {
            $dataRow = array();
            foreach ($dataRekon2DB as $rowDataRekon) {

                array_push($dataRow, $rowDataRekon->data_row[$rowCompare->kolom_index]);

            }
            $rowCompare["data_row"] = $dataRow;
            array_push($dataKolomCompareArr2, $rowCompare);
        }
        

        $data['title'] = 'Add New Rekon Preview';
        $data['view'] = 'dashboard/add_rekon_data_preview'; 
        $data['data_compare_satu'] = $dataKolomCompareArr; 
        $data['data_compare_satu_db'] = $dataRekon1DB; 
        $data['data_compare_dua'] = $dataKolomCompareArr2; 
        $data['data_compare_dua_db'] = $dataRekon2DB; 
        log_message('info', 'Done Preview..');

        return view('dashboard/layout', $data);
    }

    public function save_compare() {
        $tipe = $this->request->getPost('tipe');
        $id_rekon = $this->session->get('id_rekon'); 

        if($tipe == 1) {
            $rule = $this->request->getPost('compareRadioSatu'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_satu');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_satu2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);
        } else {
            $rule = $this->request->getPost('compareRadioDua'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_dua');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_dua2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);
        }

        if($toKolIndex == "" || $toKolIndex == null) {
            return $this->add_rekon_preview();
        }

        $objData = array(
            "kolom_index" => $kolIndex,
            "kolom_name" => $kolName,
            "tipe" => $tipe,
            "rule" => $rule,
            "rule_value" => $ruleValue,
            "to_compare_index" => $toKolIndex,
            "to_compare_name" => $toKolName,
        );

        $dataSave = array(
            "kolom_compare" => $objData
        );


        $this->rekon_buff->deleteKolomCompare($id_rekon, $tipe, $kolIndex, $kolName);
        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);

        return $this->add_rekon_preview();
    }

    public function add_rekon_preview_sum() {

        log_message('info', 'Prepare Preview..');
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

        if(!isset($id_rekon) || !isset($tipe)) {
            return $this->data_rekon_master();
        }

        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);

        /* get All rekons limit 5 */
        $dataRekon1DB = $this->rekon_buff_detail->getRekons($id_rekon, "1", 5);
        $dataRekon2DB = $this->rekon_buff_detail->getRekons($id_rekon, "2", 5);

        /* Get Data Index Compare DB */
        $dataIndexCompare1DB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            if($dataRow->tipe == "1") array_push($dataIndexCompare1DB, $dataRow);
        }
        $dataIndexCompare2DB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            if($dataRow->tipe == "2") array_push($dataIndexCompare2DB, $dataRow);
        }

        // /* Get Data Index SUM DB */
        // $dataKolomSumDB = array();
        // foreach($dataRekon->kolom_sum as $dataRow) {
        //     array_push($dataKolomSumDB, $dataRow);
        // }

        /* collect data kolom compare */
        $dataKolomCompareArr = array();
        foreach($dataIndexCompare1DB as $rowCompare) {
            $dataRow = array();
            foreach ($dataRekon1DB as $rowDataRekon) {

                array_push($dataRow, $rowDataRekon->data_row[$rowCompare->kolom_index]);

            }
            $rowCompare["data_row"] = $dataRow;
            array_push($dataKolomCompareArr, $rowCompare);
        }

        /* collect data kolom compare #2 */
        $dataKolomCompareArr2 = array();
        foreach($dataIndexCompare2DB as $rowCompare) {
            $dataRow = array();
            foreach ($dataRekon2DB as $rowDataRekon) {

                array_push($dataRow, $rowDataRekon->data_row[$rowCompare->kolom_index]);

            }
            $rowCompare["data_row"] = $dataRow;
            array_push($dataKolomCompareArr2, $rowCompare);
        }
        

        $data['title'] = 'Add New Rekon Preview (Data to SUM)';
        $data['view'] = 'dashboard/add_rekon_data_preview_sum'; 
        $data['data_compare_satu'] = $dataKolomCompareArr; 
        $data['data_compare_satu_db'] = $dataRekon1DB; 
        $data['data_compare_dua'] = $dataKolomCompareArr2; 
        $data['data_compare_dua_db'] = $dataRekon2DB; 
        log_message('info', 'Done Preview..');

        return view('dashboard/layout', $data);
    }

    public function save_compare_sum() {
        $tipe = $this->request->getPost('tipe');
        $id_rekon = $this->session->get('id_rekon'); 

        if($tipe == 1) {
            $rule = $this->request->getPost('compareRadioSatu'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_satu');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_satu2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);
        } else {
            $rule = $this->request->getPost('compareRadioDua'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_dua');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_dua2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);
        }
        
        $objData = array(
            "kolom_index" => $kolIndex,
            "kolom_name" => $kolName,
            "tipe" => $tipe,
            "rule" => $rule,
            "rule_value" => $ruleValue,
            "to_compare_index" => $toKolIndex,
            "to_compare_name" => $toKolName,
            "total" => 0,
        );

        $dataSave = array(
            "kolom_sum" => $objData
        );

        $this->rekon_buff->deleteKolomSum($id_rekon, $tipe, $kolIndex, $kolName);
        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);

        return $this->add_rekon_preview_sum();
    }

    public function add_rekon_finish() {
        $id_rekon = $this->session->get('id_rekon'); 
        $this->rekon_buff->updateRekon($id_rekon, ["is_proses" => "pending"]);
        return $this->data_rekon_master();
    }

    public function rekon_result() {
        $id_rekon = $this->session->get('id_rekon');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 1);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2);

        $dataRekonSatu = array();
        $dataRekonDua = array();
        foreach ($rekonResult as $row) {
            if($row->tipe == 1) array_push($dataRekonSatu, $row);
            if($row->tipe == 2) array_push($dataRekonDua, $row);
        }
        // echo json_encode($rekonResult[0]->tipe);
        $data['title'] = 'Rekon Result';
        $data['view'] = 'dashboard/rekon_result'; 
        $data['data_rekon_satu'] = $dataRekonSatu; 
        $data['data_rekon_dua'] = $dataRekonDua; 
        $data['data_rekon_unmatch_satu'] = $dataRekon1unmatch; 
        $data['data_rekon_unmatch_dua'] = $dataRekon2unmatch; 
        // echo json_encode($dataRekonSatu);
        return view('dashboard/layout', $data);
    }

    public function rekon_result_post() {
        $id_rekon = $this->request->getPost('id_rekon');
        $this->session->set('id_rekon', $id_rekon);
        return "sukses";
    }
}

