<?php

namespace App\Controllers;
use App\Models\RekonBuff;
use App\Models\RekonBuffDetail;
use App\Models\RekonResult;
use App\Models\RekonUnmatch;
use App\Models\RekonMatch;
use App\Models\DataModels;
use App\Models\UserModel;
use App\Models\DBModel;
use App\Models\Postgres;
use App\Models\ChannelModel;
use App\Models\BankModel;
use App\Libraries\PdfGenerator;
use App\Libraries\Encryption;
use App\Models\SettlementModel;


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
        $this->user_model = new UserModel();
        $this->dbModel = new DBModel();
        $this->pg = new Postgres();
        $this->channel_model = new ChannelModel();
        $this->pdfGen = new PdfGenerator();
        $this->settlement_model = new SettlementModel();
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

    public function proses_settlement_choosen() {
        $dataId = $this->request->getPost('id');
        $this->session->set('data_settlement_choosen', $dataId);
        echo "sukses";
    }

    public function proses_settlement() {
        $idRekonResult = $this->session->get('id_rekon_result');     
        $rekonResult = $this->rekon_result->getRekonSetlement($idRekonResult);
        $dataBank = $this->bank_model->getAllBank($rekonResult[0]->id_mitra);

        $data['title'] = 'Proses Settlement';
        $data['nama_rekon'] = $rekonResult[0]->nama_rekon;

        $dataSettlementChoosen = $this->session->get('data_settlement_choosen');
        if($dataSettlementChoosen == "2") {
            $data['data_rekon_settlement'] = $rekonResult[0]->data_result2;
        } else {
            $data['data_rekon_settlement'] = $rekonResult[0]->data_result1;
        }

        
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

    public function proses_cek_split()
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
        $idAmount = $decryptedData->id_amount;
        
        if($idAmount == 1) {
            $data_rekon_amount = $rekonResult[0]->data_result1;
        } else {
            $data_rekon_amount = $rekonResult[0]->data_result2;
        }
        
        $totalFee = (int) $data_rekon_amount->fee_detail->fee1->total + $data_rekon_amount->fee_detail->fee2->total + (int) $data_rekon_amount->fee_detail->fee3->total + (int) $data_rekon_amount->fee_detail->fee4->total  + (int) $data_rekon_amount->fee_detail->fee5->total + $data_rekon_amount->fee_detail->fee_admin->total ;
        $netAmount = (int)$data_rekon_amount->sum_result->total_sum_match - (int) $totalFee;

        // echo json_encode($this->splitNominal($netAmount));

        $dataSplit = $this->splitNominal($netAmount);

        echo count($dataSplit);

        // $dataResp = array();

        // foreach($dataSplit as $index => $row) {
        //     // var_dump($row);die;
        //     $response = $this->sender_inq($dataBank->kode_bank, $dataBank->norek, $row, $decryptedData->id_rekon_result);
        //     $jsonResponse = json_decode($response);
        //     // var_dump($jsonResponse);die;

        //     $dataArr = array(
        //         "data" => $jsonResponse,
        //         "id_rekon_result" => $decryptedData->id_rekon_result,
        //         "id_mitra" => $decryptedData->id_mitra,
        //         "id_bank" => $decryptedData->id_bank
        //     );

        //     array_push($dataResp, $dataArr);

        //     // if(isset($jsonResponse->status) && $jsonResponse->status == "SUCCESS") {
                
        //     //     echo $response;

        //     // } else {
        //     //     $resDesc = isset($jsonResponse->response_desc) ? "EX : " . $jsonResponse->response_desc : "EX : Undefined Error";
        //     //     echo json_encode(array("response_code" => "XX", "response_desc" => $resDesc));
        //     // }    

        // }

        // echo json_encode($dataResp);
    
    }

    public function proses_split()
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
        $idAmount = $decryptedData->id_amount;

        if($idAmount == 1) {
            $data_rekon_amount = $rekonResult[0]->data_result1;
        } else {
            $data_rekon_amount = $rekonResult[0]->data_result2;
        }
        
        $totalFee = (int) $data_rekon_amount->fee_detail->fee1->total + $data_rekon_amount->fee_detail->fee2->total + (int) $data_rekon_amount->fee_detail->fee3->total + (int) $data_rekon_amount->fee_detail->fee4->total  + (int) $data_rekon_amount->fee_detail->fee5->total + $data_rekon_amount->fee_detail->fee_admin->total ;
        $netAmount = (int)$data_rekon_amount->sum_result->total_sum_match - (int) $totalFee;
        $dataDetailDisburst = $this->settlement_model->getAllDisburstDetail($decryptedData->id_rekon_result);
        
        if(count($dataDetailDisburst) > 0) {
            foreach($dataDetailDisburst as $row) {
                if($row->is_payment == 1) {
                    echo json_encode(array("response_code" => "01", "response_desc" => "Disbursment sudah pernah sukses"));die;
                }
            }

            $this->settlement_model->deleteDisbursmentMany($decryptedData->id_rekon_result);
        }


        $dataSplit = $this->splitNominal($netAmount);
        $dataResp = array();
        $totalPay = 0;

        foreach($dataSplit as $index => $row) {
            $response = $this->sender_inq($dataBank->kode_bank, $dataBank->norek, $row, $decryptedData->id_rekon_result . $index);
            $jsonResponse = json_decode($response);
            
            $dataArr = array(
                "data_inquiry" => $jsonResponse,
                "id_rekon_result" => $decryptedData->id_rekon_result,
                "id_mitra" => $decryptedData->id_mitra,
                "id_bank" => $decryptedData->id_bank,
                "is_inquiry" => 1,
                "is_payment" => 0,
                "is_schedule" => 0,
                "is_proses" => 0,
                "data_payment" => (object) array(),
            );

            array_push($dataResp, $dataArr);

            if(isset($jsonResponse->status) && $jsonResponse->status == "SUCCESS") {
                $totalPay = (int) $totalPay +  (int)  $jsonResponse->amount + (int) $jsonResponse->additionalfee;
            } else {
                $resDesc = isset($jsonResponse->response_desc) ? "EX : " . $jsonResponse->response_desc : "EX : Undefined Error";
                echo json_encode(array("response_code" => "XX", "response_desc" => $resDesc));die;
            }

        }
        
        $this->settlement_model->insertDisbursmentMany($dataResp);
        $this->session->set("id_rekon_result", $decryptedData->id_rekon_result);

        $dataUp = array(
            "is_settlement" => 1,
            "is_ready_disburse" => 2,
            "settlement_status" => '05', // set pending
            "is_proses" => "settlement",
            "partner_reff" => $decryptedData->id_rekon_result,
            "inquiry_reff" => 0,
            "data_disbursment" => (object) array(
                "total_amount" => $netAmount,
                "split" => count($dataSplit),
                "total_pay" => $totalPay,
                "total_sukses_pay" => 0,
                "total_gagal_pay" => 0
            )
        );

        $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        $this->settlement_model->insertDisbursmentOrder($rekonResult);
        $this->settlement_model->updateDisbursmentOrder($rekonResult[0]->id_rekon_result, $dataUp);
        echo json_encode(array("response_code" => "00", "response_desc" => "SUKSES"));die;
    
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
        // var_dump($response);die;
        if(isset($jsonResponse->status) && $jsonResponse->status == "SUCCESS") {
            
            echo $response;

        } else {
            $resDesc = isset($jsonResponse->response_desc) ? "EX : " . $jsonResponse->response_desc : "EX : Undefined Error";
            echo json_encode(array("response_code" => "XX", "response_desc" => $resDesc));
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
        // $response = "{
        //     \"bankcode\": \"112\",
        //     \"bankname\": \"BPD DIY\",
        //     \"accountnumber\": \"2615587316289371\",
        //     \"accountname\": \"Henda Sujiadi\",
        //     \"remark\": \"\",
        //     \"serialnumber\": \"\",
        //     \"dst_app\": \"\",
        //     \"amount\": ". $nominal .",
        //     \"additionalfee\": 2500,
        //     \"sendername\": \"LinkQu Rekon\",
        //     \"category\": \"04\",
        //     \"customeridentity\": \"1234567890123456\",
        //     \"signature\": \"\",
        //     \"time\": 430,
        //     \"username\": \"LI307GXIN\",
        //     \"pin\": \"------\",
        //     \"status\": \"SUCCESS\",
        //     \"response_code\": \"00\",
        //     \"response_desc\": \"SUCCESS\",
        //     \"partner_reff\": \"9022004\",
        //     \"inquiry_reff\": 113895
        // }";

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

        if(isset($jsonResponse->status) && $jsonResponse->status == "SUCCESS") {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '00', // set sukses
                "is_proses" => "settlement",
                "partner_reff" => $decryptedData->id_rekon_result,
                "inquiry_reff" => $decryptedData->id_inq_reff
            );

            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
            echo $response;
            

        } else {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '05', // set pending
                "is_proses" => "settlement",
                "partner_reff" => $decryptedData->id_rekon_result,
                "inquiry_reff" => $decryptedData->id_inq_reff
            );

            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
            $resDesc = isset($jsonResponse->response_desc) ? "EX : " . $jsonResponse->response_desc : "EX : Undefined Error";
            echo json_encode(array("response_code" => "XX", "response_desc" => $resDesc));
        }        
        
    }

    public function proses_Payment()
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
        $idDetailDisburst = $decryptedData->id_disbursment_detail;
        $dataDetailDisbursment = $this->settlement_model->getDisburstDetail($idDetailDisburst);
        $rekonResult = $this->rekon_result->getRekonSetlement($dataDetailDisbursment->id_rekon_result);
        $dataBank = $this->bank_model->getBank($dataDetailDisbursment->id_bank);    

        /* Data To Send */
        $nominalInquiry = $dataDetailDisbursment->data_inquiry->amount;
        $nominalFee = $dataDetailDisbursment->data_inquiry->additionalfee;
        $idReffInq = $dataDetailDisbursment->data_inquiry->inquiry_reff;
        $idInq = $dataDetailDisbursment->data_inquiry->partner_reff;

        $response = $this->sender_pay($dataBank->kode_bank, $dataBank->norek, $nominalInquiry, $idInq, $idReffInq);
        $jsonResponse = json_decode($response);
        
        if(isset($jsonResponse->status) && $jsonResponse->status == "SUCCESS") {
            $dataUp = array(
                "is_payment" => 1,
                "data_payment" => $jsonResponse
            );

            $this->settlement_model->updateDisburstDetailOne($idDetailDisburst, $dataUp);

            /* Update Data Rekon */
            $totalPaySukses = $rekonResult[0]->data_disbursment->total_sukses_pay;
            $dataUp = array(
                "data_disbursment" => (object) array(
                    "total_amount" => $rekonResult[0]->data_disbursment->total_amount,
                    "split" => $rekonResult[0]->data_disbursment->split,
                    "total_pay" => $rekonResult[0]->data_disbursment->total_pay,
                    "total_sukses_pay" => (int) $totalPaySukses + (int) $nominalInquiry + (int) $nominalFee,
                    "total_gagal_pay" => 0
                )
            );

            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
            $this->settlement_model->updateDisbursmentOrder($rekonResult[0]->id_rekon_result, $dataUp);
            echo $response;

        } else {
            $dataUp = array(
                "is_payment" => 1,
                "data_payment" => $jsonResponse
            );

            $this->settlement_model->updateDisburstDetailOne($idDetailDisburst, $dataUp);
            $resDesc = isset($jsonResponse->response_desc) ? "EX : " . $jsonResponse->response_desc : "EX : Undefined Error";
            echo json_encode(array("response_code" => "XX", "response_desc" => $resDesc));
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
        // $response = "{
        //     \"bankcode\": \"008\",
        //     \"accountnumber\": \"1234566788234\",
        //     \"accountname\": \"Henda Sujiadi\",
        //     \"remark\": \"Syalalalal\",
        //     \"serialnumber\": \"569861617400\",
        //     \"amount\": ".$nominal.",
        //     \"additionalfee\": 2500,
        //     \"balance\": 790000,
        //     \"time\": 1082,
        //     \"dst_app\": \"\",
        //     \"username\": \"LI307GXIN\",
        //     \"pin\": \"------\",
        //     \"status\": \"SUCCESS\",
        //     \"response_code\": \"00\",
        //     \"response_desc\": \"SUCCESS\",
        //     \"partner_reff\": \"".$idInq."\",
        //     \"inquiry_reff\": ".$idInqReff.",
        //     \"payment_reff\": 70292,
        //     \"totalcost\": 52500,
        //     \"bankname\": \"Bank BCA\"
        //   }";

        // close cURL
        curl_close($curl);
        return $response;

    }

    public function callback() {

        //     "username" : "LI801D8G7",
        //     "transaction_time" : "2020-07-01 21:00:21",
        //     "accountnumber": "7205022111",
        //     "accountname": "RAHARJO",
        //     "serialnumber": "706829105471",
        //     "amount": 50000,
        //     "additionalfee": 2500,
        //     "balance": 99994499,
        //     "status": "SUCCESS",
        //     "partner_reff": "543214",
        //     "payment_reff": 70311,
        //     "totalcost": 52500, 
        //     "bankcode" : "014",
        //     "bankname" : "Bank BCA"

        $jsonObj = $this->request->getJSON();
        $cId = $this->request->header('client-id');
        $cSecret = $this->request->header('client-secret');
        $cId = str_replace("client-id: ", "", strtolower($cId));
        $cSecret = str_replace("client-secret: ", "", strtolower($cSecret));
        
        if($cId == "" || $cSecret == "") {
            return json_encode(array("response" => "OK"));
        }

        if(!isset($jsonObj->username) && $jsonObj->username != getenv("USERNAME_LINKQU")) {
            return json_encode(array("response" => "OK"));
        }
        
        $rekonResult = $this->rekon_result->getRekonSetlement($jsonObj->partner_reff);
        if(!isset($rekonResult[0]->id_rekon_result)) {
            return json_encode(array("response" => "OK"));
        }
        
        if($jsonObj->status == "SUCCESS") {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '00', // set sukses
                "settlement_desc" => 'SUCCESS', // set pending
                "is_proses" => "settlement",
                "response_callback" => json_encode($jsonObj)
            );
    
            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        } else if($jsonObj->status == "PENDING") {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => '05', // set pending
                "settlement_desc" => 'PENDING', // set pending
                "is_proses" => "settlement",
                "response_callback" => json_encode($jsonObj)
            );
    
            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        } else {
            $dataUp = array(
                "is_settlement" => 1,
                "is_ready_disburse" => 2,
                "settlement_status" => 'XX', // set gagal
                "settlement_desc" => 'FAILED', // set pending
                "is_proses" => "settlement",
                "response_callback" => json_encode($jsonObj)
            );
    
            $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        }


        return array("response" => "OK");

    }

    public function manual_action() {

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

        $unamePic = $this->session->get("uname_admin");

        $dataUp = array(
            "is_settlement" => 1,
            "is_ready_disburse" => 2,
            "settlement_status" => '01', // set gagal
            "settlement_desc" => "SUCCESS MANUAL BY $unamePic", // set pending
            "is_proses" => "settlement",
            "response_callback" => ""
        );
        $this->rekon_result->updateRekonResult($rekonResult[0]->id_rekon_result, $dataUp);
        echo json_encode(array("response_code" => "00", "response_desc" => "SUKSES"));
    }

    function splitNominal($nominal) {
        $limitNominal = 50000000;
        $chunks = [];
        if ($nominal > $limitNominal) {
            while ($nominal > 0) {
                $chunk = ($nominal >= $limitNominal) ? $limitNominal : $nominal;
                array_push($chunks, $chunk);
                $nominal -= $chunk;
            }
        } else {
            array_push($chunks, $nominal);
        }
        
        return $chunks;
    }

    public function detail_disbursment_temp() {
        $encryptedData = $this->request->getPost('encryptedData');
        $key = getenv('encryption_key');
        $Encryption = new Encryption();
        $decryptedData = $Encryption->decrypt($encryptedData, $key);
        if ($decryptedData === false) {
            echo "gagal";
        }

        $this->session->set('id_rekon_result', $decryptedData);
    }
    public function detail_disbursment() {
        $idRekonResult = $this->session->get("id_rekon_result");
        $data['data_detail'] =$this->settlement_model->getAllDisburstDetail($idRekonResult);

        $data['title'] = 'Data Settlement';
        $data['view'] = 'disbursment/detail_disbustment';
        return view('dashboard/layout', $data);
    }
}