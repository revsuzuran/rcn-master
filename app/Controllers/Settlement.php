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
        $rekonResult = $this->rekon_result->getRekonSetlement($decryptedData->id_rekon_result);
        
        $dataBank = $this->bank_model->getBank($decryptedData->id_bank);
        $data_rekon_satu = $rekonResult[0]->data_result1;
        $totalFee = (int) $data_rekon_satu->fee_detail->fee1->total + $data_rekon_satu->fee_detail->fee2->total + (int) $data_rekon_satu->fee_detail->fee3->total + (int) $data_rekon_satu->fee_detail->fee4->total  + (int) $data_rekon_satu->fee_detail->fee5->total + $data_rekon_satu->fee_detail->fee_admin->total ;
        $netAmount = (int)$data_rekon_satu->sum_result->total_sum_match - (int) $totalFee;

        $response = $this->sender_inq($dataBank->kode_bank, $dataBank->norek, $netAmount, $decryptedData->id_rekon_result);
        $jsonResponse = json_decode($response);
        
        if(isset($jsonResponse->response_code) && $jsonResponse->response_code == "00") {
            
            echo $response;

        } else {
            
            echo json_encode(array("response_code" => "gagal"));
        }        
        
    }

    public function sender_inq($bankCodeTujuan, $bankRekTujuan, $nominal, $idInq) {

        $curl = curl_init();
        $transaction = [
            "username" => getenv("USERNAME_LINKQU"),
            "pin" => getenv("PIN_LINKQU"),
            "bankcode" => $bankCodeTujuan,
            "accountnumber" => $bankRekTujuan,
            "amount" => $nominal,
            "partner_reff" => $idInq,
            "sendername" => getenv("SENDER_NAME_LINKQU"),
            "category" => "04",
            "customeridentity" => "1234567890123456",
        ];

        // $transaction = [
        //     "username" => getenv("USERNAME_LINKQU"),
        //     "pin" => getenv("PIN_LINKQU"),
        //     "bankcode" => "014",
        //     "accountnumber" => "12454695",
        //     "amount" => 20000,
        //     "partner_reff" => "20211223124530",
        //     "sendername" => "name testing",
        //     "category" => "99",
        //     "customeridentity" => "636483743246"
        // ];

        $headerReq = array(
            "Content-Type:application/json",
            "client-id:".getenv("CLIENT_ID_LINKQU"),
            "client-secret:".getenv("CLIENT_SECRET_LINKQU"),
        );

        // set options
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv("DOMAIN_LINKQU") . getenv("PATH_INQ_LINKQU") ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($transaction),
            CURLOPT_HTTPHEADER => $headerReq,
        ));

        // execute request
        $response = curl_exec($curl);

        // close cURL
        curl_close($curl);

        return $response;

    }

    public function proses_Pay()
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
        $rekonResult = $this->rekon_result->getRekonSetlement($decryptedData->id_rekon_result);
        
        $dataBank = $this->bank_model->getBank($decryptedData->id_bank);
        $data_rekon_satu = $rekonResult[0]->data_result1;
        $totalFee = (int) $data_rekon_satu->fee_detail->fee1->total + $data_rekon_satu->fee_detail->fee2->total + (int) $data_rekon_satu->fee_detail->fee3->total + (int) $data_rekon_satu->fee_detail->fee4->total  + (int) $data_rekon_satu->fee_detail->fee5->total + $data_rekon_satu->fee_detail->fee_admin->total ;
        $netAmount = (int)$data_rekon_satu->sum_result->total_sum_match - (int) $totalFee;
        
        $response = $this->sender_pay($dataBank->kode_bank, $dataBank->norek, $netAmount, $decryptedData->id_rekon_result, $decryptedData->id_inq_reff);
        $jsonResponse = json_decode($response);

        if(isset($jsonResponse->response_code) && $jsonResponse->response_code == "00") {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '00', // set sukses
                "is_proses" => "settlement"
            );

            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
            echo $response;
            

        } else {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '05', // set pending
                "is_proses" => "settlement"
            );

            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
            
            echo json_encode(array("response_code" => "pending"));
        }        
        
    }

    public function sender_pay($bankCodeTujuan, $bankRekTujuan, $nominal, $idInq, $idInqReff)
    {
        $curl = curl_init();

        $transaction = [
            "username" => getenv("USERNAME_LINKQU"),
            "pin" => getenv("PIN_LINKQU"),
            "bankcode" => $bankCodeTujuan,
            "accountnumber" => $bankRekTujuan,
            "amount" => $nominal,
            "partner_reff" => $idInq,
            "inquiry_reff" => $idInqReff,
            "remark" => getenv("REMARK_LINKQU"),
        ];

        // $transaction = [
        //     "username" => getenv("USERNAME_LINKQU"),
        //     "pin" => getenv("PIN_LINKQU"),
        //     "bankcode" => "014",
        //     "accountnumber" => "12454691",
        //     "amount" => 20000,
        //     "partner_reff" => "testingfastpay001",
        //     "inquiry_reff" => "108333",
        //     "remark" => "Syalalala"
        // ];

        $headerReq = array(
            "Content-Type:application/json",
            "client-id:" . getenv("CLIENT_ID_LINKQU"),
            "client-secret:" . getenv("CLIENT_SECRET_LINKQU"),
        );

        // set options
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv("DOMAIN_LINKQU") . getenv("PATH_PAY_LINKQU"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($transaction),
            CURLOPT_HTTPHEADER => $headerReq,
        )
        );

        // execute request
        $response = curl_exec($curl);

        // close cURL
        curl_close($curl);
        return $response;

    }
}