<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonBuff {
    private $rekon_buff;
    private $rekon_buff_seq;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->rekon_buff = $database->rekon_buff;
        $this->rekon_buff_seq = $database->rekon_buff_seq;
    }

    function getRekons($limit = 10) {
        try {
            $cursor = $this->rekon_buff->find([], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekons;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id) {
        try {
            $rekon = $this->rekon_buff->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertRekon($namaRekon, $idRekon) {
        try {
            $insertOneResult = $this->rekon_buff->insertOne([
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

    function updateRekon($id, $data) {
        try {
            $result = $this->rekon_buff->updateOne(
                ['id_rekon' => $id],
                ['$set' => $data ]
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
            $result = $this->rekon_buff->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getNextSequenceRekon(){     
        $ret = $this->rekon_buff_seq->findOneAndUpdate(
            array("_id" => "id_rekon"),
            array('$inc' => array("seq" => 1)),
            array("new" => true, "upsert" => true)
        );
        return $ret->seq;
    }
    
    
}