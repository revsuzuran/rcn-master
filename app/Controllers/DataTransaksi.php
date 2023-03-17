<?php

namespace App\Controllers;
use App\Models\MitraModel;
use App\Models\RekonBuff;
use App\Models\TransaksiModel;
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


class DataTransaksi extends BaseController
{

    protected $request;
    protected $session;
    protected $rekon_buff;
    protected $transaksi_model;
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

    public function data_master() {
        $data['title'] = 'Data Transaksi';
        $data['view'] = 'data_transaksi/data_master';
        $trxResult = $this->transaksi_model->getTransaksiAll();
        
        foreach($trxResult as $index => $row) {
            $dataMitra = $this->mitra->getMitra($row['id_mitra']);
            $dataCollection = $this->transaksi_model->getCollectionById($row['id_collection']);
            $dataTransaksiFound = $this->transaksi_model->getTransaksiDetailFound($row['id_transaksi']);
            $dataTransaksiNotFound = $this->transaksi_model->getTransaksiDetailNotFound($row['id_transaksi']);
            $trxResult[$index]->nama_mitra = $dataMitra->nama_mitra;
            $trxResult[$index]->nama_collection = $dataCollection->nama_collection;
            $trxResult[$index]->data_found =  count($dataTransaksiFound);
            $trxResult[$index]->data_not_found =  count($dataTransaksiNotFound);
        }

        $data['data_transaksi'] = $trxResult;
        return view('dashboard/layout', $data);
    }
    public function add_transaksi()
    {
        $data['title'] = 'Add New Data Transaksi';
        $data['view'] = 'data_transaksi/add_transaksi'; 
        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSettingTransaksi();
        $data['data_mitra'] = $this->mitra->getMitraAll();    
        return view('dashboard/layout', $data);
    }

    public function get_collection_view() {
        $idMitra = $this->request->getPost('id_mitra');
        $dataCollection =$this->transaksi_model->getCollection($idMitra);

        $output = "'<option value='-'>-</option>'";
        foreach($dataCollection as $row) {
            $output .= '<option value="'.$row->_id->__toString().'">'.$row->nama_collection.'</option>';
        }

        echo $output;
    }

    public function save_collection_view() {
        $idMitra = $this->request->getPost('id_mitra');
        $namaCollection = $this->request->getPost('nama_collection');

        $data = array(
            "id_mitra" => (int) $idMitra,
            "nama_collection" => $namaCollection
        );
        $this->transaksi_model->insertCollection($data);
        $dataCollection =$this->transaksi_model->getCollection($idMitra);

        $output = "";
        foreach($dataCollection as $row) {
            $output .= '<option value="'.$row->_id->__toString().'">'.$row->nama_collection.'</option>';
        }

        echo $output;
    }


