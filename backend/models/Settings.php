<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Settings".
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
class Settings extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['name', 'value'], 'required']
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
            'value' => 'Value'
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
    static function GiveLovestarViaConnections(){
        $set=Settings::findOne(['name' => 'give_lovestar_via_connection']);
        if($set == NULL){
            $new_set = new Settings();
            $new_set->name = 'give_lovestar_via_connection';
            $new_set->value = 'true';
            $new_set->save(false);
            return true;
        } else {
            if($set->value === 'true')
                return true;
            else
                return false;
        }
    }
    static function CreateRecords(){//create records to show in settings page
        Settings::GiveLovestarViaConnections();

    }
    static function UpdateSetting($name, $value){
        $set = Settings::findOne(['name' => $name]);
        if($set !== NULL){
            $set->value = $value;
            $set->save(false);
        }
    }
}
