<?php

namespace backend\models;

use common\models\User;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "UsersWithSharedInterests".
 *
 * @property string $id
 * @property string $user_id_1
 * @property string $user_id_2
 * @property string $shared_interests
 * @property string $created_at
 * @property string $updated_at
 */
class UsersWithSharedInterests extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UsersWithSharedInterests';
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
            'id' => 'ID',
            'user_id_1' => 'User #1',
            'user_id_2' => 'User #2',
            'shared_interests' => 'Shared interests',
            'created_at' => 'Creation date',
            'updated_at' => 'Updated at date'
        ];
    }
    static function getUserWithSharedInterests($user_id) {
        $users = UsersWithSharedInterests::find()
            ->where(['user_id_1' => $user_id])
            ->orWhere(['user_id_2' => $user_id])
            ->all();

        $result = [];
        foreach ($users as $userWithInterests) {
            $id = $userWithInterests->user_id_1 === $user_id ? $userWithInterests->user_id_2 : $userWithInterests->user_id_1;

            $user = User::findOne(['id' => $id]);

            $username = '';
            if(!empty($user)) {
                if(!empty($user->publicAlias)) {
                    $username = $user->publicAlias;
                } else if ($user->full_name) {
                    $username = $user->full_name;
                } else if ($user->username) {
                    $username = $user->username;
                }
            }

            $result[] = [
                'id' => $userWithInterests->id,
                'user_id' => $id,
                'username' => $username,
                'created_at' => $userWithInterests->created_at,
                'created_at_timestamp' => strtotime($userWithInterests->created_at)
            ];
        }
        return $result;
    }

    static function setUserWithSharedInterests($user_id_1, $user_id_2, $shared_interests){
        $new_connection = new UsersWithSharedInterests();
        $new_connection->user_id_1 = $user_id_1;
        $new_connection->user_id_2 = $user_id_2;
        $new_connection->shared_interests = $shared_interests;
        if($new_connection->save(false)){
            return ['status' => 'success'];
        }
        else {
            return ['status' => 'error'];
        }
    }

    static function setMockupDataForUser($user_id){
        $users_mockup_ids = range(1, 10);
        shuffle($users_mockup_ids);
        foreach ($users_mockup_ids as $key => $id) {
            if($key % 2 == 0){
                self::setUserWithSharedInterests($user_id, $id, 'test123');
            } else {
                self::setUserWithSharedInterests($id, $user_id, 'test123');
            }
        }
    }
}