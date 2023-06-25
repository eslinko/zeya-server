<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Teacher".
 *
 * @property string $id
 * @property string $publicAlias
 * @property string $title
 * @property string $description
 * @property string $hashtags
 * @property string $status
 */
class Teacher extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['title'], 'required'],
          [['description'], 'string'],
          [['hashtags', 'publicAlias', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'hashtags' => 'Hashtags',
            'publicAlias' => 'Public alias',
            'status' => 'Status',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if(empty($this->publicAlias)) {
                $this->publicAlias = bin2hex(random_bytes(20));
            }
            return true;
        }else{
            return false;
        }
    }
	
	static function filterTeacher($params = []){
		$query = Teacher::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		$allTeachers = array_column(Teacher::find()->all(), 'hashtags', 'id');
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'hashtags':
					$ids_with_hashtags = [];
					
					foreach ($allTeachers as $id => $hashtags) {
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
