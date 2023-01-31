<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class DataModels {
    private $ftp;
    private $db;
    private $setting;
    private $mailconf;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->ftp = $database->ftp_data;
        $this->db = $database->db_data;
        $this->setting = $database->setting_data;
        $this->mailconf = $database->email_data;
    }
    
    function getFtp() {
        try {
            $cursor = $this->ftp->find([]);
            $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }
    
    function updateFtp($id, $data) {
        try {
            $result = $this->ftp->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function deleteFtpOne($id) {
        try {
            $result = $this->ftp->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getFtpOne($id) {
        try {
            $rekon = $this->ftp->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            // $rekon = $cursor->toArray();

            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function saveFTP($data) {
        try {
            $result = $this->ftp->insertOne($data);

            if($result->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: '  . $ex->getMessage(), 500);
        }
    }




    /* ============================================= */
    /* CONFIG DATABASE HERE */
    /* ============================================= */

    function getDatabase() {
        try {
            $cursor = $this->db->find([]);
            $rekon = $cursor->toArray();
            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getDatabaseOne($id) {
        try {
            $rekon = $this->db->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function deleteDatabaseOne($id) {
        try {
            $result = $this->db->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function updateDatabase($id, $data) {
        try {
            $result = $this->db->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function saveDatabase($data) {
        try {
            $result = $this->db->insertOne($data);

            if($result->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: '  . $ex->getMessage(), 500);
        }
    }


    /* Settings */
    function getSetting() {
        try {
            $cursor = $this->setting->find([]);
            $rekon = $cursor->toArray();
            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getSettingOne($id) {
        try {
            $setting = $this->setting->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            return $setting;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function saveSetting($data) {
        try {
            $result = $this->setting->insertOne($data);

            if($result->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }


    /* Email Config Setting */
    function getSettingEmail() {
        try {
            $cursor = $this->mailconf->find([]);
            $rekon = $cursor->toArray();
            return $rekon;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function updateSettingEmail($id, $data) {
        try {
            $result = $this->mailconf->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => $data]
            );

            if($result->getModifiedCount()) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

}