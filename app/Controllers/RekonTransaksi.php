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
use App\Models\TransaksiModel;

class RekonTransaksi extends BaseController
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
    protected $transaksi_model;

    public function __construct() {
        //mengisi variable global dengan data
        $this->request = \Config\Services::request(); 
        $this->session = session();
        $this->rekon_buff = new RekonBuff();
        $this->transaksi_model = new TransaksiModel();
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

    public function data_rekon_master()
    {
        $data['title'] = 'Data Rekon Master';
        $data['view'] = 'rekon_transaksi/rekon_master';
        $rekonResult = $this->rekon_result->getRekons();
        
        foreach($rekonResult as $index => $row) {
            $dataMitra = $this->mitra->getMitra($row['id_mitra']);
            $rekonResult[$index]->nama_mitra = $dataMitra->nama_mitra;
        }

        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }

    public function add_rekon_master()
    {
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'rekon_transaksi/add_rekon'; 
        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSettingTransaksi();
        $dataChannel = $this->channel_model->getAllChannel();
        
        foreach($dataChannel as $key => $row) {
            $dataMitra = $this->mitra->getMitra($row->id_mitra);
            $dataChannel[$key]->nama_mitra = $dataMitra->nama_mitra;
        }
        
        $data['data_channel'] = $dataChannel;        
        return view('dashboard/layout', $data);
    }

    public function get_collection_view() {
        $idChannel = $this->request->getPost('id_channel');
        $dataChannel = $this->channel_model->getChannel($idChannel);
        $idMitra = $dataChannel->id_mitra;
        $dataCollection =$this->transaksi_model->getCollection($idMitra);

        $output = "'<option value='-'>-</option>'";
        foreach($dataCollection as $row) {
            $output .= '<option value="'.$row->_id->__toString().'">'.$row->nama_collection.'</option>';
        }

        echo $output;
    }

    public function upload_data_rekon() {
        $namaRekon =$this->request->getPost('namaRekon');
        $tipe = "2";
        $radioTipe = $this->request->getPost('radioUpload');
        $csv = $this->request->getFile('csvFile');
        $schPost = $this->request->getPost('is_schedule');
        $idChannel = $this->request->getPost('opt_channel');
        $idCollection = $this->request->getPost('opt_collection');
        $tanggal_rekon = $this->request->getPost('tanggal_rekon');
        
        if($idCollection == "-") {
            $this->session->setFlashdata('error', 'Failed! Collection belum dipilih');
                return redirect()->to(base_url('rekon_transaksi/add'));
        }

        $isSch = 0;
        $timeSch = 0;
        if($schPost == "on") {
            $timeSch = $this->request->getPost('waktuRekon');
            $isSch = 1;

            if($radioTipe != "ftp" && $radioTipe != "db") {
                $this->session->setFlashdata('error', 'Failed! Penjadwalan rekon hanya untuk opsi FTP dan DB');
                return redirect()->to(base_url('rekon_transaksi/add'));
            }

        }

        $dataFtp = $this->data_model->getFtp();
        $detailMode = (object) array();
        
        if($radioTipe == "ftp") {
            try {
                $namaFile = $this->request->getPost('nama_file') ;
                $ftpOptionID = $this->request->getPost('ftp_option') ;
                $detailMode->nama_file = $namaFile;
                $detailMode->option_id = $ftpOptionID;
                $detailMode->tipe =  "ftp";
                $dataFtp = $this->data_model->getFtpOne($ftpOptionID);
                
                if(isset($dataFtp->tipe_ftp) && $dataFtp->tipe_ftp  == "sftp") {
                    $radioTipe = "sftp"; //change mode
                    /* SFTP */
                    $portFtp = isset($dataFtp->port) ? $dataFtp->port : 22;
                    $connection = ssh2_connect($dataFtp->domain, $portFtp);
                    
                    $usernameFtp = $dataFtp->username;
                    $passwordFtp = $dataFtp->password;
                    if (ssh2_auth_password($connection,  $usernameFtp, $passwordFtp)) {
                        $pathFile = $dataFtp->path;
                        $file = "$pathFile$namaFile";
                        ssh2_scp_recv($connection, $file, $namaFile);
                        $csv = $namaFile;
                    } else {
                        $this->session->setFlashdata('error', 'FTP Error! Authentication failed');
                        return redirect()->to(base_url('rekon_transaksi/add'));
                    }

                } else {
                    $pathFile = $dataFtp->path;
                    $source = "$pathFile$namaFile";
                    $portFtp = isset($dataFtp->port) ? $dataFtp->port : 21;
                    $target = fopen($source, "r+");
                    $conn = ftp_connect($dataFtp->domain, $portFtp) or die("Could not connect");
                    ftp_login($conn,$dataFtp->username,$dataFtp->password);
                    ftp_fget($conn,$target,$source,FTP_ASCII);
                    $csv = $source;
                }
                
            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', 'FTP Error! Failed to get file or file not found');
                redirect()->to(base_url('rekon_transaksi/add'));
            }
            
        } else if($radioTipe == "db") {
            try {
                $namaFile = $this->request->getPost('nama_file');
                $dbOptionID = $this->request->getPost('db_option');
                $query = $this->request->getPost('query');
                $detailMode->nama_file = $namaFile;
                $detailMode->option_id = $dbOptionID;
                $detailMode->query = $query;
                $detailMode->tipe =  "db";
                $dataDB = $this->data_model->getDatabaseOne($dbOptionID);
                $this->dbModel->initConnection($dataDB->hostname, $dataDB->username, $dataDB->password, $dataDB->database, $dataDB->driver, $dataDB->port);
                $result = $this->dbModel->getData($query);

            } catch (\Throwable $th) {
                $this->session->setFlashdata('error', "DB Error! $th");
                return redirect()->to(base_url('rekon_transaksi/add'));
            }
            
        } else {
            $csv = $this->request->getFile('csvFile');
        }

        
        /* Create New Rekon and Save Id to Sessions */
        $dataChannel = $this->channel_model->getChannel($idChannel);
        $this->session->set('id_mitra', $dataChannel->id_mitra);
        $id_mitra = $dataChannel->id_mitra;
        $id_rekon = $this->rekon_buff->getNextSequenceRekon(); // get id sequence
        $timestamp = date("Y-m-d h:i:sa");

        $dataRekon = array(
            'id_rekon' => $id_rekon,
            'nama_rekon' => $namaRekon,
            'kolom_compare' => array(),
            'kolom_sum' => array(),
            'clean_rule' => array(),
            'is_proses' => "",
            'timestamp' => date("Y-m-d H:i:s"),
            'timestamp_complete' => "-",
            'detail_mode' => $detailMode,
            'is_schedule' => $isSch,
            'detail_schedule' => (object) array(
                'time' => $timeSch
            ),
            'id_channel' => $idChannel,
            'id_mitra' => $id_mitra,
            'tanggal_rekon' => $tanggal_rekon,
            'delimiter' => '',
            'is_rekon_transaksi' => 1,
            'id_collection' => $idCollection
        );

        $this->rekon_buff->insertRekonTransaksi($dataRekon);
        $this->session->set('id_rekon', $id_rekon);
        $this->session->set('id_collection', $idCollection);
        
        /* Save Tipe */
        $this->session->set('tipe', $tipe);

        if($radioTipe == "db") {
            /* Langsung Save Data Ke DB tanpa pilih delimiter */
            $dataCsvArr = array();

            /* Insert Header */
            $dataHeader = array();
            $arrData = array_keys($result[0]);
            $drow = array(
                "row_index" => 0,
                "data_asli" => "header",
                "data_row" => $arrData,
                "tipe" => $tipe,
                "id_rekon" => $id_rekon,
            );
            array_push($dataHeader, $drow);

            /* insert header */
            $this->rekon_buff_detail->deleteRekonManyHeader($id_rekon, $tipe);
            $this->rekon_buff_detail->insertRekonManyHeader($dataHeader);

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
            return redirect()->to(base_url('rekon_transaksi/cleansing_data'));

        }

        if($csv == "") {
            $this->session->setFlashdata('error', 'Failed to process file!');
            return redirect()->to(base_url('rekon_transaksi/add'));
        }

        $file = file($csv);       
        // if($radioTipe == "ftp") unlink($namaFile); // remove ftp files
        $arrData = array();
        $strDataPreview = "";
        foreach($file as $key => $hehe) {
            $hehe = utf8_encode($hehe);
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

        return redirect()->to(base_url('rekon_transaksi/delimiter'));

       
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
        $data['view'] = 'rekon_transaksi/add_rekon_delimiter';
        $data['total_lines'] = count($dataRekon);
        $data['csv_preview'] = $strDataPreview;
        return view('dashboard/layout', $data);
    }

    public function save_delimiter() {
        $delimiter =$this->request->getPost('delimiter');
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');

        $saveHeader = 1;        
        $sampleCsv = $this->rekon_buff_detail->getRekons($id_rekon, $tipe, 0);
        
        /* Split data and save to Array to preview in tables */
        $dataCsvArr = array();
        $dataHeader = array();
        $dataCsvSample = array();
        $no_index = 0;
        foreach ($sampleCsv as $key => $valueRow) {

            $dataObj = str_getcsv($valueRow->data_asli, $delimiter);

            /* untuk diinsert ulang */
            $drow = array(
                "row_index" => $no_index,
                "data_asli" => $valueRow->data_asli,
                "data_row" => $dataObj,
                "tipe" => $valueRow->tipe,
                "id_rekon" => $valueRow->id_rekon,
            );

            /* Simpan data header */
            if($saveHeader == 1) {
                if($key == 0) {
                    array_push($dataHeader, $drow);
                    continue;
                }
            }

            array_push($dataCsvArr, $drow);
            
            /* untuk preview sample */
            if($key < 20) {
                array_push($dataCsvSample, $drow);
            }

            $no_index++;
            
        }
        
        /* delete all rekon to detail */
        log_message('info', 'DO Remove from DATABASE...');
        $this->rekon_buff_detail->deleteRekonMany($id_rekon, $tipe);
        log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
        
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->rekon_buff_detail->insertRekonMany($dataCsvArr);
        log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');

        /* insert header */
        $this->rekon_buff_detail->deleteRekonManyHeader($id_rekon, $tipe);
        $this->rekon_buff_detail->insertRekonManyHeader($dataHeader);

        /* Save data delimiter to DB */
        $data = array(
            "delimiter" => $delimiter,
            "is_save_header" => $saveHeader
        );

        $this->rekon_buff->updateRekon($id_rekon, $data);

        return redirect()->to(base_url('rekon_transaksi/cleansing_data'));
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
        $data['view'] = 'rekon_transaksi/add_rekon_cleaning';
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
            return redirect()->to(base_url('rekon_transaksi/cleansing_data'));
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
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'rekon_transaksi/add_rekon_cleaning';
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
        $data['view'] = 'rekon_transaksi/add_rekon_data_tocompare';
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
        $dataRekon1DB = $this->transaksi_model->getTransaksiDetailByCollection($dataRekon->id_collection, false, 5);
        $dataRekon2DB = $this->rekon_buff_detail->getRekons($id_rekon, "2", 5);
        // var_dump($dataRekon1DB[0]->id_transaksi);die;

        /* Ambil setting kolom compare dari salah satu data transaksi */
        $dataTransaksi = $this->transaksi_model->getTransaksiOne($dataRekon1DB[0]->id_transaksi);
        /* Get Data Index Compare DB */
        $dataIndexCompare1DB = array();
        foreach($dataTransaksi->kolom_compare as $dataRow) {
            array_push($dataIndexCompare1DB, $dataRow);
        }
        $dataIndexCompare2DB = array();
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->tipe == "2") array_push($dataIndexCompare2DB, $dataRow);
        }

        /* Get Data Index SUM DB */
        $dataKolomSumDB = array();
        foreach($dataTransaksi->kolom_sum as $dataRow) {
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
        $data['view'] = 'rekon_transaksi/add_rekon_data_preview'; 
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
        $id_collection = $this->session->get('id_collection'); 

        /* for data transaksi */
        if($tipe == 1) {
            $rule = $this->request->getPost('compareRadioSatu'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_satu');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_satu2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);

        /* for data comparing */
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


        if($tipe == 1) { /* for data transaksi */
            $this->transaksi_model->deleteKolomCompareByCollection($id_collection, $kolIndex, $kolName);
            $this->transaksi_model->updateTransaksiPushByCollection($id_collection, $dataSave);
        } else { /* for data comparing */
            $this->rekon_buff->deleteKolomCompare($id_rekon, $tipe, $kolIndex, $kolName);
            $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);
        }       

        return $this->add_rekon_preview();
    }

    public function add_rekon_finish() {
        $id_rekon = $this->session->get('id_rekon'); 


        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        foreach($dataRekon->kolom_compare as $dataRow) {
            if($dataRow->to_compare_index == "") {
                $this->session->setFlashdata('error', 'Semua data wajib di compare');
                return redirect()->to(base_url('rekon_transaksi/rekon_preview'));
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