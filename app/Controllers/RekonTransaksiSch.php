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


class RekonTransaksiSch extends BaseController
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
        $this->transaksi_model = new TransaksiModel();
    }

    public function data_rekon_master()
    {
        $data['title'] = 'Data Rekon Master';
        $data['view'] = 'rekon_transaksi_sch/rekon_master';
        $rekonResult = $this->rekon_buff->getRekonSchAll();
        
        foreach($rekonResult as $index => $row) {
            $dataMitra = $this->mitra->getMitra($row['id_mitra']);
            $dataChannel = $this->channel_model->getChannel($row["id_channel"]);
            $rekonResult[$index]->nama_mitra = $dataMitra->nama_mitra;
            $rekonResult[$index]->nama_channel = $dataChannel->nama_channel;
        }

        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }

    public function data_rekon_sch_temp() {
        $id_rekon = $this->request->getPost('id_rekon');
        $this->session->set('id_rekon', $id_rekon);
    }

    public function data_rekon_sch()
    {
        $id_rekon = $this->session->get('id_rekon');
        $dataRekon = $this->rekon_buff->getRekonSch($id_rekon);
        $data['title'] = 'Data Rekon Schedule';
        $data['view'] = 'rekon_transaksi_sch/data_rekon_sch_new';
        $data['data_rekon'] = $dataRekon;
        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSetting();
        
        $dataChannel = $this->channel_model->getAllChannel();        
        foreach($dataChannel as $key => $row) {
            $dataMitra = $this->mitra->getMitra($row->id_mitra);
            $dataChannel[$key]->nama_mitra = $dataMitra->nama_mitra;
        }
        
        $data['data_channel'] = $dataChannel; 
        return view('dashboard/layout', $data);
    }

    public function update_rekon() {

        /* decrypt internal */
        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo json_encode(array("response_code" => "XX", "response_desc" => "Failed Decrypt"));
            die;
        }

        /* decode and parsing json */
        $decryptedData = json_decode($decryptedData);
        $namaRekon = $decryptedData->nama_rekon;
        $idChannel = $decryptedData->opt_channel;
        $id_rekon = $this->session->get('id_rekon');

        $dataChannel = $this->channel_model->getChannel($idChannel);
        $this->session->set('id_mitra', $dataChannel->id_mitra);
        $id_mitra = $dataChannel->id_mitra;

        $dataRekon = array(
            'nama_rekon' => $namaRekon,
            'detail_schedule' => $decryptedData,
            'id_channel' => $idChannel,
            'id_mitra' => $id_mitra,
        );
        
        $hasilUpdate = $this->rekon_buff->updateRekon($id_rekon, $dataRekon);
        echo "sukses";
    }

    public function add_rekon()
    {
        $data['title'] = 'Add Rekon Schedule';
        $data['view'] = 'rekon_transaksi_sch/add_rekon'; 
        $data['dataFtp'] = $this->data_model->getFtp();
        $data['dataDb'] = $this->data_model->getDatabase();
        $data['data_setting'] = $this->data_model->getSetting();
        $dataChannel = $this->channel_model->getAllChannel();
        
        foreach($dataChannel as $key => $row) {
            $dataMitra = $this->mitra->getMitra($row->id_mitra);
            $dataChannel[$key]->nama_mitra = $dataMitra->nama_mitra;
        }
        
        $data['data_channel'] = $dataChannel;        
        return view('dashboard/layout', $data);
    }

    public function syntax_formater() {

        $dataText = $this->request->getPost('data_text');
        $idChannel = $this->request->getFile('id_channel');
        return $this->do_syntax_formater($dataText, $idChannel);
    }

    public function do_syntax_formater($dataText, $idChannel) {

        $dataChannel = $this->channel_model->getChannel($idChannel);
        $dataMitra = $this->mitra->getMitra($dataChannel->id_mitra);

        // menentukan nilai default jika tidak ditemukan dalam format
        $defaults = array(
            '#D#' => date('d'),
            '#DD#' => date('D'),
            '#DDD#' => date('l'),
            '#M#' => date('m'),
            '#MM#' => date('M'),
            '#MMM#' => date('F'),
            '#YYYY#' => date('Y'),
            '#YY#' => date('y'),
            '#MITRA#' => $dataMitra->nama_mitra,
            '#CHANNEL#' => $dataChannel->nama_channel,
        );
        
        // mengganti semua placeholder dalam format dengan nilai yang sesuai
        foreach($defaults as $placeholder => $value) {
            $dataText = str_replace($placeholder, $value, $dataText);
        }
        
        return $dataText;
    }


    public function save_data_sch() {
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
        log_message('info', '== PROSES SAVE REKON SCH ==');
        $id_rekon = $this->rekon_buff->getNextSequenceRekon(); // get id sequence
        $this->session->set('id_rekon', $id_rekon);

        /* decode and parsing json */
        $decryptedData = json_decode($decryptedData);
        $namaRekon = $decryptedData->nama_rekon;
        $idChannel = $decryptedData->opt_channel;
        $idCollection = $decryptedData->opt_collection; 
        $this->session->set('id_collection', $idCollection);

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
            'detail_schedule' => $decryptedData,
            'id_channel' => $idChannel,
            'id_mitra' => $id_mitra,
            'tanggal_rekon' => 0,
            'delimiter' => '',
            'is_rekon_unmatch_bulanan' => 0,
            'id_collection' => $idCollection,
            'is_rekon_transaksi' => 1
        );

        $this->rekon_buff->insertRekonSch($dataRekon);
        echo "sukses";
    }

    public function process_data_sch() {
        /* decrypt internal */
        $jsonObj = $this->request->getJSON();
        // var_dump($jsonObj->encryptedData);
        $encryptedData = $jsonObj->encryptedData;
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo json_encode(array("response_code" => "XX", "response_desc" => "Failed Decrypt"));
            die;
        }

        /* decode and parsing json */
        $decryptedData = json_decode($decryptedData);
        $id_rekon = $decryptedData->id_rekon;
        $dataRekon = $this->rekon_buff->getRekonSch($id_rekon);
        $dataRekonSchDetail = $dataRekon->detail_schedule;

        $namaRekon = $dataRekonSchDetail->nama_rekon;
        $idChannel = $dataRekonSchDetail->opt_channel;
        $waktuRekon = $dataRekonSchDetail->waktu_rekon;

        /* Delete / Clean Data Clean,Compare,SUM */
        $dataSave = array(
            "kolom_compare" => [],
            "kolom_sum" => [],
            "clean_rule" => [],
        );
        $this->rekon_buff->updateRekon($id_rekon, $dataSave);

        /* Data Rekon Dua */
        log_message('info', 'PROCESS DATA DUA');
        $dataDua = $dataRekonSchDetail->data_dua;
        $IDsettingDua = $dataDua->setting; 
        $dataSettingDua = $this->data_model->getSettingOne($IDsettingDua);
        $updateDataDua = $this->upload_data_sch($dataRekonSchDetail->data_dua, $id_rekon, $idChannel, "2");
        if($updateDataDua != "sukses") {
            return $this->response("XX", $updateDataDua);
        }
        $this->save_delimiter($dataSettingDua->delimiter, $id_rekon, $dataRekonSchDetail->data_dua->tipe, "2");
        $this->auto_cleaning($dataSettingDua->clean_rule, $id_rekon, "2");   
        $this->auto_save_compare($dataSettingDua->kolom_compare, $id_rekon, "2");
        $this->auto_save_sum($dataSettingDua->kolom_sum, $id_rekon, "2");

        /* IF DONE */
        $this->rekon_buff->updateRekon($id_rekon, ["is_proses" => "proses"]);

        /* pindah ke rekon result */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        unset($dataRekon->_id);
        $dataRekon->nama_rekon = $this->do_syntax_formater($dataRekonSchDetail->nama_rekon, $idChannel);
        $dataRekon->detail_result1 = (object) array();
        $dataRekon->detail_result2 = (object)  array();
        $dataRekon->id_rekon_result = rand(1000000,9999999);
        $this->rekon_result->insertRekonResultSch($dataRekon);

        return $this->response("00", "sukses", $dataRekon);

    }

    public function process_data_sch_cek() {
        
        $id_rekon = $this->session->get('id_rekon');
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        $dataRekonSchDetail = $dataRekon->detail_schedule;
        $idChannel = $dataRekonSchDetail->opt_channel;

        /* Delete / Clean Data Clean,Compare,SUM */
        $dataSave = array(
            "kolom_compare" => [],
            "kolom_sum" => [],
            "clean_rule" => [],
        );
        $this->rekon_buff->updateRekon($id_rekon, $dataSave);       

        /* Data Rekon Dua */
        log_message('info', 'PROCESS DATA DUA');
        $dataDua = $dataRekonSchDetail->data_dua;
        $IDsettingDua = $dataDua->setting; 
        $dataSettingDua = $this->data_model->getSettingOne($IDsettingDua);
        $updateDataDua = $this->upload_data_sch($dataRekonSchDetail->data_dua, $id_rekon, $idChannel, "2");
        if($updateDataDua != "sukses") {
            return $this->response("XX", $updateDataDua);
        }
        $this->save_delimiter($dataSettingDua->delimiter, $id_rekon, $dataRekonSchDetail->data_dua->tipe, "2");
        $this->auto_cleaning($dataSettingDua->clean_rule, $id_rekon, "2");   
        $this->auto_save_compare($dataSettingDua->kolom_compare, $id_rekon, "2");
        $this->auto_save_sum($dataSettingDua->kolom_sum, $id_rekon, "2");

        // return $this->response("00", "sukses", $dataRekon);
        $this->session->set('id_rekon', $id_rekon); 
        return redirect()->to(base_url('rekon_transaksi_sch/rekon_preview'));

    }


    public function upload_data_sch($dataRekonSchDetail, $id_rekon, $idChannel, $tipe) {

        $radioTipe = $dataRekonSchDetail->tipe;
        $dataFtp = $this->data_model->getFtp();
        $detailMode = (object) array();
        
        if($radioTipe == "ftp") {
            try {
                $namaFile = $this->do_syntax_formater($dataRekonSchDetail->input, $idChannel);
                $ftpOptionID = $dataRekonSchDetail->koneksi;
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
                        return 'FTP Error! Authentication failed';
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

                if($csv == "") {
                    return "Error : Failed to process file!";
                }
        
                $file = file($csv);   
                // if($radioTipe == "ftp") unlink("$namaFile"); // remove ftp files
                $arrData = array();
                $strDataPreview = "";
                foreach($file as $key => $hehe) {
                    
                    $hehe = utf8_encode($hehe);
                    $drow = array(
                        "data_asli" => $hehe,
                        "data_string" => $hehe,
                        "tipe" => (string) $tipe,
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
                
            } catch (\Throwable $th) {
                return "Error : FTP Error! ". $th;
            }
            
        } else if($radioTipe == "db") {
            try {
                $dbOptionID = $dataRekonSchDetail->koneksi;
                $query = $dataRekonSchDetail->input;
                $detailMode->option_id = $dbOptionID;
                $detailMode->query = $query;
                $detailMode->tipe =  "db";
                $dataDB = $this->data_model->getDatabaseOne($dbOptionID);
                $this->dbModel->initConnection($dataDB->hostname, $dataDB->username, $dataDB->password, $dataDB->database, $dataDB->driver, $dataDB->port);
                $result = $this->dbModel->getData($query);

                /* Langsung Save Data Ke DB tanpa pilih delimiter */
                $dataCsvArr = array();

                 /* Insert Header */
                $dataHeader = array();
                $arrData = array_keys($result[0]);
                $drow = array(
                    "row_index" => 0,
                    "data_asli" => "header",
                    "data_row" => $arrData,
                    "tipe" => (string) $tipe,
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
                        "tipe" =>  (string)  $tipe,
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

            } catch (\Throwable $th) {
                return  "Error : DB Error! $th";
            }
            
        } 
                
        return "sukses";

    }

    public function save_delimiter($delimiter, $id_rekon, $tipeKoneksi, $tipe) {
        
        if($tipeKoneksi == "db") {
            return "";
        }

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
            if($key == 0) {
                array_push($dataHeader, $drow);
                continue;
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
            "is_save_header" => 1
        );

        $this->rekon_buff->updateRekon($id_rekon, $data);

    }

    public function auto_cleaning($clean_rule, $id_rekon, $tipe) {

        // $tipe = $this->session->get('tipe');
        // $id_rekon = $this->session->get('id_rekon');

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
            $this->auto_proses_cleaning($objData, $id_rekon, $tipe);
        }
    }

    public function auto_proses_cleaning($dataClean, $id_rekon, $tipe) {
        // $id_rekon = $this->session->get('id_rekon'); 
        // $tipe = $this->session->get('tipe');

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

    public function auto_save_compare($kolomCompare, $id_rekon, $tipe) {
        // $id_rekon = $this->session->get('id_rekon'); 
        // $tipe = $this->session->get('tipe');
        
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

    public function auto_save_sum($kolomSum, $id_rekon, $tipe) {
        // $id_rekon = $this->session->get('id_rekon'); 
        // $tipe = $this->session->get('tipe');
        // var_dump($kolomSum);
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

    /* Preview rekon and choose compare */
    public function add_rekon_preview() {

        log_message('info', 'Prepare Preview..');
        $id_rekon = $this->session->get('id_rekon');

        /* Get Data Rekon Master */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);


        /* get All rekons limit 5 */
        $dataRekon1DB = $this->transaksi_model->getTransaksiDetailByCollection($dataRekon->id_collection, false, 5);
        $dataRekon2DB = $this->rekon_buff_detail->getRekons($id_rekon, "2", 5);
        /* Ambil setting kolom compare dari salah satu data transaksi */
        // var_dump($dataRekon->id_collection);die; 
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

        $data['title'] = 'Compare Data Rekon Sch';
        $data['view'] = 'rekon_transaksi_sch/add_rekon_data_preview'; 
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
        } else {
            $rule = $this->request->getPost('compareRadioDua'); 
            $ruleValue = $this->request->getPost($rule);
            $kolIndex = $this->request->getPost('kolom_compare_dua');
            $kolName = "KOLOM " . ((int) $kolIndex + 1);
            $toKolIndex = $this->request->getPost('kolom_compare_dua2');
            $toKolName = "KOLOM " . ((int) $toKolIndex + 1);
        }

        if($rule == null) {
            return redirect()->to(base_url('rekon_transaksi_sch/rekon_preview'));
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
        /* Update Setting */
        $dataRekon = $this->rekon_buff->getRekon($id_rekon);
        if($tipe == 1) { /* for data transaksi */
            $this->transaksi_model->deleteKolomCompareByCollection($id_collection, $kolIndex, $kolName);
            $this->transaksi_model->updateTransaksiPushByCollection($id_collection, $dataSave);
        } else if($tipe == "2") {
            $idSetting = $dataRekon->detail_schedule->data_dua->setting;
            $this->data_model->deleteKolomCompare($idSetting, $tipe, $kolIndex, $kolName);
            $this->data_model->updateSettingPush($idSetting, $dataSave);
        }

        $this->rekon_buff->deleteKolomCompare($id_rekon, $tipe, $kolIndex, $kolName);
        $this->rekon_buff->updateRekonPush($id_rekon, $dataSave);

        return redirect()->to(base_url('rekon_transaksi_sch/rekon_preview'));
    }


    public function add_rekon_finish() {
        $id_rekon = $this->session->get('id_rekon');
        $this->rekon_buff->updateRekon($id_rekon, ["is_schedule" => 1]);
        return redirect()->to(base_url('rekon_sch'));
    }


    public function response($rc, $desc, $data = null) {

        if($data !== null) {
            return json_encode(array(
                "response_code" => $rc,
                "response_desc" => $desc,
                "response_data" => $data 
            ));
        }

        return json_encode(array(
            "response_code" => $rc,
            "response_desc" => $desc 
        ));
    }
}