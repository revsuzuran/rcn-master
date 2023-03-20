<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class SettlementModel {
    private $rekon_buff;
    private $rekon_buff_seq;
    private $disbursment_detail;
    private $disbursment_order;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->session = session();
        $this->id_mitra = $this->session->get('id_mitra');
        $this->rekon_buff = $database->rekon_buff;
        $this->disbursment_detail = $database->disbursment_detail;
        $this->disbursment_order = $database->disbursment_order;
    }

    function insertDisbursmentMany($data) {
        try {
            $insertOneResult = $this->disbursment_detail->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function deleteDisbursmentMany($id) {
        try {
            $result = $this->disbursment_detail->deleteMany(['id_rekon_result' => $id]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }    

    function getAllDisburstDetail($id_rekon_result, $limit =0) {
        try {
            $cursor = $this->disbursment_detail->find(['id_rekon_result' => $id_rekon_result], ['limit' => $limit]);
            $rekons = $cursor->toArray();

            return $rekons;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getDisburstDetail($id) {
        try {
            $rekon = $this->disbursment_detail->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function updateDisburstDetailOne($id, $data) {
        try {
            $result = $this->disbursment_detail->updateOne(
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


    /* disbursment order */
    function insertDisbursmentOrder($data) {
        try {
            $insertOneResult = $this->disbursment_order->insertOne($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function updateDisbursmentOrder($id, $data) {
        try {
            $result = $this->disbursment_order->updateOne(
                ['id_rekon_result' => (int) $id],
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

    function getDisburseAll($limit = 0) {
        try {
            $desc = -1;
            $cursor = $this->disbursment_order->find([], ['limit' => $limit, 'sort' => ['updatedAt' => 1] ]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }
}