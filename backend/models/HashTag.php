<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "HashTag".
 *
 * @property string $id
 * @property string $name
 */
class HashTag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'HashTag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
	
	static function fromIdsToNames($ids) {
		$ids = !is_array($ids) ? explode(',', $ids) : $ids;
		return implode(', ', array_column(HashTag::find()->where(['in', 'id', $ids])->all(), 'name'));
	}

    static function fromArrayToHashtags($hashtags_arr) {
        $result = [];
        foreach ($hashtags_arr as $hashtag_item) {
            $hashtag_name = trim($hashtag_item);
            $finded_hashtag = HashTag::find()->where(['name' => $hashtag_name])->one();

            if(empty($finded_hashtag)) {
                $new_hash = new HashTag();
                $new_hash->name = $hashtag_name;
                $new_hash->save();
                $result[] = $new_hash->id;
            } else $result[] = $finded_hashtag->id;
        }

        return $result;
    }
	
	static function filterHashtag($params = []){
		$query = HashTag::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'name':
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
