<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class DBModel
{
    private $db;

    function initConnection($hostname, $user, $pass, $database, $driver, $port)
    {
        $custom = [
            'DSN'      => '',
            'hostname' => $hostname,
            'username' => $user,
            'password' => $pass,
            'database' => $database,
            'DBDriver' => $driver,
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
            'port'     => $port,
        ];
        $db = \Config\Database::connect($custom);
        // $connection = mysqli_connect($hostname, $user, $pass, $database) or die(mysqli_error($connection));
        $this->db = $db;
    }

    function getData($query) {
        $query = $this->db->query($query);
        $row = $query->getResultArray();
        return $row;
    }
}