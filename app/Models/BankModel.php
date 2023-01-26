<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class BankModel

{
    private $login;

    function __construct()
    {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->session = session();
        $this->bank = $database->bank_data;
        $this->id_mitra = $this->session->get('id_mitra');
    }

    function getAllBank($id) {
        try {
            $cursor = $this->bank->find(['id_mitra' => (int) $id], ['limit' => 0]);
            $usr = $cursor->toArray();

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getBank($id) {
        try {
            $usr = $this->bank->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }

    function saveBank($data) {
        try {
            $insertOneResult = $this->bank->insertOne($data);
            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a bank: ' . $ex->getMessage(), 500);
        }
    }

    function updateBank($id, $data) {
        try {
            $result = $this->bank->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
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

    function deleteBank($id) {
        try {
            $result = $this->bank->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }
}