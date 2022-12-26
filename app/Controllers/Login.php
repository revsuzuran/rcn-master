<?php 

namespace App\Controllers;
use App\Models\LoginModel;

class Login extends BaseController
{
    public function __construct() {
        // mengisi variable global dengan data
        $this->session = session();
		$this->request = \Config\Services::request(); //memanggil class request
        $this->uri = $this->request->uri; //class request digunakan untuk request uri/url
        $this->login_model = new LoginModel();
    }

    public function do_auth(){

        $uname = $this->request->getPost('uname');
        $pwd = md5($this->request->getPost('password'));
        $hasil = $this->login_model->getUserOne($uname, $pwd);
        if(isset($hasil->name))
        {
            // set session
            $sess_data = array('masukAdmin' => TRUE, 'uname' => $hasil->name, 'uname_admin' => $hasil->name);
            $this->session->set($sess_data);
            return redirect()->to(base_url());
            exit();
        }
        else
        {
            $this->session->setFlashdata('errors', ['Password Salah']);
            return redirect()->to(base_url('/login'));
        }
		
    }
    
    public function do_unauth(){

        $this->session->destroy();
        return redirect()->to(base_url('/login'));
		
	}

    public function login()
    {

        // init data first time;
        // $this->login_model->getUserOne($uname, $pwd);

        if(session()->has('masukAdmin'))
        {
        	return redirect()->to(base_url());
        }
        $data['title'] = 'Selamat Datang!';
        $data['view'] = 'auth/login';
        return view('auth/layout', $data);
    }
}