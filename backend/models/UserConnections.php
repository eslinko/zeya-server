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
            'updated_at' => 'Updated at date'
        ];
    }
    static function getUserConnections($user_id) {
        $connections = UserConnections::find()->where(['user_id_1' => $user_id])->all();
        $result = [];
        foreach ($connections as $con) {
            $user = User::findOne(['id' => $con->user_id_2]);

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
                'created_on' => $con->created_at,
                'username' => $username
            ];
        }
        $connections = UserConnections::find()->where(['user_id_2' => $user_id])->all();
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
                'created_on' => $con->created_at,
                'username' => $username
            ];
        }

        return $result;
    }
    static function setUserConnection($user_id_1, $user_id_2, $status = 'pending'){
        // $user = User::find()->where(['id' => $user_id_1])->one();
        //if (empty($user)) return ['status' => 'error'];
        $new_connection = new UserConnections();
        $new_connection->user_id_1 = $user_id_1;
        $new_connection->user_id_2 = $user_id_2;
        $new_connection->status = $status;
        $new_connection->save(false);
        if($new_connection->save(false)){
            return ['status' => 'success'];
        }
        else{
            return ['status' => 'error'];
        }
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