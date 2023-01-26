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
use App\Models\BankModel;
use App\Libraries\PdfGenerator;
use App\Libraries\Encryption;


class Settlement extends BaseController
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
        $this->bank_model = new BankModel();
        $this->dbModel = new DBModel();
        $this->pg = new Postgres();
        $this->channel_model = new ChannelModel();
        $this->pdfGen = new PdfGenerator();

        $this->uri = $this->request->uri;
    }

    public function data_settlement()
    {
        $data['title'] = 'Data Settlement';
        $data['view'] = 'dashboard/data_settlement';
        $rekonResult = $this->rekon_result->getRekonSetlementAll();
        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }

    public function proses_settlement() {
        $idRekonResult = $this->session->get('id_rekon_result');     
        $rekonResult = $this->rekon_result->getRekonSetlement($idRekonResult);
        $dataBank = $this->bank_model->getAllBank($rekonResult[0]->id_mitra);

        $data['title'] = 'Proses Settlement';
        $data['nama_rekon'] = $rekonResult[0]->nama_rekon;
        $data['data_rekon_satu'] = $rekonResult[0]->data_result1;
        $data['data_rekon_dua'] = $rekonResult[0]->data_result2;
        $data['view'] = 'dashboard/proses_settlement';
        $data['data_bank'] = $dataBank;
        $data['data_rekon'] = $rekonResult;
        return view('dashboard/layout', $data);
    }

    public function proses_temp() {
        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo "gagal";
        }

        $this->session->set('id_rekon_result', $decryptedData);
    }

    public function proses_Inq()
    {
        /* decrypt internal */
        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo "gagal";
            die;
        }

        $decryptedData = json_decode($decryptedData);

        $response = $this->sender_inq();
        $jsonResponse = json_decode($response);
        $rekonResult = $this->rekon_result->getRekonSetlement($decryptedData->id_rekon_result);

        $dataUp = array(
            "is_settlement" => 1,
            "is_ready_disburse" => 2,
            "settlement_status" => '05', // set pending
            "is_proses" => "settlement"
        );
        $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        echo "sukses";
        // if($jsonResponse->response_code == "00") {
            
        // } else {

        // }
    }

    public function sender_inq() {
        // // initialize cURL
        // $curl = curl_init();

        // // set options
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "/linkqu-partner/transaction/withdraw/payment",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS =>"{\n\t\"username\" : \"LI307GXIN\",\n\t\"pin\" : \"2K2NPCBBNNTovgB\",\n\t\"bankcode\" : \"008\",\n\t\"accountnumber\" : \"1234566788234\",\n\t\"amount\" : 50000,\n\t\"partner_reff\" : \"54321\",\n\t\"inquiry_reff\" : \"70291\"\n}",
        //     CURLOPT_HTTPHEADER => array(
        //         "Content-Type: application/json",
        //         "client-id: testing",
        //         "client-secret: 123"
        //     ),
        // ));

        // // execute request
        // $response = curl_exec($curl);

        // // close cURL
        // curl_close($curl);

        $response = '{
            "bankcode": "008",
            "accountnumber": "1234566788234",
            "accountname": "Henda Sujiadi",
            "remark": "Syalalalal",
            "serialnumber": "569861617400",
            "amount": 50000,
            "additionalfee": 2500,
            "balance": 790000,
            "time": 1082,
            "dst_app": "",
            "username": "LI307GXIN",
            "pin": "------",
            "status": "SUCCESS",
            "response_code": "00",
            "response_desc": "SUCCESS",
            "partner_reff": "54321",
            "inquiry_reff": 70291,
            "payment_reff": 70292,
            "totalcost": 52500,
            "bankname": "Bank BCA"
          }';

        return $response;

    }
}