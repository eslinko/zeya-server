<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "UserInterestsAnswers".
 *
 * @property string $id
 * @property string $user_id
 * @property string $question_type
 * @property string $response
 */
class UserInterestsAnswers extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserInterestsAnswers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['id','user_id','question_type','response'], 'required']
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
            'question_type' => 'question_type',
            'response' => 'response'
        ];
    }

    public static function setUserInterestsAnswers($user_id, $question_type, $answer) {

        $ans = UserInterestsAnswers::find()->where(['id' => $user_id, 'question_type' => $question_type])->one();
        if($ans === NULL){
            $ans = new UserInterestsAnswers();
            $ans->user_id = $user_id;
            $ans->question_type = $question_type;
        }
        $ans->response = $answer;

        if($ans->save(false)){
            return ['status' => true];
        }
        else{
            return ['status' => false];
        }
    }
    static function getUserInterestsAnswers($user_id) {
        return UserInterestsAnswers::find()->where(['user_id' => $user_id])->all();
    }
}
