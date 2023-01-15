<?php 

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\DataModels;
use App\Models\RekonBuff;

class Setting extends BaseController
{
    public function __construct()
    {
        // mengisi variable global dengan data
        $this->session = session();
        $this->request = \Config\Services::request(); //memanggil class request
        $this->uri = $this->request->uri; //class request digunakan untuk request uri/url
        $this->user_model = new UserModel();
        $this->data_model = new DataModels();
        $this->rekon_buff = new RekonBuff();
    }

    public function profil() {
        $uname = $this->session->get('uname_admin');
        $data['title'] = 'Pengaturan User';
        $data['view'] = 'profil';
        $data['user'] = $this->user_model->getUserOne($uname);
        return view('dashboard/layout', $data);
    }

    public function update_user() {
        // die('ok');
        $uname = $this->request->getPost('username');
        $passw = $this->request->getPost('password');
        $name = $this->request->getPost('name');
        
        $this->user_model->updateUser($uname, $passw, $name);
        return "sukses";

    }

    public function ftp() {
        $data['title'] = 'Data FTP';
        $data['view'] = 'ftp';
        $data['dataFtp'] = $this->data_model->getFtp();
        
        return view('dashboard/layout', $data);
    }


    public function edit_ftp() {
        $data['title'] = 'Edit Data FTP';
        $data['view'] = 'edit_ftp';
        $id = $this->uri->getSegment(2);
        $data['data_ftp'] = $this->data_model->getFtpOne($id);
        // die($data['dataFtp']);
        return view('dashboard/layout', $data);
    }

    public function rm_ftp() {
        $id = $this->request->getPost('id');
        $this->data_model->deleteFtpOne($id);
        return "sukses";
    }

    public function update_ftp() {
        $uname = $this->request->getPost('username');
        $passw = $this->request->getPost('password');
        $domain = $this->request->getPost('domain');
        $ftpName = $this->request->getPost('ftp_name');
        $path = $this->request->getPost('path');
        $id = $this->request->getPost('id');

        $data = array(
            "username" => $uname,
            "password" => $passw,
            "domain" => $domain,
            "ftp_name" => $ftpName,
            "path" => $path
        );

        $this->data_model->updateFtp($id, $data);
        return "sukses";

    }
    public function add_ftp() {
        $data['title'] = 'Add Data FTP';
        $data['view'] = 'add_ftp';
        return view('dashboard/layout', $data);
    }

    public function save_ftp() {
        $uname = $this->request->getPost('username');
        $passw = $this->request->getPost('password');
        $domain = $this->request->getPost('domain');
        $ftpName = $this->request->getPost('ftp_name');
        $path = $this->request->getPost('path');

        $data = array(
            "username" => $uname,
            "password" => $passw,
            "domain" => $domain,
            "ftp_name" => $ftpName,
            "path" => $path
        );

        $this->data_model->saveFtp($data);
        return "sukses";

    }

    /* ============================================= */
    /* CONTROL DATABASE HERE */
    /* ============================================= */
    public function database() {
        $data['title'] = 'Data Database';
        $data['view'] = 'database_view';
        $data['dataDb'] = $this->data_model->getDatabase();
        
        return view('dashboard/layout', $data);
    }

    public function edit_database() {
        $data['title'] = 'Edit Data Database';
        $data['view'] = 'edit_database';
        $id = $this->uri->getSegment(2);
        $data['data_db'] = $this->data_model->getDatabaseOne($id);
        // die($data['dataFtp']);
        return view('dashboard/layout', $data);
    }

    public function update_database() {
        $dbName = $this->request->getPost("db_name");
        $driver = $this->request->getPost("driver");
        $database = $this->request->getPost("database");
        $hostname = $this->request->getPost("hostname");
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $port = $this->request->getPost("port");
        $id = $this->request->getPost("id");

        $data = array(
            "database" => $database,
            "driver" => $driver,
            "username" => $username,
            "password" => $password,
            "hostname" => $hostname,
            "db_name" => $dbName,
            "port" => $port
        );

        $this->data_model->updateDatabase($id, $data);
        return "sukses";

    }

    public function add_database() {
        $data['title'] = 'Add New Database';
        $data['view'] = 'add_database';
        return view('dashboard/layout', $data);
    }

    public function save_database() {
        $dbName = $this->request->getPost("dbName");
        $driver = $this->request->getPost("driver");
        $database = $this->request->getPost("database");
        $hostname = $this->request->getPost("hostname");
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $port = $this->request->getPost("port");

        $data = array(
            "database" => $database,
            "driver" => $driver,
            "username" => $username,
            "password" => $password,
            "hostname" => $hostname,
            "db_name" => $dbName,
            "port" => $port
        );
        
        $this->data_model->saveDatabase($data);
        return "sukses";

    }

    public function rm_database() {
        $id = $this->request->getPost('id');
        $this->data_model->deleteDatabaseOne($id);
        return "sukses";
    }


    /* config setting */
    public function get_setting() {
        $id = $this->request->getPost('id');
        echo json_encode($this->data_model->getSettingOne($id));
    }

    public function save_setting() {
        $id_rekon = $this->session->get('id_rekon');
        $tipe = $this->session->get('tipe');
        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $nama_setting = $this->request->getPost('nama_setting');

        $kolomCompare = array();
        foreach($rekonBuff->kolom_compare as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($kolomCompare, $row);
        }

        $kolomSum = array();
        foreach($rekonBuff->kolom_sum as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($kolomSum, $row);
        }

        $cleanRule = array();
        foreach($rekonBuff->clean_rule as $row) {
            if ($tipe != $row->tipe) continue;
            array_push($cleanRule, $row);
        }

        $data = array(
            "kolom_compare" => $kolomCompare,
            "kolom_sum" => $kolomSum,
            "delimiter" => $rekonBuff->delimiter,
            "clean_rule" => $cleanRule,
            "nama_setting" => $nama_setting
        );
        
        $this->data_model->saveSetting($data);
        return "sukses";

    }
}