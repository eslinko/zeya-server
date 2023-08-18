<?php

namespace backend\controllers;

use common\models\TableCreator;
use yii\web\Controller;


class AppController extends Controller
{
    public function beforeAction($action)
    {
        new TableCreator();
        return parent::beforeAction($action);
    }

    protected function setMeta($title = null, $keywords = null, $description = null) {
        $this->view->title = $title;
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => "$keywords"]);
        $this->view->registerMetaTag(['name' => 'description', 'content' => "$description"]);
    }

    public static function getDateForModel(){
        date_default_timezone_set('Europe/Kiev');
        return date('Y-m-d H:i:s');
    }

    public static function printR($obj){
        echo '<pre>';
            print_r($obj);
        echo '</pre>';
    }
}