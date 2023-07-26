<?php

namespace common\models;
use backend\models\UserConnections;
use backend\models\UsersWithSharedInterests;
use Yii;

class TableCreator {
    private $db;

    public function __construct() {
        $this->db = Yii::$app->db;
        $this->createTables();
    }

    private function createTables(): void {
        $this->userConnections();
        $this->usersWithSharedInterests();
    }

    private function userConnections(): void {
        $query = "
                CREATE TABLE IF NOT EXISTS UserConnections (
                    connection_id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id_1 INT NOT NULL,
                    user_id_2 INT NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
        $this->db->createCommand($query)->execute();
    }

    private function usersWithSharedInterests(): void {
        $query = "
                CREATE TABLE IF NOT EXISTS UsersWithSharedInterests (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id_1 INT NOT NULL,
                    user_id_2 INT NOT NULL,
                    shared_interests TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                );
            ";
        $this->db->createCommand($query)->execute();
    }
}