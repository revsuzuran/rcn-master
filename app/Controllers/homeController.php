<?php

namespace App\Controllers;
use App\Models\RekonResult;
use App\Models\MitraModel;
use CodeIgniter\HTTP\Response;

class homeController extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->session = session();
        $this->rekon_result = new RekonResult();
        $this->mitra = new MitraModel();
        $this->uri = $this->request->uri;   
    }

    public function index()
    {
        $idMitra = $this->session->get('id_mitra');
        $rekonResult = $this->rekon_result->getRekonDisburseAll();
        $dataMitra = $this->mitra->getMitraAll();
        $month = date('m'); // Ambil bulan saat ini dalam format numerik
        $year = date('Y'); // Ambil tahun saat ini dalam format numerik

        // Hitung jumlah hari pada bulan ini
        $numDays = date('t', strtotime($year . '-' . $month . '-01'));
        for ($i = 1; $i <= $numDays; $i++) {
            $array1[$i] = 0;
            $array2[$i] = 0;
        }
        $data['idmitra'] = $idMitra;
        $data['dataMitra'] = $dataMitra;
        $data['maxdate'] = $numDays;
        $data['array1'] = $array1;
        $data['array2'] = $array2;
        $data['fromAPI'] = $this->getAPI();
        $data['data_rekon'] = $rekonResult;
        // $data['data_bank'] = $this->bank->getAllBank($idMitra);
        $data['view'] = 'home/homeview';
        $data['title'] = 'Home';
        return view('dashboard/layout', $data);
    }

    public function getAPI(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => getenv('DOMAIN_LINKQU').'/linkqu-partner/akun/resume?username='.getenv('USERNAME_LINKQU'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'client-id: '.getenv('CLIENT_ID_LINKQU'),
            'client-secret:'.getenv('CLIENT_SECRET_LINKQU')
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

}