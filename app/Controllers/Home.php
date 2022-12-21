<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('upload');
    }

    public function do_upload_csv() {

        $csv = $this->request->getFile('file');
        // var_dump($csv);
        $handle = fopen($csv,"r");
        $arrData = array();
        while (($row = fgetcsv($handle, 100000)) != FALSE) //get row vales
        {
            $drow = array(
                "data_row" => implode(",",$row),
                "tipe" => "1",
            );
            array_push($arrData, $drow);
        }

        $model = model(DataModels::class);
        $model->insertRekonMany($arrData);
        echo json_encode($arrData);
    }

    public function data_rekon_master()
    {
        $data['title'] = 'Data Rekon Master';
        $data['view'] = 'base/dashboard/rekon_master';
        $data['total_pengunjung'] = $this->DashboardModel->get_total_pengunjung();
        $data['total_pengunjung_today'] = $this->DashboardModel->get_total_pengunjung_today();
        $data['total_mingguan'] = $this->DashboardModel->get_total_pengunjung_mingguan();
        $data['pengunjung'] = $this->DashboardModel->get_all_pengunjung();

        return view('base/dashboard/layout', $data);
    }
}
