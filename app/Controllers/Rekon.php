<?php

namespace App\Controllers;
use App\Models\RekonBuff;
use App\Models\RekonBuffDetail;

class Rekon extends BaseController
{
    public function __construct() {
        //mengisi variable global dengan data
        $this->session = session();
        $this->rekon_buff = new RekonBuff();
        $this->rekon_buff_detail = new RekonBuffDetail();
    }

    public function index()
    {
        return view('upload');
    }

    public function data_rekon_master()
    {
        $this->session->set(array("uname" => "AQIL PRAKOSO"));
        $data['title'] = 'Data Rekon Master';
        $data['view'] = 'dashboard/rekon_master';

        return view('dashboard/layout', $data);
    }

    public function add_rekon_master()
    {
        $this->session->set(array("uname" => "AQIL PRAKOSO"));
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
            $this->rekon_buff->insertRekon($namaRekon, $id_rekon);
            $this->session->set('id_rekon', $id_rekon); // save id_rekon to session untuk nanti (tipe 2)
        } else {
            $id_rekon = $this->session->get('id_rekon');
        }       
        

        /* insert _buff data to Mongo DB  */
        $handle = fopen($csv,"r");
        $arrData = array();
        $strDataPreview = ""; // save data string to preview
        $countPreview = 0;
        while (($row = fgetcsv($handle, 1000)) != FALSE) //get row vales
        {
            $drow = array(
                "data_row" => implode(",",$row),
                "tipe" => $tipe,
                "id_rekon" => $id_rekon,
            );
            array_push($arrData, $drow);

            if($countPreview < 20) {
                $strDataPreview .= implode(",", $row) . "\r\n";
            }
            $countPreview++;

        }

        /* insert all rekon to detail */
        $this->rekon_buff_detail->insertRekonMany($arrData);

        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_delimiter';
        $data['csv_preview'] = $strDataPreview;
        return view('dashboard/layout', $data);
    }

    public function save_delimiter() {
        $delimiter =$this->request->getPost('delimiter');
        $id_rekon = $this->session->get('id_rekon');
        $sampleCsv = $this->request->getPost('sampleCsv');

        /* Split data and save to Array to preview in tables */
        $dataCsvArr = array();
        $arrRow = explode("\r\n", $sampleCsv);
        foreach ($arrRow as $indexRow) {
            $dataDetail = explode($delimiter, $indexRow);
            if ($dataDetail[0] == "") continue; // remove last splitting \r\n
            array_push($dataCsvArr, $dataDetail);
        }
        
        /* Save data delimiter to DB */
        $data = array(
            "delimiter" => $delimiter
        );
        $this->rekon_buff->updateRekon($id_rekon, $data);

        /* Prepare Preview */
        $data['title'] = 'Add New Rekon';
        $data['view'] = 'dashboard/add_rekon_preview';
        $data['data_csv'] = $dataCsvArr;

        return view('dashboard/layout', $data);
    }

    public function save_cleansing() {
        $radioSelect = $this->request->getPost('customRadio');
        $id_rekon = $this->session->get('id_rekon');
        $dataCsvSample = $this->request->getPost('dataCsv');

        if($radioSelect == "radioRowRemove") {
            $indexKolom = 0;
            $rule = "removeRow";
            $ruleVal = $this->request->getPost('rowRemove');
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
            "clean_rule" => array($objData)
        );

        $this->rekon_buff->updateRekon($id_rekon, $dataSave);

        echo json_encode($dataSave) . "==============" . json_encode($dataCsvSample);

        // /* Prepare Preview */
        // $data['title'] = 'Add New Rekon';
        // $data['view'] = 'dashboard/add_rekon_preview';
        // $data['data_csv'] = $dataCsvArr;

        // return view('dashboard/layout', $data);
    }
}
