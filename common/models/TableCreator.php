<?php

namespace common\models;
use backend\models\UserConnections;
use backend\models\UsersWithSharedInterests;
use Yii;


class TableCreator
{
    private $db;

    public function __construct()
    {
        $this->db = Yii::$app->db;
        $this->createTables();
        $this->updateTables();
        if(!empty($_GET['test'])){
            Daemon::matchUsersByInterest();
        }
    }

    private function createTables(): void
    {
        $this->userConnections();
        $this->usersWithSharedInterests();
        $this->BotSettings();
    }

    private function updateTables(): void
    {
        $this->userUpdate();
        $this->UsersWithSharedInterestsUpdate();
    }

    // create table methods
    private function userConnections(): void
    {
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
    private function BotSettings(): void
    {
        $query = "
                CREATE TABLE IF NOT EXISTS Settings (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    value VARCHAR(50) NOT NULL
                )
            ";
        $this->db->createCommand($query)->execute();
    }
    private function usersWithSharedInterests(): void
    {
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

    // update table methods
    private function userUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('User');
        if (!isset($table->columns['telegram_alias'])) {
            $this->db->createCommand()->addColumn('User', 'telegram_alias', 'varchar(33) AFTER telegram')->execute();
        }
    }
    private function UsersWithSharedInterestsUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('UsersWithSharedInterests');
        if (!isset($table->columns['need_update'])) {
            $this->db->createCommand()->addColumn('UsersWithSharedInterests', 'need_update', 'int(1) DEFAULT NULL AFTER updated_at')->execute();
        }
    }

}