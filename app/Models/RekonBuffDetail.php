<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class RekonBuffDetail {
    private $rekon_buff_header;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->rekon_buff_detail = $database->rekon_buff_detail;
        $this->rekon_buff_header = $database->rekon_buff_header;
    }

    function getRekons($id_rekon, $tipe, $limit = 0) {
        try {
            $cursor = $this->rekon_buff_detail->find(['id_rekon' => $id_rekon, "tipe" => $tipe], ['limit' => $limit]);
            $rekons = $cursor->toArray();

            return $rekons;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getRekon($id) {
        try {
            $rekon = $this->rekon_buff_detail->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function insertRekon($title, $author, $pages) {
        try {
            $insertOneResult = $this->rekon_buff_detail->insertOne([
                'title' => $title,
                'author' => $author,
                'pages' => $pages,
                'pagesRead' => 0,
            ]);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function insertRekonMany($data) {
        try {
            $insertOneResult = $this->rekon_buff_detail->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function updateRekon($id, $title, $author, $pagesRead) {
        try {
            $result = $this->rekon_buff_detail->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'title' => $title,
                    'author' => $author,
                    'pagesRead' => $pagesRead,
                ]]
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
            $result = $this->rekon_buff_detail->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }    

    function deleteRekonMany($id, $tipe) {
        try {
            $result = $this->rekon_buff_detail->deleteMany(['id_rekon' => $id, "tipe" => $tipe]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }    

    function deleteRekonManyHeader($id, $tipe) {
        try {
            $result = $this->rekon_buff_header->deleteMany(['id_rekon' => $id, "tipe" => $tipe]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }    
    
    function insertRekonManyHeader($data) {
        try {
            $insertOneResult = $this->rekon_buff_header->insertMany($data);

            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a rekon: ' . $ex->getMessage(), 500);
        }
    }

    function getHeader($id_rekon, $tipe, $limit = 0) {
        try {
            $cursor = $this->rekon_buff_header->find(['id_rekon' => (int) $id_rekon, "tipe" => $tipe], ['limit' => $limit]);
            $rekons = $cursor->toArray();

            return $rekons;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }
}