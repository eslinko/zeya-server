<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\UploadFiles;
use common\models\User;

/**
 * MilitarySpecialtyController implements the CRUD actions for MilitarySpecialty model.
 */
class UploadFilesController extends AppController
{
  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          [
            'actions' => [
              'upload-files',
              'remove-file-by-url',
            ],
            'allow' => true,
            'roles' => ['?'],
          ],
          [
            'allow' => true,
            'roles' => [User::ROLE_ADMIN],
          ],
          [
            'allow' => true,
            'roles' => [User::ROLE_USER],
          ],
//          [
//            'actions' => ['delete'],
//            'allow' => false,
//            'denyCallback' => function($rule, $admin) {
//              return $this->redirect(Url::to(['site/no-access']));
//            },
//            'roles' => [User::ROLE_USER],
//          ]
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ];
  }

//  public function actionUploadFiles() {
//    $pasport_series = !empty($_POST['pasport_series']) ? $_POST['pasport_series'] : '';
//    $pasport_number = !empty($_POST['pasport_number']) ? $_POST['pasport_number'] : '';
//    if(empty($pasport_number) || empty($pasport_series) || empty($_FILES)) return false;
//    $folder_name = $pasport_series . $pasport_number;
//    $file_info = pathinfo($_FILES['file']['name']);
//
//    $model = new UploadFiles($folder_name, $file_info, $_FILES["file"]["tmp_name"]);
//
//    return $model->upload();
//  }
//
//  public function actionRemoveFileByUrl() {
//    $pasport_series = !empty($_POST['pasport_series']) ? $_POST['pasport_series'] : '';
//    $pasport_number = !empty($_POST['pasport_number']) ? $_POST['pasport_number'] : '';
//    $file_url = !empty($_POST['url_file_for_remove']) ? $_POST['url_file_for_remove'] : '';
//
//    if(empty($pasport_number) || empty($pasport_series) || empty($file_url)) return false;
//
//    $folder_name = $pasport_series . $pasport_number;
//    $file_info = pathinfo($file_url);
//
//    $model = new UploadFiles($folder_name, $file_info);
//
//    return $model->removeFile();
//  }

}
