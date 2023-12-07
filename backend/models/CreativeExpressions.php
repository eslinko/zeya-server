<?php

namespace backend\models;

use common\models\CurlHelper;
use yii\db\ActiveRecord;
use yii\debug\components\search\matchers\SameAs;
use \yii\helpers\FileHelper;
use Yii;
use yii\helpers\Url;

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

    public static function getCreativeExpressionsByUser($user_id){
        return CreativeExpressions::find()->where(['user_id' => $user_id])->all();
    }

    /**
     * @param $user_id
     * @param $remove_previous
     * @return boolean
     */
    public static function setMockupData($user_id, $remove_previous = false) {
        if($remove_previous) {
            self::removeAllExpressionsByUser($user_id);
        }

        $mockupFiles = FileHelper::findFiles(Yii::getAlias('@webroot').'/assets/images/creative-expressions-mockup/',['recursive' => false]);

        $creativeTypes = CreativeTypes::find()->asArray()->all();
        $creativeTypesCountKeys = count($creativeTypes) - 1;

        foreach ($mockupFiles as $mockupFile){
            $creativeExpression = new CreativeExpressions();
            $creativeExpression->user_id = $user_id;
            $creativeExpression->type = rand(0, $creativeTypesCountKeys);
            $base = Url::base(true);
            $creativeExpression->content = parse_url($base)['scheme'].'://'.parse_url($base)['host'].self::uploadMockupFile($user_id, $mockupFile);
            $creativeExpression->description = 'Test description - ' . rand(0, $creativeTypesCountKeys);
            $creativeExpression->tags = 'Tag 1, Tag2, Taaggg3';
            $creativeExpression->active_period = time() + 3600 * 24;
            $creativeExpression->upload_date = time();
            $creativeExpression->status = 'active';
            $creativeExpression->save(false);
        }

        $urls = ['https://www.w3schools.com/html/pic_trulli.jpg', 'https://example.com/'];

        foreach ($urls as $url){
            $creativeExpression = new CreativeExpressions();
            $creativeExpression->user_id = $user_id;
            $creativeExpression->type = rand(0, $creativeTypesCountKeys);
            $creativeExpression->content = $url;
            $creativeExpression->description = 'Test description 2222 - ' . rand(0, $creativeTypesCountKeys);
            $creativeExpression->tags = 'Tag 3, 55, 612';
            $creativeExpression->active_period = time() + 3600 * 24;
            $creativeExpression->upload_date = time();
            $creativeExpression->status = 'active';
            $creativeExpression->save(false);
        }

        return true;
    }

    /**
     * @param $user_id
     * @param $file
     * @return string
     */
    public static function uploadMockupFile($user_id, $file) {
        $target_dir = dirname(Yii::getAlias('@webroot'),2).'/frontend/web'.'/uploads/creative_expressions/' . $user_id . '/';

        if(!file_exists($target_dir)){
            FileHelper::createDirectory($target_dir);
        }

        $new_file_name = uniqid() . '_' . basename($file);

        $target_file = $target_dir . $new_file_name;

        return copy($file, $target_file) ? '/frontend/web/uploads/creative_expressions/' . $user_id . '/' . $new_file_name : '';
    }

    public static function removeAllExpressionsByUser($user_id){
        FileHelper::removeDirectory(Yii::getAlias('@webroot') . '/uploads/creative_expressions/' . $user_id . '/');
        return CreativeExpressions::deleteAll(['user_id' => $user_id]);
    }

    public static function uploadFileFromTelegram($user_id, $file_id, $supported_formats=NULL) {
        $target_dir = Yii::getAlias('@webroot').'/uploads/creative_expressions/' . $user_id . '/';

        if(!file_exists($target_dir)){
            FileHelper::createDirectory($target_dir);
        }

        // get file path
        $url = "https://api.telegram.org/bot".TelegramBotId."/getFile?file_id={$file_id}";
        $result = json_decode(CurlHelper::curl($url));

        if(!$result->ok) {
            return '';
        }

        $file_path = $result->result->file_path;
        if($supported_formats !== NULL){
            $arr = explode('.', $file_path);
            $ext = strtolower($arr[count($arr)-1]);
            if(!in_array($ext,$supported_formats)) return 'unsupported_format';
        }


        if(empty($file_path)) {
            return '';
        }

        //get file
        $url = "https://api.telegram.org/file/bot".TelegramBotId."/{$file_path}";
        $file = file_get_contents($url);

        if(!$file) {
            return '';
        }

        $new_file_name = uniqid() . '_' . basename($url);

        $target_file = $target_dir . $new_file_name;

        if (file_put_contents($target_file, $file) !== false) {
            return env('DOMAIN').'/backend/web/uploads/creative_expressions/' . $user_id . '/' . $new_file_name;
        } else {
            return '';
        }
    }

    public static function removeFileFromExpression($expression_id) {
        $cur_expression = CreativeExpressions::findOne($expression_id);

        if(empty($cur_expression->content)) return true;

        $file = Yii::getAlias('@webroot') . $cur_expression->content;

        if(file_exists($file)){
            return FileHelper::unlink($file);
        }

        return true;
    }
}
