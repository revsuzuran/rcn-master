<?php

namespace App\Controllers;
use App\Models\BankModel;
use App\Models\MitraModel;

class Bank extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->session = session();
        $this->bank = new BankModel();
        $this->mitra = new MitraModel();
        $this->uri = $this->request->uri;   
    }

    public function index()
    {
        $idMitra = $this->session->get('id_mitra');
        $data['data_mitra'] = $this->mitra->getMitra($idMitra);
        $data['data_bank'] = $this->bank->getAllBank($idMitra);
        $data['view'] = 'bank/data_bank';
        $data['title'] = 'Data Bank';
        return view('dashboard/layout', $data);
    }

    public function add() {
        $data['title'] = 'Add Data Bank';
        $data['view'] = 'bank/add_bank';
        return view('dashboard/layout', $data);
    }

    public function save_bank() {
        $namaBank = $this->request->getPost('nama_bank');
        $norek = $this->request->getPost('norek');
        $kode_bank = $this->request->getPost('kode_bank');
        
        $idMitra = $this->session->get('id_mitra');

        $data = array(
            "id_mitra" => (int) $idMitra,
            "nama_bank" => $namaBank,
            "norek" => $norek,
            "kode_bank" => $kode_bank,            
        );
        
        $this->bank->saveBank($data);
        return "sukses";
    }

    public function bank_temp() {
        $id_bank = $this->request->getPost('id');
        $this->session->set('id_bank', $id_bank);
        return "sukses";
    }

    public function edit_bank() {
        $data['title'] = 'Edit Data Bank';
        $data['view'] = 'bank/edit_bank';
        $idBank = $this->session->get('id_bank');
        $data['data_bank'] = $this->bank->getBank($idBank);
        return view('dashboard/layout', $data);
    }

    public function update_bank() {
        $namaBank = $this->request->getPost('nama_bank');
        $norek = $this->request->getPost('norek');
        $kode_bank = $this->request->getPost('kode_bank');

        $idMitra = $this->session->get('id_mitra');
        $idBank = $this->session->get('id_bank');

        $data = array(
            "id_mitra" => (int) $idMitra,
            "nama_bank" => $namaBank,
            "norek" => $norek,
            "kode_bank" => $kode_bank,            
        );
        
        $this->bank->updateBank($idBank, $data);
        return "sukses";
    }

    public function rm_bank() {
        $id = $this->request->getPost('id');
        $this->bank->deleteBank($id);
        return "sukses";
    }
}