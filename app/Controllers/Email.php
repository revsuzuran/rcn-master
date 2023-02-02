<?php 

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\DataModels;
use App\Models\RekonBuff;
use App\Models\MitraModel;
use App\Models\RekonBuffDetail;
use App\Models\RekonResult;
use App\Models\RekonUnmatch;
use App\Models\RekonMatch;
use App\Models\DBModel;
use App\Models\Postgres;
use App\Models\ChannelModel;
use App\Libraries\PdfGenerator;


class Email extends BaseController
{
    protected $user_model;
    protected $data_model;
    protected $mitra;
    protected $uri;
    protected $session;
    protected $rekon_buff;
    protected $email;
    protected $rekon_buff_detail;
    protected $rekon_result;
    protected $rekon_unmatch;
    protected $rekon_match;
    protected $dbModel;
    protected $pg;
    protected $channel_model;
    protected $pdfGen;

    public function __construct()
    {
        // mengisi variable global dengan data
        $this->session = session();
        $this->email = \Config\Services::email();
        $this->request = \Config\Services::request(); //memanggil class request
        $this->uri = $this->request->uri; //class request digunakan untuk request uri/url
        $this->user_model = new UserModel();
        $this->data_model = new DataModels();
        $this->rekon_buff = new RekonBuff();
        $this->mitra = new MitraModel();
        $this->rekon_buff = new RekonBuff();
        $this->rekon_buff_detail = new RekonBuffDetail();
        $this->rekon_result = new RekonResult();
        $this->rekon_unmatch = new RekonUnmatch();
        $this->rekon_match = new RekonMatch();
        $this->dbModel = new DBModel();
        $this->pg = new Postgres();
        $this->channel_model = new ChannelModel();
        $this->pdfGen = new PdfGenerator();
        $this->mitra = new MitraModel();    
		$this->uri = $this->request->uri;
    }

    public function kirim_email() {


        $emailTo = $this->request->getPost('emailTo'); 
        $emailCC = $this->request->getPost('emailCC'); 
        $subject = $this->request->getPost('subject'); 
        $bodyEmail = $this->request->getPost('bodyEmail');

        $loadConfig = $this->data_model->getSettingEmail();

        if($loadConfig[0]->host != null) {
            $this->session->setFlashdata('error', 'Failed Send! Setting SMTP Error');
            return redirect()->to(base_url('rekon/rekon_result'));
        }

        $config['SMTPHost'] = $loadConfig[0]->host;
        $config['SMTPUser'] = $loadConfig[0]->username;
        $config['SMTPPass'] = $loadConfig[0]->password;
        $config['SMTPPort'] = $loadConfig[0]->port;
        $config['protocol'] = $loadConfig[0]->protokol;
        $config['userAgent'] = $loadConfig[0]->user_agent;
        $config['SMTPCrypto'] = $loadConfig[0]->crypto;
        $config['CRLF'] = $loadConfig[0]->crlf;
        $config['newline'] = $loadConfig[0]->newline;
        $config['mailPath'] = $loadConfig[0]->path;
        $this->email->initialize($config);
        $this->email->setTo(explode(";", $emailTo));
        $this->email->setCc(explode(";", $emailCC));
        $this->email->setFrom('operasional.yokke@cs.linkqu.id', 'Rekon LinkQu');
        $this->email->setSubject($subject);
        $this->email->setMessage($bodyEmail);
        
        $dataPDF = $this->generatePDF();
        $dataCsvUnmatch = $this->export_unmatch();
        $this->email->attach($dataPDF['file_pdf'], 'attachment', $dataPDF['nama_pdf'], 'application/pdf');
        $this->email->attach($dataCsvUnmatch['file_data'], 'attachment', $dataCsvUnmatch['file_name'], 'text/csv');
    
        if ($this->email->send()) 
		{
            return redirect()->to(base_url('rekon/rekon_result'));
        }

    }

