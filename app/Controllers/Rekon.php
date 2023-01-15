<?php

namespace App\Controllers;
use App\Models\RekonBuff;
use App\Models\RekonBuffDetail;
use App\Models\RekonResult;
use App\Models\RekonUnmatch;
use App\Models\RekonMatch;
use App\Models\DataModels;
use App\Models\DBModel;
use App\Models\Postgres;
use App\Libraries\PdfGenerator;


class Rekon extends BaseController
{
    public function __construct() {
        //mengisi variable global dengan data
        $this->request = \Config\Services::request(); 
        $this->session = session();
        $this->rekon_buff = new RekonBuff();
        $this->rekon_buff_detail = new RekonBuffDetail();
        $this->rekon_result = new RekonResult();
        $this->rekon_unmatch = new RekonUnmatch();
        $this->rekon_match = new RekonMatch();
        $this->data_model = new DataModels();
        $this->dbModel = new DBModel();
        $this->pg = new Postgres();
        $this->pdfGen = new PdfGenerator();
    
		$this->uri = $this->request->uri;
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
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon'; 
        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSetting();
        
        return view('dashboard/layout', $data);
    }

    public function upload_data_rekon() {
        $namaRekon =$this->request->getPost('namaRekon');
        $tipe = $this->request->getPost('tipe');
        $radioTipe = $this->request->getPost('radioUpload');
        $csv = $this->request->getFile('csvFile');

        $dataFtp = $this->data_model->getFtp();
        
        if($radioTipe == "ftp") {
            try {
                $namaFile = $this->request->getPost('nama_file') ;
                $ftpOptionID = $this->request->getPost('ftp_option') ;
                $dataFtp = $this->data_model->getFtpOne($ftpOptionID);
                $pathFile = $dataFtp->path;
                $source = "$pathFile$namaFile.csv";
                $target = fopen($source, "w");
                $conn = ftp_connect($dataFtp->domain) or die("Could not connect");
                ftp_login($conn,$dataFtp->username,$dataFtp->password);
                ftp_fget($conn,$target,$source,FTP_ASCII);
                $csv = $source;
            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', 'FTP Error! Failed to get file or file not found');
                if($tipe == 1) return redirect()->to(base_url('rekon/add'));
                else return redirect()->to(base_url('rekon/add_rekon_next'));
            }
            
        } else if($radioTipe == "db") {
            try {
                $namaFile = $this->request->getPost('nama_file');
                $dbOptionID = $this->request->getPost('db_option');
                $query = $this->request->getPost('query');
                $dataDB = $this->data_model->getDatabaseOne($dbOptionID);

                $this->dbModel->initConnection($dataDB->hostname, $dataDB->username, $dataDB->password, $dataDB->database, $dataDB->driver, $dataDB->port);
                $result = $this->dbModel->getData($query);

            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', 'FTP Error! Failed to get file or file not found');
                if($tipe == 1) return redirect()->to(base_url('rekon/add'));
                else return redirect()->to(base_url('rekon/add_rekon_next'));
            }
            
        } else {
            $csv = $this->request->getFile('csvFile');
        }

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

        if($radioTipe == "db") {
            /* Langsung Save Data Ke DB tanpa pilih delimiter */
            $dataCsvArr = array();

            /* Insert Header */
            $arrData = array_keys($result[0]);
            $drow = array(
                "row_index" => 0,
                "data_asli" => "header",
                "data_row" => $arrData,
                "tipe" => $tipe,
                "id_rekon" => $id_rekon,
            );
            array_push($dataCsvArr, $drow);

            foreach($result as $index => $row) {

                $arrData = array();
                foreach($row as $rowData) {
                    array_push($arrData, $rowData);
                }

                /* untuk diinsert ulang */
                $drow = array(
                    "row_index" => $index+1,
                    "data_asli" => $row,
                    "data_row" => $arrData,
                    "tipe" => $tipe,
                    "id_rekon" => $id_rekon,
                );
                array_push($dataCsvArr, $drow);
            }

            /* delete all rekon to detail */
            log_message('info', 'DO Remove from DATABASE...');
            $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
            log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
            
            /* insert all rekon to detail */
            log_message('info', 'DO Writes To DATABASE...');
            $this->rekon_buff_detail->insertRekonMany($dataCsvArr);
            log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');
            
            // echo json_encode($dataCsvArr);die();
            return redirect()->to(base_url('rekon/cleansing_data'));

        }

        if($csv == "") {
            $this->session->setFlashdata('error', 'Failed to process file!');
            if($tipe == 1) return redirect()->to(base_url('rekon/add'));
            else return redirect()->to(base_url('rekon/add_rekon_next'));
        }

        $file = file($csv);
        if($radioTipe == "ftp") unlink("$namaFile.csv"); // remove ftp files
        $arrData = array();
        $strDataPreview = "";
        foreach($file as $key => $hehe) {
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

        $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($arrData);
        log_message('info', 'DONE.. Writes To DATABASE...');

        return redirect()->to(base_url('rekon/delimiter'));

       
    }

    public function add_rekon_delimiter() {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');
        $dataRekon = $this->rekon_buff_detail->getRekons($id_rekon, $tipe);
        // var_dump($id_rekon, $tipe);
        $strDataPreview = "";
        foreach($dataRekon as $key => $row) {
            if($key < 20) {
                $strDataPreview .= $row->data_asli . "\r\n";
            }
        }        

        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_delimiter';
        $data['csv_preview'] = $strDataPreview;
        return view('dashboard/layout', $data);
    }

    public function save_delimiter() {
        $delimiter =$this->request->getPost('delimiter');
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');

        $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        
        /* Split data and save to Array to preview in tables */
        $dataCsvArr = array();
        $dataCsvSample = array();
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

        return redirect()->to(base_url('rekon/cleansing_data'));
    }

    public function cleansing_data() {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');
        $limit = $this->uri->getSegment(3);
        
        if($limit == "all") {
            $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        } else {
            $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 20);
        }
        
        $dataCsvSample = array();
        foreach ($sampleCsv as $key => $valueRow) {
            /* untuk preview sample */
            if($limit != "all") {
                if($key < 20) {
                    array_push($dataCsvSample, $valueRow);
                }
            } else {
                array_push($dataCsvSample, $valueRow);
            }           
            
        }

        /* Prepare Preview */
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_cleaning';
        $data['data_csv'] = $dataCsvSample;

        return view('dashboard/layout', $data);
    }

