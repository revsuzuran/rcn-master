<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonMatch {
    private $rekon_match;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->rekon_match = $database->rekon_match;
    }

    function getRekons($limit = 10) {
        try {
            $cursor = $this->rekon_match->find([], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id) {
        try {
            $rekon = $this->rekon_match->find(['id_rekon' => (int) $id]);
            $rekon = $rekon->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonAll($id_rekon, $id_rekon_result, $tipe, $limit = 0) {
        try {
            $cursor = $this->rekon_match->find(['id_rekon' => (int) $id_rekon, 'id_rekon_result' => (int) $id_rekon_result, 'tipe' => (int) $tipe], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }

    function deleteRekonMany($id_rekon_result) {
        try {
            $result = $this->rekon_match->deleteMany(['id_rekon_result' => $id_rekon_result]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $ex->getMessage(), 500);
        }
    }    
    
}