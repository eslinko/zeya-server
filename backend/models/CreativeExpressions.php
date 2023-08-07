<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "CreativeExpressions".
 *
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $content
 * @property string $description
 * @property string $tags
 * @property string $active_period
 * @property string $status
 * @property string $upload_date
 */
class CreativeExpressions extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CreativeExpressions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'content', 'description', 'tags', 'active_period', 'status', 'upload_date'], 'required'],
            [['type', 'tags'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'type' => 'Type',
            'content' => 'Content',
            'description' => 'Description',
            'tags' => 'Tags',
            'active_period' => 'Active Period',
            'status' => 'Status',
            'upload_date' => 'Upload Date',
        ];
    }
}
