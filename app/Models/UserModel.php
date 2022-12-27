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

    function insertRekon($title, $author, $pages) {
        try {
            $insertOneResult = $this->user->insertOne([
                'title' => $title,
                'author' => $author,
                'pages' => $pages,
                'pagesRead' => 0,
            ]);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function insertRekonMany($data) {
        try {
            $insertOneResult = $this->user->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
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

    function deleteRekon($id) {
        try {
            $result = $this->user->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertRekonMaster($namaRekon, $idRekon) {
        try {
            $insertOneResult = $this->user->insertOne([
                'id_rekon' => $idRekon,
                'nama_rekon' => $namaRekon
            ]);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function getNextSequenceRekon(){
        global $user;
        
        $retval = $user->findAndModify(
            array('_id' => 'id_rekon'),
            array('$inc' => array("seq" => 1)),
            null,
            array(
                "new" => true,
            )
        );
        return $retval['seq'];
    }
    
    
}