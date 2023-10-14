<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "MatchAction".
 *
 * @property string $id
 * @property string $action_user_id
 * @property string $expression_user_id
 * @property string $action_result
 * @property string $timestamp
 */
class MatchAction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MatchAction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['action_user_id','expression_id','expression_user_id','action_result'], 'required'],

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
            'action_user_id' => 'Action user id',
            'expression_user_id' => 'Owner of expression',
            'expression_id' => 'Expression id',
            'action_result' => 'Like',
            'timestamp' => 'Action date',
        ];
    }
    static function doesActionExist($user_id, $expression_id){
        $action = MatchAction::find()->where(['action_user_id' => $user_id])->andWhere(['expression_id' => $expression_id])->one();
        if($action === NULL)
            return false;
        else
            return true;
    }
    static function addAction($action_user_id, $expression_id, $expression_user_id, $action_result){
        $new_action = MatchAction::find()->where(['action_user_id' => $action_user_id,'expression_id' => $expression_id, 'expression_user_id' => $expression_user_id])->one();
        if($new_action === NULL) {
            $new_action = new MatchAction();
        }
        $new_action->action_user_id = $action_user_id;
        $new_action->expression_id = $expression_id;
        $new_action->expression_user_id = $expression_user_id;
        $new_action->action_result = $action_result;
        if($new_action->save(false)){
            return ['status' => true];
        }
        else{
            return ['status' => false];
        }
    }
    static function didUserLikedAnyOfOursExpression($user_id_1, $user_id_2){
        //did user user_id_2 liked any expression of user_id_1?
        $res = MatchAction::find()->where(['action_user_id' => $user_id_2, 'expression_user_id' => $user_id_1])->one();
        /*if($res === NULL)
            return false;
        else
            return true;*/
        return $res;
    }
}
