<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonBuff {
    private $rekon_buff;
    private $rekon_buff_seq;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->session = session();
        $this->id_mitra = $this->session->get('id_mitra');
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
            $rekon = $this->rekon_buff->findOne(['id_rekon' => (int)  $id]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonSch($id) {
        try {
            $rekon = $this->rekon_buff->findOne(['id_rekon' => (int)  $id, "is_schedule" => 1]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonAll($limit = 10) {
        try {

            if($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_buff->find([], ['limit' => $limit, 'sort' => ['_id' => -1]]);
            } else {
                $cursor = $this->rekon_buff->find(['id_mitra' => (int) $this->id_mitra], ['limit' => $limit, 'sort' => ['_id' => -1]]);
            }

            
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }


    function getRekonSchAll($limit = 10) {
        try {
            if ($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_buff->find(["is_schedule" => 1], ['limit' => $limit, 'sort' => ['_id' => -1]]);
            } else {
                $cursor = $this->rekon_buff->find(["is_schedule" => 1, 'id_mitra' => $this->id_mitra], ['limit' => $limit, 'sort' => ['_id' => -1]]);
            }
            
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertRekon($namaRekon, $idRekon, $detailMode, $isSch, $timeSch, $idChannel, $tanggalRekon, $id_mitra) {
        try {
            $insertOneResult = $this->rekon_buff->insertOne([
                'id_rekon' => $idRekon,
                'nama_rekon' => $namaRekon,
                'kolom_compare' => array(),
                'kolom_sum' => array(),
                'clean_rule' => array(),
                'is_proses' => "",
                'timestamp' => date("Y-m-d H:i:s"),
                'timestamp_complete' => "-",
                'detail_mode' => $detailMode,
                'is_schedule' => $isSch,
                'detail_schedule' => (object) array(
                    'time' => $timeSch
                ),
                'id_channel' => $idChannel,
                'id_mitra' => $id_mitra,
                'tanggal_rekon' => $tanggalRekon,
                'delimiter' => ''
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
                ['id_rekon' => (int) $id],
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
            $result = $this->rekon_buff->updateOne(
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
            $result = $this->rekon_buff->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

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
            $result = $this->rekon_buff->updateOne(
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
            $result = $this->rekon_buff->updateOne(
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


    function getNextSequenceRekon(){     
        $ret = $this->rekon_buff_seq->findOneAndUpdate(
            array("_id" => "id_rekon"),
            array('$inc' => array("seq" => 1)),
            array("new" => true, "upsert" => true)
        );
        return $ret->seq;
    }
    
    function insertRekonUnmatchBulanan($dataRekon) {
        try {
            $insertOneResult = $this->rekon_buff->insertOne($dataRekon);
            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }


    /* Rekon Sch */
    function insertRekonSch($dataRekon) {
        try {
            $insertOneResult = $this->rekon_buff->insertOne($dataRekon);
            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

}