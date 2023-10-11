<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Matches".
 *
 * @property string $id
 * @property string $user_1_id
 * @property string $user_2_id
 * @property string $user_1_telegram
 * @property string $user_2_telegram
 * @property string $timestamp
 */
class Matches extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Matches';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['user_1_id','user_2_id','user_1_telegram','user_2_telegram'], 'required'],

          [['timestamp'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_1_id' => 'user_1_id',
            'user_2_id' => 'user_2_id',
            'user_1_telegram' => 'user_1_telegram',
            'user_2_telegram' => 'user_2_telegram',
            'timestamp' => 'Action date',
        ];
    }

    static function addMatch($user_1_id, $user_2_id){
        $new_match = Matches::find()->where(['user_1_id' => $user_1_id,'user_2_id' => $user_2_id])->orWhere(['user_1_id' => $user_2_id,'user_2_id' => $user_1_id])->one();
        if($new_match === NULL) {
            $new_match = new Matches();
        } else {
            return ['status' => true];
        }
        $new_match->user_1_id = $user_1_id;
        $new_match->user_2_id = $user_2_id;
        $us = User::find()->where(['id' => $user_1_id])->one();
        if($us === NULL) return ['status' => false];
        $new_match->user_1_telegram = $us->telegram;
        $us = User::find()->where(['id' => $user_2_id])->one();
        if($us === NULL) return ['status' => false];
        $new_match->user_2_telegram = $us->telegram;
        if($new_match->save(false)){
            return ['status' => true];
        }
        else{
            return ['status' => false];
        }
    }

    static function getUserMatches($user_id) {
        $matches = Matches::find()->where(['user_1_id' => $user_id])->orWhere(['user_2_id' => $user_id])->all();
        $result = [];
        foreach ($matches as $mat) {
            if($user_id == $mat->user_2_id)
                $user=User::findOne(['id' => $mat->user_1_id]);
            else
                $user=User::findOne(['id' => $mat->user_2_id]);
            if($user === NULL) continue;

            $result[] = [
                'user' => $user,
                'timestamp' => $mat->timestamp
            ];
        }

        return $result;
    }
}
