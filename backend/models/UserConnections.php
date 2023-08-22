<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "UserConnections".
 *
 * @property string $connection_id
 * @property string $user_id_1
 * @property string $user_id_2
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $attempts
 */
class UserConnections extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserConnections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['user_id_1','user_id_2'], 'required']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'connection_id' => 'ID',
            'user_id_1' => 'Inviting user',
            'user_id_2' => 'Invited user',
            'status' => 'Status',
            'created_at' => 'Creation date',
            'updated_at' => 'Updated at date',
            'attempts' => 'Pending invites send attempts'
        ];
    }

    static function getUserConnections($user_id) {
        $connections = UserConnections::find()->where(['user_id_1' => $user_id,'status'=>'accepted'])->all();
        $result = [];
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_2]);

            $username = '';
            if(!empty($user)) {
                $username = $user->publicAlias;
                if(empty($username)){
                    $username=$user->full_name;
                    if(empty($username)){
                        $username=$user->username;
                    }
                }
            }

            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_2,
                'created_on' => $con->updated_at,
                'username' => $username,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias
            ];
        }
        $connections = UserConnections::find()->where(['user_id_2' => $user_id,'status'=>'accepted'])->all();
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_1]);
            $username = '';
            if(!empty($user)) {
                $username = $user->publicAlias;
                if(empty($username)){
                    $username=$user->full_name;
                    if(empty($username)){
                        $username=$user->username;
                    }
                }
            }

            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_1,
                'created_on' => $con->updated_at,
                'username' => $username,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias
            ];
        }
        return $result;
    }

    static function getUserSentInvites($user_id){
        $connections =  UserConnections::find()->where(['user_id_1' => $user_id,'status'=>'declined'])->orWhere(['user_id_1' => $user_id,'status'=>'pending'])->all();
        $result = [];
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_2]);

            $username = '';
            if(!empty($user)) {
                $username = $user->publicAlias;
                if(empty($username)){
                    $username=$user->full_name;
                    if(empty($username)){
                        $username=$user->username;
                    }
                }
            }
            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_2,
                'updated_at' => $con->updated_at,
                'username' => $username,
                'status' => $con->status,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias
            ];
        }
        return $result;
    }
    static function getUserSentPendingInvites($user_id){
        $connections =  UserConnections::find()->where(['user_id_1' => $user_id,'status'=>'pending'])->all();
        $result = [];
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_2]);
            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_2,
                'updated_at' => $con->updated_at,
                'status' => $con->status,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias,
                'attempts' => $con->attempts
            ];
        }
        return $result;
    }

    static function getUserRejectedInvites($user_id){
        $connections =  UserConnections::find()->where(['user_id_2' => $user_id,'status'=>'declined'])->all();
        $result = [];
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_1]);

            $username = '';
            if(!empty($user)) {
                $username = $user->publicAlias;
                if(empty($username)){
                    $username=$user->full_name;
                    if(empty($username)){
                        $username=$user->username;
                    }
                }
            }
            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_1,
                'updated_at' => $con->updated_at,
                'username' => $username,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias
            ];
        }
        return $result;
    }
    static function getUserPendingInvites($user_id){
        $connections =  UserConnections::find()->where(['user_id_2' => $user_id,'status'=>'pending'])->all();
        $result = [];
        foreach ($connections as $con) {
            $user=User::findOne(['id' => $con->user_id_1]);

            $result[] = [
                'connection_id' => $con->connection_id,
                'user_id' => $con->user_id_1,
                'updated_at' => $con->updated_at,
                'public_alias' => $user->publicAlias,
                'telegram_alias' => $user->telegram_alias
            ];
        }
        return $result;
    }
    static function setUserConnection($user_id_1,$user_id_2, $status = 'pending'){
        $new_connection = UserConnections::find()->where(['user_id_2' => $user_id_2,'user_id_1' => $user_id_1])->orWhere(['user_id_2' => $user_id_1,'user_id_1' => $user_id_2])->one();
        if($new_connection===NULL) {
            $new_connection = new UserConnections();
        }
        $new_connection->user_id_1 = $user_id_1;
        $new_connection->user_id_2 = $user_id_2;
        $new_connection->status = $status;
        $new_connection->attempts = 1;
        if($new_connection->save(false)){
            return ['status' => 'success'];
        }
        else{
            return ['status' => 'error'];
        }
    }

    static function IncrementUserSentPendingInvitation($user_id_1, $user_id_2)
    {
        $connection = UserConnections::find()->where(['user_id_2' => $user_id_2,'user_id_1' => $user_id_1])->one();
        if($connection===NULL) {
            return ['status' => 'error'];
        }
        $connection->attempts = intval($connection->attempts)+1;
        if($connection->save(false)){
            return ['status' => 'success'];
        }
        else{
            return ['status' => 'error'];
        }
    }
    static function DeleteUserConnection($connection_id){
        $connection=UserConnections::findOne(['connection_id' => $connection_id]);
        if($connection===NULL) return ['status' => 'error'];
        if($connection->delete()===false)
            return ['status' => 'error'];
        else
            return ['status' => 'success'];
    }
    static function AcceptUserConnectionRequest($user_id_1,$user_id_2){
        $connection=UserConnections::findOne(['user_id_1' => $user_id_1,'user_id_2' => $user_id_2]);
        if($connection===NULL) return ['status' => 'error'];
        $connection->status='accepted';
        if($connection->save(false)){
            return ['status' => 'success'];
        }
        else{
            return ['status' => 'error'];
        }
    }
    static function DeclineUserConnectionRequest($user_id_1,$user_id_2){
        $connection=UserConnections::findOne(['user_id_1' => $user_id_1,'user_id_2' => $user_id_2]);
        if($connection===NULL) return ['status' => 'error'];
        $connection->status='declined';
        if($connection->save(false)){
            return ['status' => 'success'];
        }
        else{
            return ['status' => 'error'];
        }
    }
    static function CheckUserConnection($user_id_1,$user_id_2){
        $connection=UserConnections::findOne(['user_id_1' => $user_id_1,'user_id_2' => $user_id_2]);
        if($connection===NULL){
            $connection=UserConnections::findOne(['user_id_1' => $user_id_2,'user_id_2' => $user_id_1]);
        }
        return $connection;
    }

    static function GetUserSentPendingInvitation($user_id_1,$user_id_2){
        $connection=UserConnections::find()->where(['user_id_1' => $user_id_1,'user_id_2' => $user_id_2])->asArray()->one();
        if($connection===NULL){
            return ['status' => 'error'];
        }
        return ['status' => 'success', 'connection' => $connection];
    }

    static function getUserSecondaryUser($user_id) {
        $connections = self::getUserConnections($user_id);
        if(empty($connections)) return [];

        $result = [];
        foreach ($connections as $connection) {
            $secondary_users = self::getUserConnections($connection['user_id']);
            foreach ($secondary_users as $secondary_user) {
                if($secondary_user['user_id'] === $user_id) {
                    continue;
                }
                $result[] = [
                    'user_id' => $secondary_user['user_id'],
                    'connection_id' => $secondary_user['connection_id'],
                    'created_at_timestamp' => strtotime($secondary_user['created_on']),
                ];
            }
        }

        return $result;
    }

    static function setMockupDataForUser($user_id){
        $connections_mockup_ids = range(1, 10);
        shuffle($connections_mockup_ids);
        foreach ($connections_mockup_ids as $key => $id) {
            if($key % 2 == 0){
                self::setUserConnection($user_id, $id, );
            } else {
                self::setUserConnection($id, $user_id);
            }
        }

        $secondary_user_mockup_ids = range(11, 25);
        shuffle($secondary_user_mockup_ids);
        foreach ($secondary_user_mockup_ids as $key => $id) {
            if($key % 2 == 0){
                self::setUserConnection($connections_mockup_ids[array_rand($connections_mockup_ids)], $id);
            } else {
                self::setUserConnection($id, $connections_mockup_ids[array_rand($connections_mockup_ids)]);
            }
        }
    }

}