    public function upload_data_transaksi() {
        $namaTransaksi =$this->request->getPost('namaTransaksi');
        $radioTipe = $this->request->getPost('radioUpload');
        $csv = $this->request->getFile('csvFile');
        $tanggal_transaksi = $this->request->getPost('tanggal_transaksi');
        $id_mitra = $this->request->getPost('opt_mitra');
        $id_collection = $this->request->getPost('opt_collection');

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
                        return redirect()->to(base_url('data_transaksi/add'));
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
                return redirect()->to(base_url('data_transaksi/add'));
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
                return redirect()->to(base_url('data_transaksi/add'));
            }
            
        } else {
            $csv = $this->request->getFile('csvFile');
        }


        /* Create New Transaksi */
        $id_transaksi = $this->transaksi_model->getNextSequenceRekon(); // get id sequence
        $dataTrx = array(
            'id_transaksi' => (int) $id_transaksi,
            'id_mitra' => (int) $id_mitra,
            'kolom_compare' => array(),
            'kolom_sum' => array(),
            'clean_rule' => array(),
            'timestamp' => date("Y-m-d H:i:s"),
            'nama_transaksi' => $namaTransaksi,
            'tanggal_transaksi' => $tanggal_transaksi,
            'delimiter' => '',
            'id_collection' => $id_collection
        );

        $this->transaksi_model->insertTransaksi($dataTrx);
        $this->session->set('id_transaksi', $id_transaksi); // save id_transaksi to session untuk nanti

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
                "tipe" => "",
                "id_transaksi" => $id_transaksi,
            );
            array_push($dataHeader, $drow);

            /* insert header */
            $this->transaksi_model->deleteTransaksiManyHeader($id_transaksi);
            $this->transaksi_model->insertTransaksiManyHeader($dataHeader);

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
                    "tipe" => "",
                    "id_transaksi" => $id_transaksi,
                    "is_found" => false,
                    "tanggal_transaksi" => $tanggal_transaksi,
                    "id_collection" => $id_collection
                );
                array_push($dataCsvArr, $drow);
            }

            /* delete all rekon to detail */
            log_message('info', 'DO Remove from DATABASE...');
            $this->transaksi_model->deleteTransaksiDetailMany($id_transaksi);
            log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
            
            /* insert all rekon to detail */
            log_message('info', 'DO Writes To DATABASE...');
            $this->transaksi_model->insertTransaksiDetailMany($dataCsvArr);
            log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');
            
            // echo json_encode($dataCsvArr);die();
            return redirect()->to(base_url('data_transaksi/cleansing_data'));

        }

        if($csv == "") {
            $this->session->setFlashdata('error', 'Failed to process file!');
            return redirect()->to(base_url('data_transaksi/add'));
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
                "tipe" => "",
                "id_transaksi" => $id_transaksi,
                "is_found" => false,
                "tanggal_transaksi" => $tanggal_transaksi,
                "id_collection" => $id_collection
            );
            array_push($arrData, $drow);
            if($key < 20) {
                $strDataPreview .= $hehe . "\r\n";
            }
        }

        $this->transaksi_model->deleteTransaksiDetailMany($id_transaksi);
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->transaksi_model->insertTransaksiDetailMany($arrData);
        log_message('info', 'DONE.. Writes To DATABASE...');

        return redirect()->to(base_url('data_transaksi/delimiter'));       
    }

    public function add_transaksi_delimiter() {
        $id_transaksi = $this->session->get('id_transaksi');
        $dataTransaksi = $this->transaksi_model->getTransaksiDetail($id_transaksi);
        $strDataPreview = "";
        foreach($dataTransaksi as $key => $row) {
            if($key < 20) {
                $strDataPreview .= $row->data_asli . "\r\n";
            }
        }        

        $data['title'] = 'Add New Transaksi';
        $data['view'] = 'data_transaksi/add_transaksi_delimiter';
        $data['total_lines'] = count($dataTransaksi);
        $data['csv_preview'] = $strDataPreview;
        return view('dashboard/layout', $data);
    }

    public function save_delimiter() {
        $delimiter =$this->request->getPost('delimiter');
        $id_transaksi = $this->session->get('id_transaksi');
        

        $saveHeader = 1;
        
        $sampleCsv = $this->transaksi_model->getTransaksiDetail($id_transaksi);
        
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
                "id_transaksi" => $valueRow->id_transaksi,
                "is_found" => false,
                "tanggal_transaksi" => $valueRow->tanggal_transaksi,
                "id_collection" => $valueRow->id_collection
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
        
        /* delete all Transaksi to detail */
        log_message('info', 'DO Remove from DATABASE...');
        $this->transaksi_model->deleteTransaksiDetailMany($id_transaksi);
        log_message('info', 'DONE.. Remove ' .count($dataCsvArr).' from DATABASE...');
        
        /* insert all Transaksi to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->transaksi_model->insertTransaksiDetailMany($dataCsvArr);
        log_message('info', 'DONE.. Writes ' .count($dataCsvArr).'  To DATABASE...');

        /* insert header */
        $this->transaksi_model->deleteTransaksiManyHeader($id_transaksi);
        $this->transaksi_model->insertTransaksiManyHeader($dataHeader);

        /* Save data delimiter to DB */
        $data = array(
            "delimiter" => $delimiter,
            "is_save_header" => $saveHeader
        );

        $this->transaksi_model->updateTransaksi($id_transaksi, $data);

        return redirect()->to(base_url('data_transaksi/cleansing_data'));
    }

    public function cleansing_data() {
        $id_transaksi = $this->session->get('id_transaksi');
        $limit = $this->uri->getSegment(3);
        
        if($limit == "all") {
            $sampleCsv = $this->transaksi_model->getTransaksiDetail($id_transaksi, 0);
        } else {
            $sampleCsv = $this->transaksi_model->getTransaksiDetail($id_transaksi, 20);
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
        $data['title'] = 'Add New Transaksi';
        $data['view'] = 'data_transaksi/add_transaksi_cleaning';
        $data['data_csv'] = $dataCsvSample;

        return view('dashboard/layout', $data);
    }

    public function save_cleansing() {
        $radioSelect = $this->request->getPost('customRadio');
        $id_transaksi = $this->session->get('id_transaksi'); 

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
            return redirect()->to(base_url('data_transaksi/cleansing_data'));
        }

        $objData = array(
            "index_kolom" => $indexKolom-1,
            "rule" => $rule,
            "rule_value" => $ruleVal,
            "tipe" => ""
        );

        $dataSave = array(
            "clean_rule" => $objData
        );

        $this->transaksi_model->updateTransaksiPush($id_transaksi, $dataSave);
        return $this->generate_clean_data($objData);        
    }

    public function generate_clean_data($dataClean) {
        $id_transaksi = $this->session->get('id_transaksi'); 
        
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
        $dataDb = $this->transaksi_model->getTransaksiDetail($id_transaksi, 0);
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
                "id_transaksi" => $rowDB->id_transaksi,
                "is_found" => false,
                "tanggal_transaksi" => $rowDB->tanggal_transaksi,
                "id_collection" => $rowDB->id_collection
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
        $this->transaksi_model->deleteTransaksiDetailMany($id_transaksi);
        log_message('info', 'DONE.. Remove ' .count($newDataCsv).' from DATABASE...');
        
        /* insert all rekon to detail */
        log_message('info', 'DO Writes To DATABASE...');
        $this->transaksi_model->insertTransaksiDetailMany($newDataCsv);
        log_message('info', 'DONE.. Writes ' .count($newDataCsv).'  To DATABASE...');
        
        /* Prepare Preview */
        $data['title'] = 'Add New Transaksi';
        $data['view'] = 'data_transaksi/add_transaksi_cleaning';
        $data['data_csv'] = $sampleDataCsv;

        return view('dashboard/layout', $data);
    }

    public function add_transaksi_data_to_compare() {
        $id_transaksi = $this->session->get('id_transaksi'); 
        
        // if(!isset($id_rekon) || !isset($tipe)) {
        //     return $this->data_rekon_master();
        // }

        $sampleCsv = $this->transaksi_model->getTransaksiDetail($id_transaksi, 20);
        $dataTransaksi = $this->transaksi_model->getTransaksiOne($id_transaksi);

        /* Get Data Kolom Compare DB */
        $dataKolomDB = array();
        foreach($dataTransaksi->kolom_compare as $dataRow) {
            array_push($dataKolomDB, $dataRow);
        }

        /* Get Data Kolom SUM DB */
        $dataKolomSumDB = array();
        foreach($dataTransaksi->kolom_sum as $dataRow) {
            array_push($dataKolomSumDB, $dataRow);
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
        foreach($dataTransaksi->kolom_compare as $row) {
            array_push($kolomCompare, $row);
        }

        $kolomSum = array();
        foreach($dataTransaksi->kolom_sum as $row) {
            array_push($kolomSum, $row);
        }

        $cleanRule = array();
        foreach($dataTransaksi->clean_rule as $row) {
            array_push($cleanRule, $row);
        }

        $dataSetting = array(
            "kolom_compare" => $kolomCompare,
            "kolom_sum" => $kolomSum,
            "delimiter" => $dataTransaksi->delimiter,
            "clean_rule" => $cleanRule

        );
        

        /* Prepare Preview */
        $data['nama_transaksi'] = $dataTransaksi->nama_transaksi;
        $data['title'] = 'Add New Transaksi';
        $data['view'] = 'data_transaksi/add_transaksi_data_tocompare';
        $data['data_csv'] = $sampleCsv;
        $data['data_kolom'] = $dataKolomArr;
        $data['data_kolom_db'] = $dataKolomDB;
        $data['data_kolom_sum'] = $dataKolomSumArr;
        $data['data_kolom_sum_db'] = $dataKolomSumDB;
        $data['data_setting'] = $dataSetting;
        
        return view('dashboard/layout', $data);
    }


    public function add_kolom_compare() {
        $id_transaksi = $this->session->get('id_transaksi'); 
        
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $objData = array(
            "kolom_index" => $rekonIndex,
            "kolom_name" => $rekonName,
            "tipe" => "",
            "rule" => "equal",
            "rule_value" => "",
            "to_compare_index" => "",
            "to_compare_name" => "null",
        );

        $dataSave = array(
            "kolom_compare" => $objData
        );

        $this->transaksi_model->updateTransaksiPush($id_transaksi, $dataSave);

        return "sukses";
    }

    public function rm_kolom_compare() {
        $id_rekon = $this->session->get('id_transaksi'); 
        // $tipe = $this->session->get('tipe');
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $this->transaksi_model->deleteKolomCompare($id_rekon, $rekonIndex, $rekonName);

        return "sukses";
    }

    public function add_kolom_sum() {
        $id_transaksi = $this->session->get('id_transaksi'); 
        $rekonIndex = $this->request->getPost('rekon_index'); 
        $rekonName = $this->request->getPost('rekon_name');

        $objData = array(
            "kolom_index" => $rekonIndex,
            "kolom_name" => $rekonName,
            "tipe" => "",
            "rule" => "equal",
            "rule_value" => "",
            "to_compare_index" => "",
            "to_compare_name" => "",
            "total" => 0
        );

        $dataSave = array(
            "kolom_sum" => $objData
        );

        $this->transaksi_model->updateTransaksiPush($id_transaksi, $dataSave);

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

    public function save_transaksi() {

    }

}