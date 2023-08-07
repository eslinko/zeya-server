<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "CreativeTypes".
 *
 * @property string $id
 * @property string $type_en
 * @property string $type_ru
 * @property string $type_et
 */
class CreativeTypes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CreativeTypes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['type_en', 'type_ru', 'type_et'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_en' => 'EN',
            'type_ru' => 'RU',
            'type_et' => 'ET',
        ];
    }
}
