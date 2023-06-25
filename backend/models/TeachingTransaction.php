<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "TeachingTransaction".
 *
 * @property string $id
 * @property string $timestamp
 * @property string $userGivingLovestars
 * @property string $teacherGivingValue
 * @property string $lovestars
 */
class TeachingTransaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TeachingTransaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['timestamp', 'userGivingLovestars', 'teacherGivingValue', 'lovestars'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timestamp' => 'Date',
            'userGivingLovestars' => 'Buyer',
            'teacherGivingValue' => 'Teacher Good',
            'lovestars' => 'Lovestars',
        ];
	}
	
	static function filterTeachingTransaction($params = []){
		$query = TeachingTransaction::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}
		
		return $query;
	}
}
