<?php

namespace App\Controllers;
use App\Models\MitraModel;

class Mitra extends BaseController
{

    protected $request;
    protected $session;
    protected $mitra;
    protected $uri;
    
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->session = session();
        $this->mitra = new MitraModel();
        $this->uri = $this->request->uri;   
    }

    public function index()
    {
        $data['view'] = 'mitra/data_mitra';
        $data['title'] = 'Data Mitra';
        $data['dataMitra'] = $this->mitra->getMitraAll();
        return view('dashboard/layout', $data);
    }

    public function add() {
        $data['title'] = 'Add Data Mitra';
        $data['view'] = 'mitra/add_mitra';
        return view('dashboard/layout', $data);
    }

    public function save_mitra() {
        $namaMitra = $this->request->getPost('namaMitra');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $alamat = $this->request->getPost('alamat');
        $uname = $this->request->getPost('uname');
        $pass = $this->request->getPost('pass');

        $data = array(
            "id_mitra" => $this->mitra->getNextSequenceMitra(),
            "nama_mitra" => $namaMitra,
            "email" => $email,
            "phone" => $phone,
            "alamat" => $alamat,
            "uname" => $uname,
            "passw" =>  password_hash($pass, PASSWORD_ARGON2I)
        );

        $this->mitra->saveMitra($data);
        return "sukses";
    }

    public function mitra_temp() {
        $isAdmin = $this->session->get('masukAdmin');
        if(isset($isAdmin)) {
            $id_mitra = $this->request->getPost('id');
            $this->session->set('id_mitra', $id_mitra);
        } 
        return "sukses";
    }

    public function edit_mitra() {
        $data['title'] = 'Edit Data Mitra';
        $data['view'] = 'mitra/edit_mitra';
        $idMitra = $this->session->get('id_mitra');
        $data['data_mitra'] = $this->mitra->getMitra($idMitra);
        return view('dashboard/layout', $data);
    }

    public function update_mitra() {
        $namaMitra = $this->request->getPost('namaMitra');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $alamat = $this->request->getPost('alamat');
        $uname = $this->request->getPost('uname');
        $pass = $this->request->getPost('pass');

        $idMitra = $this->session->get('id_mitra');

        if(isset($pass) && $pass != null) {
            $data = array(
                "nama_mitra" => $namaMitra,
                "email" => $email,
                "phone" => $phone,
                "alamat" => $alamat,
                "uname" => $uname,
                "passw" =>  password_hash($pass, PASSWORD_ARGON2I)
            );
        } else {
            $data = array(
                "nama_mitra" => $namaMitra,
                "email" => $email,
                "phone" => $phone,
                "alamat" => $alamat,
                "uname" => $uname,
            );
        }
        $this->mitra->updateMitra($idMitra, $data);
        return "sukses";
    }

    public function rm_mitra() {
        $id = $this->request->getPost('id');
        $this->mitra->deleteMitra($id);
        return "sukses";
    }
}