<?php

namespace App\Controllers;
use App\Models\MitraModel;
use App\Models\RekonBuff;
use App\Models\RekonBuffDetail;
use App\Models\RekonResult;
use App\Models\RekonUnmatch;
use App\Models\RekonMatch;
use App\Models\DataModels;
use App\Models\DBModel;
use App\Models\Postgres;
use App\Models\ChannelModel;
use App\Libraries\PdfGenerator;
use App\Libraries\Encryption;


class RekonUnmatchBulanan extends BaseController
{

    protected $request;
    protected $session;
    protected $rekon_buff;
    protected $rekon_buff_detail;
    protected $rekon_result;
    protected $rekon_unmatch;
    protected $rekon_match;
    protected $data_model;
    protected $dbModel;
    protected $pg;
    protected $channel_model;
    protected $pdfGen;
    protected $mitra;
    protected $uri;

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
        $this->channel_model = new ChannelModel();
        $this->pdfGen = new PdfGenerator();
        $this->mitra = new MitraModel();    
		$this->uri = $this->request->uri;
    }

    public function data_rekon()
    {
        $data['title'] = 'Data Rekon Unmatch Akhir Bulan';
        $data['view'] = 'rekon_unmatch/rekon_master';
        $rekonResult = $this->rekon_result->getRekonsUnmatchAkhirBulan();
        
        foreach($rekonResult as $index => $row) {
            $dataMitra = $this->mitra->getMitra($row['id_mitra']);
            $rekonResult[$index]->nama_mitra = $dataMitra->nama_mitra;
        }

        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }

    public function add_rekon()
    {
        $data['title'] = 'Add Rekon Unmatch Akhir Bulan';
        $data['view'] = 'rekon_unmatch/add_rekon'; 
        $dataChannel = $this->channel_model->getAllChannel();
        
        foreach($dataChannel as $key => $row) {
            $dataMitra = $this->mitra->getMitra($row->id_mitra);
            $dataChannel[$key]->nama_mitra = $dataMitra->nama_mitra;
        }
        
        $data['data_channel'] = $dataChannel;        
        return view('dashboard/layout', $data);
    }

    public function proses_rekon() {

        /* decrypt internal */
        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo json_encode(array("response_code" => "XX", "response_desc" => "Failed Decrypt"));
            die;
        }

        /* get sequence idrekon */
        log_message('info', '== PROSES REKON UNMATCH BULANAN ==');
        $id_rekon = $this->rekon_buff->getNextSequenceRekon(); // get id sequence
        $id_rekon_header_satu = ""; // untuk mengambil data header dari rekon sebelumnya
        $id_rekon_header_dua = ""; // untuk mengambil data header dari rekon sebelumnya

        /* decode and parsing json */
        $decryptedData = json_decode($decryptedData);
        $namaRekon = $decryptedData->nama_rekon;
        $idChannel =  $decryptedData->opt_channel;
        $tanggal_rekon =  $decryptedData->tanggal_rekon;
        $tanggal_rekon_awal_satu =  $decryptedData->tanggal_rekon_awal_satu;
        $tanggal_rekon_akhir_satu =  $decryptedData->tanggal_rekon_akhir_satu;
        $tanggal_rekon_awal_dua =  $decryptedData->tanggal_rekon_awal_dua;
        $tanggal_rekon_akhir_dua =  $decryptedData->tanggal_rekon_akhir_dua;
        
        /* Get Data Rekon Unmatch Satu */
        $dataUnmatchSatu = array();
        $dataRekonResultSatu = $this->rekon_result->getRekonsByIdChannel($idChannel,$tanggal_rekon_awal_satu,$tanggal_rekon_akhir_satu);
        $counterRekonSatu = 1;
        foreach($dataRekonResultSatu as $key => $row) {
            $id_rekon_header_satu = $row->id_rekon;
            $tempDataSatu = $this->rekon_unmatch->getRekonsByIdRekonResult($row->id_rekon_result, 1);
            foreach($tempDataSatu as $index => $rowTemp) {
                /* untuk diinsert */
                $drow = array(
                    "row_index" => $counterRekonSatu,
                    "data_asli" => "", 
                    "data_row" => $rowTemp->row_data,
                    "tipe" => (string) $rowTemp->tipe,
                    "id_rekon" => $id_rekon,
                );
                array_push($dataUnmatchSatu, $drow);
                $counterRekonSatu++;
            }
        }
        
        /* Get Data Rekon Unmatch Dua */
        $dataUnmatchDua = array();
        $dataRekonResultDua = $this->rekon_result->getRekonsByIdChannel($idChannel,$tanggal_rekon_awal_dua,$tanggal_rekon_akhir_dua);
        $counterRekonDua = 1;
        foreach($dataRekonResultDua as $key => $row) {
            $id_rekon_header_dua = $row->id_rekon;
            $tempDataDua = $this->rekon_unmatch->getRekonsByIdRekonResult($row->id_rekon_result, 2);
            foreach($tempDataDua as $index => $rowTemp) {
                /* untuk diinsert */
                $drow = array(
                    "row_index" => $counterRekonDua,
                    "data_asli" => "", 
                    "data_row" => $rowTemp->row_data,
                    "tipe" => (string) $rowTemp->tipe,
                    "id_rekon" => $id_rekon,
                );
                array_push($dataUnmatchDua, $drow);
                $counterRekonDua++;
            }
        }

        /* Get and save Data Header */
        $dataHeaderAll = array();

        $dataHeaderOneOld = $this->rekon_buff_detail->getHeader($id_rekon_header_satu, "1");
        $dataHeaderOne = array(
            "row_index" => 0,
            "data_asli" => $dataHeaderOneOld[0]->data_asli, 
            "data_row" => $dataHeaderOneOld[0]->data_row,
            "tipe" => "1",
            "id_rekon" => $id_rekon,
        );
        $dataHeaderTwoOld = $this->rekon_buff_detail->getHeader($id_rekon_header_dua, "2");
        $dataHeaderTwo = array(
            "row_index" => 0,
            "data_asli" => $dataHeaderTwoOld[0]->data_asli, 
            "data_row" => $dataHeaderTwoOld[0]->data_row,
            "tipe" => "2",
            "id_rekon" => $id_rekon,
        );
        array_push($dataHeaderAll, $dataHeaderOne);
        array_push($dataHeaderAll, $dataHeaderTwo);
        $this->rekon_buff_detail->insertRekonManyHeader($dataHeaderAll);
        
        /* Create New Rekon and Save Id to Sessions */
        $dataChannel = $this->channel_model->getChannel($idChannel);
        $this->session->set('id_mitra', $dataChannel->id_mitra);
        $id_mitra = $dataChannel->id_mitra;
        $dataRekon = array(
            'id_rekon' => $id_rekon,
            'nama_rekon' => $namaRekon,
            'kolom_compare' => array(),
            'kolom_sum' => array(),
            'clean_rule' => array(),
            'is_proses' => "",
            'timestamp' => date("Y-m-d H:i:s"),
            'timestamp_complete' => "-",
            'detail_mode' => [],
            'is_schedule' => 0,
            'detail_schedule' => (object) array(
                'time' => ''
            ),
            'id_channel' => $idChannel,
            'id_mitra' => $id_mitra,
            'tanggal_rekon' => $tanggal_rekon,
            'delimiter' => '',
            'is_rekon_unmatch_bulanan' => 1
        );
        $this->rekon_buff->insertRekonUnmatchBulanan($dataRekon);
        log_message('info', 'id_rekon => ' . $id_rekon);

        /* save id_rekon and tipe to session for next process */
        $this->session->set('id_rekon', $id_rekon);
        $this->session->set('tipe', 1);

        /* delete all rekon to detail to prevent doubles , if any*/
        log_message('info', 'DO Remove from DATABASE...');
        $this->rekon_buff_detail->deleteRekonMany($id_rekon, 1);
        log_message('info', 'DONE.. Remove ' .count($dataUnmatchSatu).' from DATABASE...');
        log_message('info', 'DO Remove from DATABASE...');
        $this->rekon_buff_detail->deleteRekonMany($id_rekon, 2);
        log_message('info', 'DONE.. Remove ' .count($dataUnmatchDua).' from DATABASE...');
        
        /* insert all rekon to detail */
        if(count($dataUnmatchSatu) > 0) {
            log_message('info', 'DO Writes Data Unmatch 1 To DATABASE...');
            $this->rekon_buff_detail->insertRekonMany($dataUnmatchSatu);
            log_message('info', 'DONE.. Writes ' .count($dataUnmatchSatu).'  To DATABASE...');
        }
       
        if(count($dataUnmatchDua) > 0) {
            log_message('info', 'DO Writes Data Unmatch 2 To DATABASE...');
            $this->rekon_buff_detail->insertRekonMany($dataUnmatchDua);
            log_message('info', 'DONE.. Writes ' .count($dataUnmatchDua).'  To DATABASE...');
        }       

        $response = array(
            "response_code" => "00", 
            "response_desc" => "sukses",
            "data_unmatch_satu" => count($dataUnmatchSatu),
            "data_unmatch_dua" => count($dataUnmatchDua)
        );

        echo json_encode($response);
    }


    public function cleansing_data() {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = 1;
        $this->session->set('tipe', 1); // set session to tipe 1
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
        $data['title'] = 'Data Rekon Unmatch';
        $data['view'] = 'rekon_unmatch/add_rekon_cleaning';
        $data['data_csv'] = $dataCsvSample;

        return view('dashboard/layout', $data);
    }

    public function cleansing_data_dua() {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = 2;
        $this->session->set('tipe', 2); // set session to tipe 2
        $limit = $this->uri->getSegment(3);
        // var_dump($id_rekon);var_dump($tipe);die;
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
        $data['title'] = 'Data Rekon Unmatch';
        $data['view'] = 'rekon_unmatch/add_rekon_cleaning';
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
            $indexKolom = $this->request->getPost('rowRegexKolomIndex');
            $rule = "regex";
            $ruleVal = $this->request->getPost('rowRegexReplaceOld') . "=>" . $this->request->getPost('rowRegexReplaceNew');
        } else if ($radioSelect == "radioSubstr") {
            $indexKolom = $this->request->getPost('rowSubstrKolomIndex');
            $rule = "substr";
            $ruleVal = $this->request->getPost('rowSubstrStart') . "=>" . $this->request->getPost('rowSubstrEnd');
        } else {
            $this->session->setFlashdata('error', 'Failed to Save! Try Again');
            return redirect()->to(base_url('rekon_unmatch/cleansing_data'));
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
                $newData[$indexKolom] = str_replace( $valFind, $valReplace, $newData[$indexKolom]);
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
            } else if ($ruleOptions == "substr") {
                $ruleValue = explode("=>" ,$dataClean["rule_value"]); // rule values di split dulu
                $position1 = (int) $ruleValue[0];
                $position2 = (int) $ruleValue[1];
                if($position2 !== 0) {
                    $newData[$indexKolom] = substr($newData[$indexKolom], $position1, $position2);
                } else {
                    $newData[$indexKolom] = substr($newData[$indexKolom], $position1);
                }
                
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
        $data['title'] = 'Data Rekon Unmatch';
        $data['view'] = 'rekon_unmatch/add_rekon_cleaning';
        $data['data_csv'] = $sampleDataCsv;

        return view('dashboard/layout', $data);
    }

    public function add_rekon_data_to_compare() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);

        if(!isset($id_rekon) || !isset($tipe)) {
            return $this->data_rekon();
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
        $data['title'] = 'Data Rekon Unmatch';
        $data['view'] = 'rekon_unmatch/add_rekon_data_tocompare';
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
            "tipe" => (string) $tipe,
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

    public function add_rekon_preview() {

        log_message('info', 'Prepare Preview..');
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');

        if(!isset($id_rekon) || !isset($tipe)) {
            return $this->data_rekon();
        }

        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);

        /* get All rekons limit 5 */
        $dataRekon1DB = $this->rekon_buff_detail->getRekons($id_rekon, 1, 5);
        $dataRekon2DB = $this->rekon_buff_detail->getRekons($id_rekon, 2, 5);

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
        $data['view'] = 'rekon_unmatch/add_rekon_data_preview'; 
        $data['data_compare_satu'] = $dataKolomCompareArr; 
        $data['data_compare_satu_db'] = $dataRekon1DB; 
        $data['data_compare_dua'] = $dataKolomCompareArr2; 
        $data['data_compare_dua_db'] = $dataRekon2DB; 
        log_message('info', 'Done Preview..');

        return view('dashboard/layout', $data);
    }

    public function add_kolom_sum() {
        $id_rekon = $this->session->get('id_rekon'); 
        $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $objData = array(
            "kolom_index" => $rekonIndex,
            "kolom_name" => $rekonName,
            "tipe" => (string) $tipe,
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

    public function add_rekon_finish() {
        $id_rekon = $this->session->get('id_rekon'); 
        
        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->to_compare_index == "") {
                $this->session->setFlashdata('error', 'Semua data wajib di compare');
                return redirect()->to(base_url('rekon_unmatch_bulanan/rekon_preview_sum'));
            }
        }
        $this->rekon_buff->updateRekon($id_rekon, ["is_proses" => "pending"]);

        /* pindah ke rekon result */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        unset($dataRekon->_id);
        $dataRekon->detail_result1 = (object) array();
        $dataRekon->detail_result2 = (object)  array();
        $dataRekon->id_rekon_result = rand(1000000,9999999);
        $this->rekon_result->insertRekon($dataRekon);
        
        return redirect()->to(base_url('rekon'));
    }


}