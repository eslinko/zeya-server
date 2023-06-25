<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "TeacherOutcome".
 *
 * @property string $id
 * @property integer $teacherId
 * @property string $type
 * @property string $title
 * @property string $description
 * @property string $hashtags
 * @property integer $valueInLovestarsFrom
 * @property integer $valueInLovestarsTo
 */
class TeacherOutcome extends ActiveRecord
{
	
	static $types = ['1' => 'Tangible', '2' => 'Intangible'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TeacherOutcome';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['teacherId', 'type', 'title', 'valueInLovestarsFrom', 'valueInLovestarsTo'], 'required'],
          [['description'], 'string'],
          [['hashtags'], 'safe'],
          [['valueInLovestarsFrom', 'valueInLovestarsTo'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacherId' => 'Teacher',
            'type' => 'Type',
            'title' => 'Title',
            'description' => 'Description',
            'hashtags' => 'Hashtags',
            'valueInLovestarsFrom' => 'Value in Lovestars From',
            'valueInLovestarsTo' => 'Value in Lovestars To',
        ];
    }
	
	static function filterTeacherOutcome($params = []){
		$query = TeacherOutcome::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		$allOutcomes = array_column(TeacherOutcome::find()->all(), 'hashtags', 'id');
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'hashtags':
					$ids_with_hashtags = [];
					
					foreach ($allOutcomes as $id => $hashtags) {
						if(!empty(array_intersect($value, explode(',', $hashtags)))) $ids_with_hashtags[] = $id;
					}
					
					$query = $query->andWhere(['in', 'id', $ids_with_hashtags]);
					break;
				case 'title':
					$query = $query->andWhere(['like', $param, '%' . $value . '%', false]);
					break;
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}
		
		return $query;
	}
}
