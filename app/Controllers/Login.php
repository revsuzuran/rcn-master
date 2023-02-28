<?php 

namespace App\Controllers;
use App\Models\LoginModel;
use CodeIgniter\HTTP\RequestInterface;

class Login extends BaseController
{
    protected $session;
    protected $request;
    protected $uri;
    protected $login_model;

    public function __construct() {
        // mengisi variable global dengan data
        $this->session = session();
		$this->request = \Config\Services::request(); //memanggil class request
        $this->uri = $this->request->uri; //class request digunakan untuk request uri/url
        $this->login_model = new LoginModel();
        
        //Key ReChapta
        $this->siteKey = '6LcrV9wZAAAAAI2P97WdwKv2Da6HmE4U1ZNtriJq';
        $this->secretKey = '6LcrV9wZAAAAABPGFjOFOUN1J48VkLHf1Nve4leo';
    }

    public function do_auth(){

        $uname = $this->request->getPost('uname');
        $pwd = $this->request->getPost('password');
        $hasil = $this->login_model->getUserOne($uname, md5($pwd));
        $mitra = $this->login_model->getUserMitra($uname);

        //verifReChapta
        $status = $this->verifReChapta();
        if(!$status['success']){
            $this->session->setFlashdata('errors', ['reCAPTCHA validation failed.']);
            return redirect()->to(base_url('/login'));
        }

        if(isset($hasil->name))
        {
            // set session admin
            $sess_data = array('masukAdmin' => TRUE, 'uname' => $hasil->name, 'uname_admin' => $hasil->username, 'isLogin' => TRUE, "id_admin" => $hasil->_id->__toString());
            $this->session->set($sess_data);
            return redirect()->to(base_url());
        } else if(isset($mitra->id_mitra) && password_verify($pwd, $mitra->passw) == true)
        {
            // set session mitra
            $sess_data = array('masukMitra' => TRUE, 'uname' => $mitra->uname, 'uname_admin' => $mitra->uname, 'id_mitra' => $mitra->id_mitra, 'isLogin' => TRUE);
            $this->session->set($sess_data);
            return redirect()->to(base_url());
        }
        else
        {
            $this->session->setFlashdata('errors', ['Password Salah']);
            return redirect()->to(base_url('/login'));
        }
		
    }
    public function verifReChapta(){
        $recaptchaResponse = trim($this->request->getVar('g-recaptcha-response'));
        $userIp=$this->request->getIPAddress();
        
        $credential = array(
                'secret' => $this->secretKey,
                'response' => $this->request->getVar('g-recaptcha-response')
            );
    
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        return json_decode($response, true);
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
        $data['sitekey'] = $this->siteKey;
        $data['title'] = 'Selamat Datang!';
        $data['view'] = 'auth/login';
        return view('auth/layout', $data);
    }
}