<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class MitraModel
{
    function __construct()
    {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->mitra = $database->mitra_data;
        $this->mitra_seq = $database->mitra_data_seq;
    }

    function getMitraAll($limit = 0) {
        try {
            $cursor = $this->mitra->find([], ['limit' => $limit]);
            $usr = $cursor->toArray();

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getMitra($id) {
        try {
            $usr = $this->mitra->findOne(['id_mitra' => (int) $id]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }

    function getNextSequenceMitra(){        
        $ret = $this->mitra_seq->findOneAndUpdate(
            array("_id" => "id_mitra"),
            array('$inc' => array("seq" => 1)),
            array("new" => true, "upsert" => true)
        );
        return $ret->seq;
    }

    function saveMitra($data) {
        try {
            $insertOneResult = $this->mitra->insertOne($data);
            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a mitra: ' . $ex->getMessage(), 500);
        }
    }

    function updateMitra($id, $data) {
        
        try {
            $result = $this->mitra->updateOne(
                ['id_mitra' => (int) $id],
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

    function deleteMitra($id) {
        try {
            $result = $this->mitra->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }
}
