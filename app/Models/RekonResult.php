<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonResult {
    private $rekon_result;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->session = session();
        $this->id_mitra = $this->session->get('id_mitra');
        $this->rekon_result = $database->rekon_result;
    }

    function getRekons($limit = 0) {
        try {
            $desc = -1;
            if ($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ]], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            } else {
                $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ], "id_mitra" => (int) $this->id_mitra], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            }
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id, $id_rekon_result) {
        try {
            $rekon = $this->rekon_result->find(['id_rekon' => (int) $id, "id_rekon_result" => (int) $id_rekon_result]);
            $rekon = $rekon->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonAll($limit = 10) {
        try {
            if ($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_result->find([], ['limit' => $limit]);
            } else {
                $cursor = $this->rekon_result->find(['id_mitra' => (int) $this->id_mitra], ['limit' => $limit]);
            }
          
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }

    function insertRekon($data) {
        try {
            $insertOneResult = $this->rekon_result->insertOne($data);
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

    function updateRekonResult($id, $data) {
        try {
            $result = $this->rekon_result->updateOne(
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


    /* Settlement */
    function getRekonSetlementAll($limit = 0) {
        try {
            $desc = -1;
            // if ($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ], "is_ready_disburse" => (int) 1], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            // } else {
            //     $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ], "is_ready_disburse" => (int) 1, "id_mitra" => (int) $this->id_mitra], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            // }
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekonSetlement($id_rekon_result) {
        try {
            $rekon = $this->rekon_result->find(["id_rekon_result" => (int) $id_rekon_result]);
            $rekon = $rekon->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }


    /* Disburse */
    function getRekonDisburseAll($limit = 0) {
        try {
            $desc = -1;
            // if ($this->session->has('masukAdmin')) {
                $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ], "is_ready_disburse" => (int) 2, "is_settlement" => (int) 1], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            // } else {
            //     $cursor = $this->rekon_result->find(["id_rekon_result" => [ '$exists' => true ], "is_ready_disburse" => (int) 2, "is_settlement" => (int) 1, "id_mitra" => (int) $this->id_mitra], ['limit' => $limit, 'sort' => ['_id' => -1] ]);
            // }
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }
    
}