    public function save_cleansing() {
        $radioSelect = $this->request->getPost('customRadio');
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

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
        } else if ($radioSelect == "radioRegex") {
            log_message('info', 'TRY REGEX');
            $indexKolom = $this->request->getPost('rowLowerKolomIndex');
            $rule = "regex";
            $ruleVal = $this->request->getPost('rowRegexReplaceOld') . "=>" . $this->request->getPost('rowRegexReplaceNew');
        } else {
            $this->session->setFlashdata('error', 'Failed to Save! Try Again');
            return redirect()->to(base_url('rekon/cleansing_data'));
        }

        $objData = array(
            "index_kolom" => $indexKolom-1,
            "rule" => $rule,
            "rule_value" => $ruleVal,
            "tipe" => $tipe
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
                $newData[$indexKolom] = preg_replace('/' . $valFind . '/', $valReplace, $newData[$indexKolom], 1);
            } else if($ruleOptions == "regex") {
                log_message('info', 'TRY REGEX');
                $ruleValue = explode("=>" ,$dataClean["rule_value"]); // rule values di split dulu khusus replace
                $valFind = $ruleValue[0];
                $valReplace = $ruleValue[1];
                $newData[$indexKolom] = preg_replace($valFind, $valReplace, $newData[$indexKolom]);
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
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);

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

        /* Setting */
        $kolomCompare = array();
        foreach($rekonBuff->kolom_compare as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($kolomCompare, $row);
        }

