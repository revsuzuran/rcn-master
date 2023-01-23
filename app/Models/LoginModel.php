<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class LoginModel {
    private $login;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->login = $database->user_data;
        $this->loginMitra = $database->mitra_data;
    }

    function getUser() {
        try {
            $cursor = $this->login->find([], ['limit' => 0]);
            $usr = $cursor->toArray();

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getUserOne($uname, $pwd) {
        try {
            $usr = $this->login->findOne(['username' => $uname, 'password' => $pwd]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getUserMitra($uname) {
        try {
            $usr = $this->loginMitra->findOne(['uname' => $uname]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }    
    
}