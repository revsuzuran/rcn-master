<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class LoginModel {
    private $login;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->login = $database->user_data;
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

    function insertRekon($title, $author, $pages) {
        try {
            $insertOneResult = $this->collection->insertOne([
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
            $insertOneResult = $this->collection->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function updateRekon($id, $title, $author, $pagesRead) {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'title' => $title,
                    'author' => $author,
                    'pagesRead' => $pagesRead,
                ]]
            );

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
            $result = $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

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
            $insertOneResult = $this->collection->insertOne([
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
        global $collection;
        
        $retval = $collection->findAndModify(
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