<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonUnmatch {
    private $rekon_unmatch;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->rekon_unmatch = $database->rekon_unmatch;
    }

    function getRekons($limit = 10) {
        try {
            $cursor = $this->rekon_unmatch->find([], ['limit' => $limit]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id) {
        try {
            $rekon = $this->rekon_unmatch->find(['id_rekon' => (int) $id]);
            $rekon = $rekon->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getRekonAll($id_rekon,$tipe) {
        try {
            $cursor = $this->rekon_unmatch->find(['id_rekon' => (int) $id_rekon, 'tipe' => (int) $tipe], ['limit' => 0]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }
    
}