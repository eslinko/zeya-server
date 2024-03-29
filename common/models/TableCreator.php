<?php

namespace common\models;
use app\models\PartnerRuleAction;
use backend\models\UserConnections;
use backend\models\UsersWithSharedInterests;
use app\models\Partner;
use app\models\PartnerRule;
use Yii;


class TableCreator
{
    private $db;

    public function __construct()
    {
        $this->db = Yii::$app->db;
        $this->createTables();
        $this->updateTables();
//        if(!empty($_GET['test'])){
//            Daemon::matchUsersByInterest();
//        }
        // create partners
        if(!empty($_GET['create-partner'])) {
            Partner::createVHPartner();
            PartnerRule::createRuleRegistrationForViralHelp();
        }
    }

    private function createTables(): void
    {
        $this->userConnections();
        $this->usersWithSharedInterests();
        $this->BotSettings();
        $this->MatchAction();
        $this->Matches();
        $this->Notifications();
        $this->UserInterestsAnswers();
        $this->LovestarEmissions();
        $this->creativeExpressions();
        $this->invitationCodes();
        $this->invitationCodesLogs();
    }

    private function updateTables(): void
    {
        $this->userUpdate();
        $this->partnerUpdate();
        $this->partnerRuleActionUpdate();
        $this->connectionsUpdate();
        $this->UsersWithSharedInterestsUpdate();
        $this->creativeExpressionsUpdate();
        $this->invitationCodesUpdate();
        $this->lovestarUpdate();
        $this->InvitationCodesLogsUpdate();
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
    private function UserInterestsAnswers(): void
    {
        $query = "        
        CREATE TABLE IF NOT EXISTS UserInterestsAnswers (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL REFERENCES User(id) ON DELETE CASCADE,
            question_type ENUM ('TIME_TRAVEL','UNLIMITED_ISLAND','MAGIC_WISH', 'INTEREST_FESTIVAL', 'LIFE_BOOK') NOT NULL,
            response TEXT NOT NULL
        );                
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
    private function Notifications(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS Notifications (
                id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                type ENUM('CONNECTION_REQUEST', 'CONNECTION_ACCEPTED', 'CONNECTION_REJECTED', 'NEW_MATCH', 'INVITE_CODE_USED', 'INVITE_CODE_USED_CONNECTIONS','INVITE_CODE_UNUSED_REMINDER','CE_EXPIRATION_WARNING') NOT NULL,
                related_entity_id INT(11) DEFAULT NULL,
                message_code VARCHAR(255) NOT NULL,
                params TEXT,
                read_status BOOLEAN NOT NULL DEFAULT FALSE,
                created_at int(11) NOT NULL,
                FOREIGN KEY (user_id) REFERENCES User (id)
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
        if (!isset($table->columns['notify_connections'])) {
            $this->db->createCommand()->addColumn('User', 'notify_connections', 'BOOLEAN NOT NULL DEFAULT TRUE')->execute();
        }
        if (!isset($table->columns['notify_matches'])) {
            $this->db->createCommand()->addColumn('User', 'notify_matches', 'BOOLEAN NOT NULL DEFAULT TRUE')->execute();
        }
        if (!isset($table->columns['notify_invite_codes'])) {
            $this->db->createCommand()->addColumn('User', 'notify_invite_codes', 'BOOLEAN NOT NULL DEFAULT TRUE')->execute();
        }
        if (!isset($table->columns['notify_ce_activity'])) {
            $this->db->createCommand()->addColumn('User', 'notify_ce_activity', 'BOOLEAN NOT NULL DEFAULT TRUE')->execute();
        }
        if (!isset($table->columns['last_notification_read_time'])) {
            $this->db->createCommand()->addColumn('User', 'last_notification_read_time', 'DATETIME DEFAULT NULL')->execute();
        }
        if (!isset($table->columns['message_counter'])) {//counter for internal general use
            $this->db->createCommand()->addColumn('User', 'message_counter', 'SMALLINT DEFAULT 0')->execute();
        }
        if (!isset($table->columns['profile_data'])) {//bio, social networks etc
            $this->db->createCommand()->addColumn('User', 'profile_data', 'TEXT')->execute();
        }
        if (!isset($table->columns['lovedo_votes'])) {//8 per months
            $this->db->createCommand()->addColumn('User', 'lovedo_votes', 'TINYINT DEFAULT 8')->execute();
        }

        if (!isset($table->columns['invitation_code_id'])) {//bio, social networks etc
            $this->db->createCommand()->addColumn('User', 'invitation_code_id', 'int DEFAULT 0 AFTER verificationCode')->execute();
        }


    }
    private function partnerUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('Partner');
        if (!isset($table->columns['authHash'])) {
            $this->db->createCommand()->addColumn('Partner', 'authHash', 'varchar(255) AFTER billingDetails')->execute();
        }
    }
    private function partnerRuleActionUpdate(): void
    {
        $this->db->createCommand('ALTER TABLE `PartnerRuleAction` CHANGE `emittedLovestarsUser` `emittedLovestarsUser` INT NULL DEFAULT NULL')->execute();
    }
    private function lovestarUpdate(): void
    {
        $this->db->createCommand('ALTER TABLE `Lovestar` CHANGE `currentOwner` `currentOwner` INT NULL DEFAULT NULL')->execute();
    }
    private function connectionsUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('UserConnections');
        if (!isset($table->columns['attempts'])) {
            $this->db->createCommand()->addColumn('UserConnections', 'attempts', 'SMALLINT DEFAULT 0')->execute();
        }
    }
    private function UsersWithSharedInterestsUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('UsersWithSharedInterests');
        if (!isset($table->columns['need_update'])) {
            $this->db->createCommand()->addColumn('UsersWithSharedInterests', 'need_update', 'int(1) DEFAULT NULL AFTER updated_at')->execute();
        }
    }

