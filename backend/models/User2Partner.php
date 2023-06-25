<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "User2Partner".
 *
 * @property string $id
 * @property string $userId
 * @property string $partnerId
 */
class User2Partner extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'User2Partner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['userId', 'partnerId'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
	
	static function getPartnersByUserId($userId) {
		return Partner::find()->where(['in', 'id', array_column(User2Partner::find()->where(['userId' => $userId])->all(), 'partnerId')])->all();
	}
}
