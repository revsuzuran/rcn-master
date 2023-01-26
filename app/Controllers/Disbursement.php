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
use App\Models\ChannelModel;
use App\Libraries\PdfGenerator;
use App\Libraries\Encryption;


class Disbursement extends BaseController
{
    public function __construct()
    {
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

        $this->uri = $this->request->uri;
    }

    function add_disbursement() {

        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo "gagal";
        }

        $dataJson = json_decode($decryptedData);
        foreach($dataJson as $row) {
            $dataUp = array(
                "is_ready_disburse" => 1,
                "is_proses" => "disburse"
            );
            $this->rekon_result->updateRekonResult($row, $dataUp);
        }

        echo "sukses";

    }

    function monitoring_disburse() {
        $data['title'] = 'Monitoring Disbursement';
        $data['view'] = 'dashboard/monit_disburse';
        $rekonResult = $this->rekon_result->getRekonDisburseAll();
        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }
}