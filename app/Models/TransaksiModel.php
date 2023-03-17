<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class TransaksiModel {
    private $rekon_transaksi;
    private $rekon_transaksi_seq;
    private $transaksi_collection;
    private $transaksi_buff;
    private $transaksi_detail;
    private $transaksi_header;
    private $transaksi_buff_seq;


    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->session = session();
        $this->id_mitra = $this->session->get('id_mitra');
        $this->rekon_transaksi = $database->rekon_transaksi;
        $this->rekon_transaksi_seq = $database->rekon_transaksi_seq;
        $this->transaksi_collection = $database->transaksi_collection;
        $this->transaksi_buff = $database->transaksi_buff;
        $this->transaksi_detail = $database->transaksi_detail;
        $this->transaksi_header = $database->transaksi_header;
        $this->transaksi_buff_seq = $database->transaksi_buff_seq;
    }

    function getRekonTransaksiAll($limit = 10) {
        try {
            $cursor = $this->rekon_transaksi->find([], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekons;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekonTransaksiOne($id) {
        try {
            $rekon = $this->rekon_transaksi->findOne(['id_rekon' => (int)  $id]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    /* Data Transaksi */
    function getTransaksiAll() {
        try {
            $cursor = $this->transaksi_buff->find([]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching transaksi all '. $ex->getMessage(), 500);
        }
    }

    function getTransaksiOne($id) {
        try {
            $rekon = $this->transaksi_buff->findOne(['id_transaksi' => (int)  $id]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertTransaksi($data) {
        try {
            $result = $this->transaksi_buff->insertOne($data);

            if($result->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function updateTransaksi($id, $data) {
        try {
            $result = $this->transaksi_buff->updateOne(
                ['id_transaksi' => (int) $id],
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

    function updateTransaksiPush($id, $data) {
        try {
            $result = $this->transaksi_buff->updateOne(
                ['id_transaksi' => $id],
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

    function updateTransaksiPushByCollection($id, $data) {
        try {
            $result = $this->transaksi_buff->updateMany(
                ['id_collection' => $id],
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


    function deleteKolomCompare($id_transaksi, $kolomIndex, $kolomName) {
        try {
            $result = $this->transaksi_buff->updateOne(
                ['id_transaksi' => $id_transaksi],
                ['$pull' => 
                    [
                        "kolom_compare" => [
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
            show_error('Error while deleting a transaksi_buff with ID: ' . $id_transaksi . $ex->getMessage(), 500);
        }
    }

    function deleteKolomCompareByCollection($id_collection, $kolomIndex, $kolomName) {
        try {
            $result = $this->transaksi_buff->updateMany(
                ['id_collection' => $id_collection],
                ['$pull' => 
                    [
                        "kolom_compare" => [
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
            show_error('Error while deleting a transaksi_buff with ID: ' . $id_transaksi . $ex->getMessage(), 500);
        }
    }

    function deleteKolomSum($id_transaksi, $kolomIndex, $kolomName) {
        try {
            $result = $this->transaksi_buff->updateOne(
                ['id_transaksi' => $id_transaksi],
                ['$pull' => 
                    [
                        "kolom_sum" => [
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
            show_error('Error while deleting a transaksi_buff with ID: ' . $id_transaksi . $ex->getMessage(), 500);
        }
    }

    /* Data Transaksi Details */
    function getTransaksiDetail($id_transaksi, $limit = 0) {
        try {
            $cursor = $this->transaksi_detail->find(['id_transaksi' => (int) $id_transaksi], ['limit' => $limit]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }


    function getTransaksiDetailFound($id_transaksi, $limit = 0) {
        try {
            $cursor = $this->transaksi_detail->find(['id_transaksi' => (int) $id_transaksi, 'is_found' => true], ['limit' => $limit]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getTransaksiDetailNotFound($id_transaksi, $limit = 0) {
        try {
            $cursor = $this->transaksi_detail->find(['id_transaksi' => (int) $id_transaksi, 'is_found' => false], ['limit' => $limit]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function insertTransaksiDetailMany($data) {
        try {
            $insertResult = $this->transaksi_detail->insertMany($data);

            if($insertResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function deleteTransaksiDetailMany($id) {
        try {
            $result = $this->transaksi_detail->deleteMany(['id_transaksi' => $id]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getTransaksiDetailByCollection($id_collection, $is_found = false, $limit = 0) {
        try {
            $cursor = $this->transaksi_detail->find(['id_collection' => $id_collection, 'is_found' => (boolean) $is_found], ['limit' => $limit]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    /* Transaksi Header */
    function deleteTransaksiManyHeader($id) {
        try {
            $result = $this->transaksi_header->deleteMany(['id_rekon' => $id]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }    
    
    function insertTransaksiManyHeader($data) {
        try {
            $insertOneResult = $this->transaksi_header->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }


    /* collection */
    function getCollectionAll() {
        try {
            $cursor = $this->transaksi_collection->find([]);
            $data = $cursor->toArray();

            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching getCollectionAll: ' . $ex->getMessage(), 500);
        }
    }

    function getCollection($idMitra) {
        try {
            $cursor = $this->transaksi_collection->find(['id_mitra' => (int) $idMitra]);
            $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching getCollectionAll: ' . $ex->getMessage(), 500);
        }
    }

    function getCollectionById($id) {
        try {
            $data = $this->transaksi_collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            // $data = $cursor->toArray();
            return $data;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching getCollectionAll: ' . $ex->getMessage(), 500);
        }
    }

    function getNextSequenceRekon(){     
        $ret = $this->transaksi_buff_seq->findOneAndUpdate(
            array("_id" => "id_transaksi"),
            array('$inc' => array("seq" => 1)),
            array("new" => true, "upsert" => true)
        );
        return $ret->seq;
    }

    /* ============================= */

    function insertCollection($data) {
        try {
            $result = $this->transaksi_collection->insertOne($data);

            if($result->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

}
