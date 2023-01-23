<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonResult {
    private $rekon_result;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->rekon_result = $database->rekon_result;
    }

    function getRekons($limit = 10) {
        try {
            $desc = -1;
            $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ]], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id) {
        try {
            $rekon = $this->rekon_result->find(['id_rekon' => (int) $id, "id_rekon_result" => [ '$ne' => null ]]);
            $rekon = $rekon->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonAll($limit = 10) {
        try {
            $cursor = $this->rekon_result->find([], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertRekon($namaRekon, $idRekon) {
        try {
            $insertOneResult = $this->rekon_result->insertOne([
                'id_rekon' => $idRekon,
                'nama_rekon' => $namaRekon,
                'kolom_compare' => array(),
                'kolom_sum' => array(),
                'is_proses' => "",
                'timestamp' => date("Y-m-d h:i:sa"),
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
            $result = $this->rekon_result->updateOne(
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

    function updateRekonPush($id, $data) {
        try {
            $result = $this->rekon_result->updateOne(
                ['id_rekon' => $id],
                ['$push' => $data ]
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
            $result = $this->rekon_result->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function deleteKolomCompare($id_rekon, $tipe, $kolomIndex, $kolomName) {
        try {
            $result = $this->rekon_result->updateOne(
                ['id_rekon' => $id_rekon],
                ['$pull' => 
                    [
                        "kolom_compare" => [
                            'tipe' => $tipe,
                            'kolom_index' => $kolomIndex,
                            'kolom_name' => $kolomName
                        ]
                    ]
                ]
            );

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id_rekon . $ex->getMessage(), 500);
        }
    }

    function deleteKolomSum($id_rekon, $tipe, $kolomIndex, $kolomName) {
        try {
            $result = $this->rekon_result->updateOne(
                ['id_rekon' => $id_rekon],
                ['$pull' => 
                    [
                        "kolom_sum" => [
                            'tipe' => $tipe,
                            'kolom_index' => $kolomIndex,
                            'kolom_name' => $kolomName
                        ]
                    ]
                ]
            );

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id_rekon . $ex->getMessage(), 500);
        }
    }

    
}