    public function generatePDF() {
         /* Preparing Data */
         $id_rekon = $this->session->get('id_rekon');
         $id_rekon_result = $this->session->get('id_rekon_result');
         $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
         $rekonResult = $this->rekon_result->getRekon($id_rekon, $id_rekon_result);
         $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, $id_rekon_result, 1, 25);
         $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon, $id_rekon_result, 2, 25);
         $dataRekon1match = $this->rekon_match->getRekonAll($id_rekon,$id_rekon_result, 1, 25);
         $dataRekon2match = $this->rekon_match->getRekonAll($id_rekon,$id_rekon_result, 2, 25);
         $dataHeader = $this->rekon_buff_detail->getHeader($id_rekon, "1", 1);
 
         $kolomFilter1 = array();
         $kolomFilter2 = array();
         foreach ($rekonBuff->kolom_compare as $rowCompare) {
             if($rowCompare->tipe == 1) array_push($kolomFilter1, $rowCompare->kolom_index);
             if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
         }
         
         $dataRekonSatu = $rekonResult[0]->data_result1;
         $dataRekonDua = $rekonResult[0]->data_result2;
         
         $data['title'] =  $rekonBuff->nama_rekon;
         $data['view'] = 'dashboard/rekon_result'; 
         $data['data_rekon'] = $rekonBuff; 
         $data['data_rekon_satu'] = $dataRekonSatu; 
         $data['data_rekon_dua'] = $dataRekonDua; 
         $data['data_rekon_unmatch_satu'] = $dataRekon1unmatch; 
         $data['data_rekon_unmatch_dua'] = $dataRekon2unmatch; 
         $data['data_rekon_match_satu'] = $dataRekon1match; 
         $data['data_rekon_match_dua'] = $dataRekon2match; 
         $data['kolom_filter_satu'] = $kolomFilter1; 
         $data['kolom_filter_dua'] = $kolomFilter2; 
         $data['data_header'] = $dataHeader;
 
        /* Preparing DomPDF */
        $Pdfgenerator = $this->pdfGen;
        $originalDate = $rekonBuff->tanggal_rekon;
        $newDate = date("Ymd", strtotime($originalDate));
        $file_pdf = $newDate . "_" . $rekonBuff->nama_rekon . "_#1";
        $data['nama_pdf'] = $file_pdf;
        $paper = 'A4';
        $orientation = "portrait";
        $html = view('pdf', $data);
         
        $data['file_pdf'] = $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation, false);
        return $data;
    }

    public function get_email() {

        $loadConfig = $this->data_model->getSettingEmail();
        $data['title'] = 'Pengaturan SMTP';
        $data['view'] = 'email/edit_email';
        $data['data_email'] = $loadConfig;
        return view('dashboard/layout', $data);
    }

    public function update_email() {
        $hostname = $this->request->getPost('hostname'); 
        $username = $this->request->getPost('username'); 
        $password = $this->request->getPost('password'); 
        $port = $this->request->getPost('port'); 
        $path = $this->request->getPost('path'); 
        $crypto = $this->request->getPost('crypto'); 
        $protokol = $this->request->getPost('protokol'); 
        $useragent = $this->request->getPost('useragent'); 
        $id = $this->request->getPost('id');

        if($password == "") {
            $data = array(
                "host" => $hostname,
                "username" => $username,
                "port" => $port,
                "path" => $path,
                "crypto" => $crypto,
                "protokol" => $protokol,
                "user_agent" => $useragent
            );
        } else {
            $data = array(
                "host" => $hostname,
                "username" => $username,
                "password" => $password,
                "port" => $port,
                "path" => $path,
                "crypto" => $crypto,
                "protokol" => $protokol,
                "user_agent" => $useragent
            );
        }
        
        $this->data_model->updateSettingEmail($id, $data);
        echo "sukses";
    }

    public function export_unmatch()
    {
         /* Preparing Data */
        $id_rekon = $this->session->get('id_rekon');
        $id_rekon_result = $this->session->get('id_rekon_result');
        $tipe = 1;
        $id = 2;

        $rekonBuff = $this->rekon_buff->getRekon($id_rekon);
        $dataRekon1unmatch = $this->rekon_unmatch->getRekonAll($id_rekon,$id_rekon_result, 1);
        $dataRekon2unmatch = $this->rekon_unmatch->getRekonAll($id_rekon,$id_rekon_result, 2);

        $dataHeader = $this->rekon_buff_detail->getHeader($id_rekon, $id, 1);

        $kolomFilter1 = array();
        $kolomFilter2 = array();
        foreach ($rekonBuff->kolom_compare as $rowCompare) {
            if($rowCompare->tipe == 2) array_push($kolomFilter1, $rowCompare->kolom_index);
            if($rowCompare->tipe == 2) array_push($kolomFilter2, $rowCompare->kolom_index);
        }

        $dataRekonUnmatch = $dataRekon2unmatch;
        $kolomFilter = $kolomFilter2;
        
        $delimiter = ","; 
        $originalDate = $rekonBuff->tanggal_rekon;
        $newDate = date("Ymd", strtotime($originalDate));
        $data['file_name'] = $newDate . "_" . $rekonBuff->nama_rekon . "_UNMATCH.csv";
        
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
        
        $dataHeader = $this->rekon_buff_detail->getHeader($id_rekon, $id, 1);
        foreach($dataHeader as $row) {
            $dataUnmatch = array();
            foreach ($row['data_row'] as $key => $rowData) {
                array_push($dataUnmatch, $rowData);
            }
            fputcsv($f, $dataUnmatch, $delimiter); 
        }

        
        foreach($dataRekonUnmatch as $row) {
            $dataUnmatch = array();
            foreach ($row['row_data'] as $key => $rowData) {
                if($tipe == 0) {
                    if (!in_array($key, $kolomFilter)) continue;
                    array_push($dataUnmatch, $rowData);
                } else if($tipe == 1) {
                    array_push($dataUnmatch, $rowData);
                } else {
                    die("no data");
                }
                
            }
            fputcsv($f, $dataUnmatch, $delimiter); 
        }
        

        // Move back to beginning of file 
        fseek($f, 0);
        
        // Return the data
        $data['file_data'] = stream_get_contents($f);
        return $data;
        
    }
}