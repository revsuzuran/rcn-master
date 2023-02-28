<?php

namespace App\Controllers;


class MutasiSaldoController extends BaseController
{
    public function __construct()
    {
        //mengisi variable global dengan data
        $this->request = \Config\Services::request();
        $this->session = session();

        $this->uri = $this->request->uri;
    }

    function index() {
        $data['title'] = 'Mutasi Saldo';
        $data['view'] = 'mutasiSaldo/mutasiSaldoView';
        return view('dashboard/layout', $data);
    }

    public function getDataAPI() {
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $offset = strval($this->request->getPost('offset'));
        $limit = strval($this->request->getPost('limit'));
        $id_transaksi = $this->request->getPost('id_transaksi');
        $partner = $this->request->getPost('partner');

        if($start_date == null || $end_date == null){
            $awal_bulan_ini = date('Y-m-01');
            $akhir_bulan_ini = date('Y-m-t');
            $start_date = $awal_bulan_ini;
            $end_date = $akhir_bulan_ini;
        }

        $tambahan = '';
        if($id_transaksi != '' && $id_transaksi != null){
            $tambahan = '&id_transaksi='.strval($id_transaksi);
        }else if($partner != '' && $partner != null){
            $tambahan = '&partner_reff='.strval($partner);
        }
        
        $curl = curl_init();
        // '/linkqu-partner/report/mutasi?start_date='.$start_date.'&end_date='.$end_date.'&username='.getenv('USERNAME_LINKQU').$tambahan,
        curl_setopt_array($curl, array(
        CURLOPT_URL => getenv('DOMAIN_LINKQU').'/linkqu-partner/report/mutasi?username='.getenv('USERNAME_LINKQU').'&offset='.$offset.'&limit='.$limit.'&start_date='.$start_date.'&end_date='.$end_date.$tambahan,
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
        return $this->response->setJSON($response);
    }
}