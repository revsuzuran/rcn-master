<?php 

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\DataModels;

class Setting extends BaseController
{
    public function __construct()
    {
        // mengisi variable global dengan data
        $this->session = session();
        $this->request = \Config\Services::request(); //memanggil class request
        $this->uri = $this->request->uri; //class request digunakan untuk request uri/url
        $this->user_model = new UserModel();
        $this->ftp_model = new DataModels();
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
        $data['title'] = 'Pengaturan FTP';
        $data['view'] = 'ftp';
        $data['data_ftp'] = $this->ftp_model->getFtp();
        
        return view('dashboard/layout', $data);
    }

    public function update_ftp() {
        $uname = $this->request->getPost('username');
        $passw = $this->request->getPost('password');
        $domain = $this->request->getPost('domain');
        $this->ftp_model->updateFtp($uname, $passw, $domain);
        return "sukses";

    }


}