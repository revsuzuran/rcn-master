<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class Postgres
{
    private $db;

    function initConnection($hostname, $user, $pass, $database)
    {
        
        $custom = [
            'DSN'      => '',
            'hostname' => $hostname,
            'username' => $user,
            'password' => $pass,
            'database' => $database,
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ];
        $db = \Config\Database::connect($custom);

        // $connection = mysqli_connect($hostname, $user, $pass, $database) or die(mysqli_error($connection));
        $this->db = $db;
    }

    function test($query) {
        $query = $this->db->query('select * from pengunjung');

        // foreach ($query->getResult() as $row) {
            echo json_encode($query);
        // }
    }
}