    private function invitationCodesUpdate(): void
    {
        $table = $this->db->schema->getTableSchema('InvitationCodes');
        if (!isset($table->columns['ruleActionId'])) {
            $this->db->createCommand()->addColumn('InvitationCodes', 'ruleActionId', 'int(1) DEFAULT NULL AFTER registered_user_id')->execute();
        }
    }

    private function creativeExpressionsUpdate(): void
    {
        $this->db->createCommand('ALTER TABLE `CreativeExpressions` CHANGE `upload_date` `upload_date` INT NULL DEFAULT NULL')->execute();

        $table = $this->db->schema->getTableSchema('CreativeExpressions');
        if (!isset($table->columns['functionalType'])){
            $this->db->createCommand("ALTER TABLE CreativeExpressions ADD functionalType ENUM('LoveDO') DEFAULT NULL;")->execute();
        }
        if(!isset($table->columns['value_giver_id'])) {
            $this->db->createCommand("ALTER TABLE CreativeExpressions
ADD COLUMN value_giver_id int(11) DEFAULT NULL,
ADD FOREIGN KEY (value_giver_id) REFERENCES User(id);")->execute();
        }
    }
    private function InvitationCodesLogsUpdate(): void
    {
        $type=$this->db->schema->getTableSchema('InvitationCodesLogs')->columns['inserted_code']->dbType;
        if(strcasecmp($type,'tinytext') != 0)
            $this->db->createCommand()->alterColumn('InvitationCodesLogs','inserted_code','TINYTEXT DEFAULT NULL')->execute();
    }
    private function MatchAction(): void
    {
        $query = "
                CREATE TABLE IF NOT EXISTS MatchAction (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    action_user_id INT NOT NULL,
                    expression_id INT NOT NULL,
                    expression_user_id INT NOT NULL,
                    action_result TINYINT(1) NOT NULL,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
        $this->db->createCommand($query)->execute();
    }
    private function Matches(): void
    {
        $query = "
                CREATE TABLE IF NOT EXISTS LovestarEmissions (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    lovestar_id INT NOT NULL,
                    creative_expression_id INT NOT NULL,
                    voter_id INT NOT NULL,
                    vote_timestamp INT DEFAULT NULL
                )
            ";
        $this->db->createCommand($query)->execute();
    }

    private function LovestarEmissions(): void
    {
        $query = "
                CREATE TABLE IF NOT EXISTS Matches (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_1_id INT NOT NULL,
                    user_2_id INT NOT NULL,
                    user_1_telegram BIGINT NOT NULL,
                    user_2_telegram BIGINT NOT NULL,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
        $this->db->createCommand($query)->execute();
    }
    private function creativeExpressions(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS CreativeExpressions (
            id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            user_id int(11) DEFAULT NULL,
            type_enum enum('Audio','Video','Text','Image','URL') DEFAULT NULL,
            content mediumtext DEFAULT NULL,
            description mediumtext DEFAULT NULL,
            tags varchar(9000) DEFAULT NULL,
            active_period int(11) DEFAULT NULL,
            status varchar(50) DEFAULT NULL,
            upload_date int(11) DEFAULT NULL,
            ContentUploadUrl varchar(1024) DEFAULT NULL) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $this->db->createCommand($query)->execute();
    }
    private function invitationCodes(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS InvitationCodes (
          id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
          user_id int(11) DEFAULT NULL,
          code varchar(11) UNIQUE KEY DEFAULT NULL,
          registered_user_id int(11) DEFAULT NULL,
          ruleActionId int(1) DEFAULT NULL,
          signup_date mediumtext DEFAULT NULL) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin";
        $this->db->createCommand($query)->execute();
    }
    private function invitationCodesLogs(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS InvitationCodesLogs (
          id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
          timestamp mediumtext DEFAULT NULL,
          user_id mediumtext DEFAULT NULL,
          inserted_code tinytext DEFAULT NULL,
          error_type mediumtext DEFAULT NULL) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin";
        $this->db->createCommand($query)->execute();
    }
}