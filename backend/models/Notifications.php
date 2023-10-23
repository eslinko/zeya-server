<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Notifications".
 *
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $related_entity_id
 * @property string $message_code
 * @property string $params
 * @property string $read_status
 * @property string $created_at
 */
class Notifications extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['user_id','type','related_entity_id','message_code','params','read_status'], 'required'],

          [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user_id',
            'type' => 'type',
            'related_entity_id' => 'related_entity_id',
            'message_code' => 'message_code',
            'params' => 'params',
            'read_status' => 'read_status',
            'created_at' => 'created_at',
        ];
    }

    static function setAsRead($id){
        $not = Notifications::find()->where(['id' => $id])->one();
        if($not === NULL) {
            return false;
        }
        $not->read_status = true;
        if($not->save(false)){
            return true;
        }
        else{
            return false;
        }
    }

    static function deleteOne($id){
        $not = Notifications::find()->where(['id' => $id])->one();
        if($not === NULL) {
            return false;
        }
        $not->delete();
        return true;
    }
    static function unreadCount($id){
        $not = Notifications::find()->where(['user_id' => $id, 'read_status' => false])->all();
        return count($not);
    }
    static function setAllAsRead($id){
        $not = Notifications::find()->where(['user_id' => $id, 'read_status' => false])->all();
        $flag = true;
        foreach ($not as $nt){
            $nt->read_status = true;
            if($nt->save(false) == false){
                $flag = false;
            }
        }
        return $flag;
    }
    static function getDetails($id){
        $not = Notifications::find()->where(['id' => $id])->one();
        return count($not);
    }
}
