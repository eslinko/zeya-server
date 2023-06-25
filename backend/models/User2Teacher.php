<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "User2Teacher".
 *
 * @property string $id
 * @property string $userId
 * @property string $teacherId
 */
class User2Teacher extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'User2Teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['userId', 'teacherId'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
	
	static function getTeacherByUserId($userId) {
		return Teacher::find()->where(['id' => User2Teacher::find()->where(['userId' => $userId])->one()->teacherId])->one();
	}

    static function getAllTeachersByUserId($userId, $status = 'all') {
        $query = Teacher::find()->where(['in', 'id', array_column(User2Teacher::find()->where(['userId' => $userId])->asArray()->all(), 'teacherId')]);
        if($status !== 'all') {
            $query->andWhere(['status' => $status]);
        }

        return $query->asArray()->all();
    }

    static function connectTeacherToUser($user_id, $teacher_id){
        $user = User::find()->where(['id' => $user_id])->one();
        $teacher = Teacher::find()->where(['id' => $teacher_id])->one();

        if(empty($user) || empty($teacher)) return false;

        $user2ColMaybe = User2Teacher::find()->where(['userId' => $user_id])->andWhere(['teacherId' => $teacher_id])->one();
        if(!empty($user2ColMaybe)) return false;

        $user2Col = new User2Teacher();
        $user2Col->userId = $user_id;
        $user2Col->teacherId = $teacher_id;
        $user2Col->save();
    }
}