        $kolomSum = array();
        foreach($rekonBuff->kolom_sum as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($kolomSum, $row);
        }

        $cleanRule = array();
        foreach($rekonBuff->clean_rule as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($cleanRule, $row);
        }

        $dataSetting = array(
            "kolom_compare" => $kolomCompare,
            "kolom_sum" => $kolomSum,
            "delimiter" => $rekonBuff->delimiter,
            "clean_rule" => $cleanRule

        );
        

        /* Prepare Preview */
        $data['nama_rekon'] = $rekonBuff->nama_rekon;
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_data_tocompare';
        $data['data_csv'] = $sampleCsv;
        $data['data_kolom'] = $dataKolomArr;
        $data['data_kolom_db'] = $dataKolomDB;
        $data['data_kolom_sum'] = $dataKolomSumArr;
        $data['data_kolom_sum_db'] = $dataKolomSumDB;
        $data['data_setting'] = $dataSetting;
        
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

        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSetting();

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
        

        $data['title'] = 'Compare Data';
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

        if($rule == null) {
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

        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->to_compare_index == "") {
                $this->session->setFlashdata('error', 'Semua data wajib di compare');
                return redirect()->to(base_url('rekon/rekon_preview'));
            }
        }

        /* Get Data Index Compare DB */
        $dataIndexCompare1DB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            
            if($dataRow->tipe == "1") array_push($dataIndexCompare1DB, $dataRow);
        }
        $dataIndexCompare2DB = array();
        foreach($dataRekon->kolom_sum as $dataRow) {
            if($dataRow->tipe == "2") array_push($dataIndexCompare2DB, $dataRow);
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
        

        $data['title'] = 'Compare Data Sum';
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


        /* Get Data Rekon Master */
        // $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        // foreach($dataRekon->kolom_sum as $dataRow) {
        //     if($dataRow->to_compare_index == "") {
        //         $this->session->setFlashdata('error', 'Semua data wajib di compare');
        //         return redirect()->to(base_url('rekon/rekon_preview_sum'));
        //     }
        // }

        $this->rekon_buff->updateRekon($id_rekon, ["is_proses" => "pending"]);
        return redirect()->to(base_url('rekon'));
    }

    public function rekon_result() {
        $id_rekon = $this->session->get('id_rekon');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 1);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2);
        $dataRekon1match = $this->rekon_match->getRekonAll($id_rekon, 1);
        $dataRekon2match = $this->rekon_match->getRekonAll($id_rekon, 2);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 1) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

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
        $data['data_rekon_match_satu'] = $dataRekon1match; 
        $data['data_rekon_match_dua'] = $dataRekon2match; 
        $data['kolom_filter_satu'] = $kolomFilter1; 
        $data['kolom_filter_dua'] = $kolomFilter2; 
        // echo json_encode($dataRekonSatu);
        return view('dashboard/layout', $data);
    }

    public function rekon_result_post() {
        $id_rekon = $this->request->getPost('id_rekon');
        $this->session->set('id_rekon', $id_rekon);
        return "sukses";
    }

    public function generate_pdf()
    {

        /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 1, 25);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2, 25);
        $dataRekon1match = $this->rekon_match->getRekonAll($id_rekon, 1, 25);
        $dataRekon2match = $this->rekon_match->getRekonAll($id_rekon, 2, 25);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 1) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

        $dataRekonSatu = array();
        $dataRekonDua = array();
        foreach ($rekonResult as $row) {
            if($row->tipe == 1) array_push($dataRekonSatu, $row);
            if($row->tipe == 2) array_push($dataRekonDua, $row);
        }
        
        $data['title'] =  $rekonBuff->nama_rekon;
        $data['view'] = 'dashboard/rekon_result'; 
        $data['data_rekon'] = $rekonBuff; 
        $data['data_rekon_satu'] = $dataRekonSatu; 
        $data['data_rekon_dua'] = $dataRekonDua; 
        $data['data_rekon_unmatch_satu'] = $dataRekon1unmatch; 
        $data['data_rekon_unmatch_dua'] = $dataRekon2unmatch; 
        $data['data_rekon_match_satu'] = $dataRekon1match; 
        $data['data_rekon_match_dua'] = $dataRekon2match; 
        $data['kolom_filter_satu'] = $kolomFilter1; 
        $data['kolom_filter_dua'] = $kolomFilter2; 

        /* Preparing DomPDF */
        $Pdfgenerator = $this->pdfGen;
        $file_pdf = 'Result '.$rekonBuff->nama_rekon;
        $paper = 'A4';
        $orientation = "portrait";
        $html = view('pdf', $data);
        // return $html;
        $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    public function generate_pdf2()
    {

        /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2, 25);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2, 25);
        $dataRekon1match = $this->rekon_match->getRekonAll($id_rekon, 2, 25);
        $dataRekon2match = $this->rekon_match->getRekonAll($id_rekon, 2, 25);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 2) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

        $dataRekonSatu = array();
        $dataRekonDua = array();
        foreach ($rekonResult as $row) {
            if($row->tipe == 2) array_push($dataRekonSatu, $row);
            if($row->tipe == 2) array_push($dataRekonDua, $row);
        }
        
        $data['title'] =  $rekonBuff->nama_rekon;
        $data['view'] = 'dashboard/rekon_result'; 
        $data['data_rekon'] = $rekonBuff; 
        $data['data_rekon_satu'] = $dataRekonSatu; 
        $data['data_rekon_dua'] = $dataRekonDua; 
        $data['data_rekon_unmatch_satu'] = $dataRekon1unmatch; 
        $data['data_rekon_unmatch_dua'] = $dataRekon2unmatch; 
        $data['data_rekon_match_satu'] = $dataRekon1match; 
        $data['data_rekon_match_dua'] = $dataRekon2match; 
        $data['kolom_filter_satu'] = $kolomFilter1; 
        $data['kolom_filter_dua'] = $kolomFilter2; 

        /* Preparing DomPDF */
        $Pdfgenerator = $this->pdfGen;
        $file_pdf = 'Result '.$rekonBuff->nama_rekon;
        $paper = 'A4';
        $orientation = "portrait";
        $html = view('pdf2', $data);
        // return $html;
        $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    public function export_unmatch()
    {
         /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $id = $this->uri->getSegment(3);
        $tipe = $this->uri->getSegment(4);

        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 1);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, 2);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 2) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

        if($id == 1) {
            $dataRekonUnmatch = $dataRekon1unmatch;
            $kolomFilter = $kolomFilter1;
        } else if($id == 2) {
            $dataRekonUnmatch = $dataRekon2unmatch;
            $kolomFilter = $kolomFilter2;
        } else {
            die("no data");
        }
        
        $delimiter = ","; 
        $filename = 'Result '.$rekonBuff->nama_rekon . "#$id-unmatch.csv"; 
        
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
        
        foreach($dataRekonUnmatch as $row) {
            $dataUnmatch = array();
            foreach ($row['row_data'] as $key => $rowData) {
                if($tipe == 0) {
                    if (!in_array($key, $kolomFilter)) continue;
                    array_push($dataUnmatch, $rowData);
                } else if($tipe == 1) {
                    array_push($dataUnmatch, $rowData);
                } else {
                    die("no data");
                }
                
            }
            fputcsv($f, $dataUnmatch, $delimiter); 
        }
        

        // Move back to beginning of file 
        fseek($f, 0);
        
        // Set headers to download file rather than displayed 
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="' . $filename . '";'); 
        
        //output all remaining data on a file pointer 
        fpassthru($f); 
        
    }

    public function export_match()
    {
        /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $id = $this->uri->getSegment(3);
        $tipe = $this->uri->getSegment(4);

        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $rekonResult = $this->rekon_result->getRekon($id_rekon);
        $dataRekon1match = $this->rekon_match->getRekonAll($id_rekon, 1);
        $dataRekon2match = $this->rekon_match->getRekonAll($id_rekon, 2);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 2) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

        if($id == 1) {
            $dataRekonMatch = $dataRekon1match;
            $kolomFilter = $kolomFilter1;
        } else if($id == 2) {
            $dataRekonMatch = $dataRekon2match;
            $kolomFilter = $kolomFilter2;
        } else {
            die("no data");
        }
        
        $delimiter = ","; 
        $filename = 'Result '.$rekonBuff->nama_rekon . "#$id-match.csv"; 
        
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
        
        foreach($dataRekonMatch as $row) {
            $dataMatch = array();
            foreach ($row['row_data'] as $key => $rowData) {
                if($tipe == 0) {
                    if (!in_array($key, $kolomFilter)) continue;
                    array_push($dataMatch, $rowData);
                } else if($tipe == 1) {
                    array_push($dataMatch, $rowData);
                } else {
                    die("no data");
                }
                
            }
            fputcsv($f, $dataMatch, $delimiter); 
        }
        

        // Move back to beginning of file 
        fseek($f, 0);
        
        // Set headers to download file rather than displayed 
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="' . $filename . '";'); 
        
        //output all remaining data on a file pointer 
        fpassthru($f); 
        
    }

    public function upload_with_setting() {
        /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $id_setting = $this->request->getPost('id_setting');
        $namaRekon =$this->request->getPost('namaRekon');
        $tipe = $this->request->getPost('tipe');
        $radioTipe = $this->request->getPost('radioUpload');

        if($id_setting == "0") {
            $this->session->setFlashdata('error', 'Failed to get data setting');
            if($tipe == 1) return redirect()->to(base_url('rekon/add'));
            else return redirect()->to(base_url('rekon/add_rekon_next'));
        }

        /* Load Setting */
        $setting = $this->data_model->getSettingOne($id_setting);

        $dataFtp = $this->data_model->getFtp();
        
        if($radioTipe == "ftp") {
            try {
                $namaFile = $this->request->getPost('nama_file') ;
                $ftpOptionID = $this->request->getPost('ftp_option') ;
                $dataFtp = $this->data_model->getFtpOne($ftpOptionID);
                $pathFile = $dataFtp->path;
                $source = "$pathFile$namaFile.csv";
                $target = fopen($source, "w");
                $conn = ftp_connect($dataFtp->domain) or die("Could not connect");
                ftp_login($conn,$dataFtp->username,$dataFtp->password);
                ftp_fget($conn,$target,$source,FTP_ASCII);
                $csv = $source;
            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', 'FTP Error! Failed to get file or file not found');
                if($tipe == 1) return redirect()->to(base_url('rekon/add'));
                else return redirect()->to(base_url('rekon/add_rekon_next'));
            }
            
        } else if($radioTipe == "db") {
            try {
                $namaFile = $this->request->getPost('nama_file');
                $dbOptionID = $this->request->getPost('db_option');
                $query = $this->request->getPost('query');
                $dataDB = $this->data_model->getDatabaseOne($dbOptionID);

                $this->dbModel->initConnection($dataDB->hostname, $dataDB->username, $dataDB->password, $dataDB->database, $dataDB->driver, $dataDB->port);
                $result = $this->dbModel->getData($query);

            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', 'FTP Error! Failed to get file or file not found');
                if($tipe == 1) return redirect()->to(base_url('rekon/add'));
                else return redirect()->to(base_url('rekon/add_rekon_next'));
            }
            
        } else {
            $csv = $this->request->getFile('csvFile');
        }

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

        if($radioTipe == "db") {
            /* Langsung Save Data Ke DB tanpa pilih delimiter */
            $dataCsvArr = array();

            /* Insert Header */
            $arrData = array_keys($result[0]);
            $drow = array(
                "row_index" => 0,
                "data_asli" => "header",
                "data_row" => $arrData,
                "tipe" => $tipe,
                "id_rekon" => $id_rekon,
            );
            array_push($dataCsvArr, $drow);

            foreach($result as $index => $row) {

                $arrData = array();
                foreach($row as $rowData) {
                    array_push($arrData, $rowData);
                }

                /* untuk diinsert ulang */
                $drow = array(
                    "row_index" => $index+1,
                    "data_asli" => $row,
                    "data_row" => $arrData,
                    "tipe" => $tipe,
                    "id_rekon" => $id_rekon,
                );
                array_push($dataCsvArr, $drow);
            }

            /* delete all rekon to detail */
            log_message('info', 'DO Remove from DATABASE...');
            $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
            log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
            
            /* insert all rekon to detail */
            log_message('info', 'DO Writes To DATABASE...');
            $this->rekon_buff_detail->insertRekonMany($dataCsvArr);
            log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');
            
            
            $this->auto_cleaning($setting->clean_rule);     
            $this->auto_save_compare($setting->kolom_compare);
            $this->auto_save_sum($setting->kolom_sum);
            if($tipe == "1") {
                return redirect()->to(base_url('rekon/add_rekon_next'));
            } else {
                return redirect()->to(base_url('rekon/rekon_preview'));
            }

        }

        if($csv == "") {
            $this->session->setFlashdata('error', 'Failed to process file!');
            if($tipe == 1) return redirect()->to(base_url('rekon/add'));
            else return redirect()->to(base_url('rekon/add_rekon_next'));
        }

        $file = file($csv);
        if($radioTipe == "ftp") unlink("$namaFile.csv"); // remove ftp files
        $arrData = array();
        $strDataPreview = "";
        foreach($file as $key => $hehe) {
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

        $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($arrData);
        log_message('info', 'DONE.. Writes To DATABASE...');


        
        $this->auto_proses_delimiter($setting->delimiter);
        $this->auto_cleaning($setting->clean_rule);     
        $this->auto_save_compare($setting->kolom_compare);
        $this->auto_save_sum($setting->kolom_sum);
        if($tipe == "1") {
            return redirect()->to(base_url('rekon/add_rekon_next'));
        } else {
            return redirect()->to(base_url('rekon/rekon_preview'));
        }

    }

    public function auto_proses_delimiter($delimiter) {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');

        $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        
        /* Split data and save to Array to preview in tables */
        $dataCsvArr = array();
        $dataCsvSample = array();
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
    }

    public function auto_cleaning($clean_rule) {

        $tipe = $this->session->get('tipe');
        $id_rekon = $this->session->get('id_rekon');

        foreach($clean_rule as $row) {
            $objData = array(
                "index_kolom" => $row->index_kolom,
                "rule" => $row->rule,
                "rule_value" => $row->rule_value,
                "tipe" => $tipe
            );
            $dataSave = array(
                "clean_rule" => $objData
            );
            $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);
            $this->auto_proses_cleaning($objData);
        }
    }

    public function auto_proses_cleaning($dataClean) {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

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
                $newData[$indexKolom] = preg_replace('/' . $valFind . '/', $valReplace, $newData[$indexKolom], 1);
            } else if($ruleOptions == "regex") {
                log_message('info', 'TRY REGEX');
                $ruleValue = explode("=>" ,$dataClean["rule_value"]); // rule values di split dulu khusus replace
                $valFind = $ruleValue[0];
                $valReplace = $ruleValue[1];
                $newData[$indexKolom] = preg_replace($valFind, $valReplace, $newData[$indexKolom]);
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
    }

    public function auto_save_compare($kolomCompare) {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        
        foreach ($kolomCompare as $row) {            
            $objData = array(
                "kolom_index" => $row->kolom_index,
                "kolom_name" => $row->kolom_name,
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
        }
    }

    public function auto_save_sum($kolomSum) {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        
        foreach ($kolomSum as $row) {
            $objData = array(
                "kolom_index" => $row->kolom_index,
                "kolom_name" => $row->kolom_name,
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
        }       
    }
}

