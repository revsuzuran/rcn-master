<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class ChannelModel
{
    private $login;

    function __construct()
    {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->channel = $database->channel_data;
    }

    function getAllChannel($id) {
        try {
            $cursor = $this->channel->find(['id_mitra' => (int) $id], ['limit' => 0]);
            $usr = $cursor->toArray();

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekons: ' . $ex->getMessage(), 500);
        }
    }

    function getChannel($id) {
        try {
            $usr = $this->channel->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $usr;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching rekon with ID: ' . $ex->getMessage(), 500);
        }
    }

    function saveChannel($data) {
        try {
            $insertOneResult = $this->channel->insertOne($data);
            if($insertOneResult->getInsertedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while creating a channel: ' . $ex->getMessage(), 500);
        }
    }

    function updateChannel($id, $data) {
        try {
            $result = $this->channel->updateOne(
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

    function deleteChannel($id) {
        try {
            $result = $this->channel->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if($result->getDeletedCount() == 1) {
                return true;
            }

            return false;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting a rekon with ID: ' . $id . $ex->getMessage(), 500);
        }
    }
}