<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class UserModel {
    private $user;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->user = $database->user_data;
    }

    function getUser() {
        try {
            $cursor = $this->user->find([], ['limit' => 0]);
            $usr = $cursor->toArray();

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getUserOne($uname) {
        try {
            $usr = $this->user->findOne(['username' => $uname]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function updateUser($uname, $password, $name) {
        try {

            if($password == "") {
                $result = $this->user->updateOne(
                    ['username' => $uname],
                    ['$set' => [
                        'username' => $uname,
                        'name' => $name,
                    ]]
                );
            } else {
                $result = $this->user->updateOne(
                    ['username' => $uname],
                    ['$set' => [
                        'username' => $uname,
                        'password' => $password,
                        'name' => $name,
                    ]]
                );
            }

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }
    
    
    
}