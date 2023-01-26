<?php

namespace App\Controllers;
use App\Models\ChannelModel;
use App\Models\MitraModel;

class Channel extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->session = session();
        $this->channel = new ChannelModel();
        $this->mitra = new MitraModel();
        $this->uri = $this->request->uri;   
    }

    public function index()
    {
        $idMitra = $this->session->get('id_mitra');
        $data['data_mitra'] = $this->mitra->getMitra($idMitra);
        $data['data_channel'] = $this->channel->getAllChannelMitra($idMitra);
        $data['view'] = 'channel/data_channel';
        $data['title'] = 'Data Channel';
        return view('dashboard/layout', $data);
    }

    public function add() {
        $idMitra = $this->session->get('id_mitra');
        $data['data_mitra'] = $this->mitra->getMitra($idMitra);
        $data['title'] = 'Add Data Channel';
        $data['view'] = 'channel/add_channel';
        return view('dashboard/layout', $data);
    }

    public function save_channel() {
        $namaChannel = $this->request->getPost('namaChannel');
        $fee_admin = $this->request->getPost('feeAdmin');
        $fee1 = $this->request->getPost('fee1');
        $fee2 = $this->request->getPost('fee2');
        $fee3 = $this->request->getPost('fee3');
        $fee4 = $this->request->getPost('fee4');
        $fee5 = $this->request->getPost('fee5');

        $is_persen_admin = $this->request->getPost('is_persen_admin') == 'true' ? 1 : 0;
        $is_persen1 = $this->request->getPost('is_persen1') == 'true' ? 1 : 0;
        $is_persen2 = $this->request->getPost('is_persen2') == 'true' ? 1 : 0;
        $is_persen3 = $this->request->getPost('is_persen3') == 'true' ? 1 : 0;
        $is_persen4 = $this->request->getPost('is_persen4') == 'true' ? 1 : 0;
        $is_persen5 = $this->request->getPost('is_persen5') == 'true' ? 1 : 0;

        // var_dump($this->request->getPost('is_persen5'));
        // die;

        $idMitra = $this->session->get('id_mitra');

        $data = array(
            "id_mitra" => (int) $idMitra,
            "nama_channel" => $namaChannel,
            "fee1" => array(
                "nilai" => $fee1,
                "is_prosentase" => $is_persen1
            ),
            "fee2" => array(
                "nilai" => $fee2,
                "is_prosentase" => $is_persen2
            ),
            "fee3" => array(
                "nilai" => $fee3,
                "is_prosentase" => $is_persen3
            ),
            "fee4" => array(
                "nilai" => $fee4,
                "is_prosentase" => $is_persen4
            ),
            "fee5" => array(
                "nilai" => $fee5,
                "is_prosentase" => $is_persen5
            ),
            "fee_admin" => array(
                "nilai" => $fee_admin,
                "is_prosentase" => $is_persen_admin
            ),
        );
        // var_dump($data);die;
        $this->channel->saveChannel($data);
        return "sukses";
    }

    public function channel_temp() {
        $id_channel = $this->request->getPost('id');
        $this->session->set('id_channel', $id_channel);
        return "sukses";
    }

    public function edit_channel() {
        $data['title'] = 'Edit Data Channel';
        $data['view'] = 'channel/edit_channel';
        $idChannel = $this->session->get('id_channel');
        $data['data_channel'] = $this->channel->getChannel($idChannel);
        return view('dashboard/layout', $data);
    }

    public function update_channel() {
        $namaChannel = $this->request->getPost('namaChannel');
        $fee_admin = $this->request->getPost('feeAdmin');
        $fee1 = $this->request->getPost('fee1');
        $fee2 = $this->request->getPost('fee2');
        $fee3 = $this->request->getPost('fee3');
        $fee4 = $this->request->getPost('fee4');
        $fee5 = $this->request->getPost('fee5');

        $is_persen_admin = $this->request->getPost('is_persen_admin') == 'true' ? 1 : 0;
        $is_persen1 = $this->request->getPost('is_persen1') == 'true' ? 1 : 0;
        $is_persen2 = $this->request->getPost('is_persen2') == 'true' ? 1 : 0;
        $is_persen3 = $this->request->getPost('is_persen3') == 'true' ? 1 : 0;
        $is_persen4 = $this->request->getPost('is_persen4') == 'true' ? 1 : 0;
        $is_persen5 = $this->request->getPost('is_persen5') == 'true' ? 1 : 0;

        // var_dump($this->request->getPost('is_persen5'));
        // die;

        $idMitra = $this->session->get('id_mitra');
        $idChannel = $this->session->get('id_channel');

        $data = array(
            "id_mitra" => (int) $idMitra,
            "nama_channel" => $namaChannel,
            "fee1" => array(
                "nilai" => $fee1,
                "is_prosentase" => $is_persen1
            ),
            "fee2" => array(
                "nilai" => $fee2,
                "is_prosentase" => $is_persen2
            ),
            "fee3" => array(
                "nilai" => $fee3,
                "is_prosentase" => $is_persen3
            ),
            "fee4" => array(
                "nilai" => $fee4,
                "is_prosentase" => $is_persen4
            ),
            "fee5" => array(
                "nilai" => $fee5,
                "is_prosentase" => $is_persen5
            ),
            "fee_admin" => array(
                "nilai" => $fee_admin,
                "is_prosentase" => $is_persen_admin
            ),
        );

        $this->channel->updateChannel($idChannel, $data);
        return "sukses";
    }

    public function rm_channel() {
        $id = $this->request->getPost('id');
        $this->channel->deleteChannel($id);
        return "sukses";
    }
}