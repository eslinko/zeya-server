<?php

namespace backend\controllers;

use app\models\Settings;
use Yii;
use app\models\Lovestar;
use app\models\TeachingTransaction;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;


class SettingsController extends AppController
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
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN],
                    ],
                    [
                        'allow' => false,
                        'roles' => [User::ROLE_USER],
                        'denyCallback' => function($rule, $admin) {
                            return $this->redirect(Url::to(['site/no-access']));
                        },
                    ],
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

    /**
     * Lists all Teacher models.
     * @return mixed
     */
    public function actionIndex()
    {
      Settings::CreateRecords();//to greate db records to show on page
      $query = Settings::find();
	  //if(!empty(Yii::$app->request->get())) $query = Settings::filterTeachingTransaction();

      $this->setMeta('Bot settings');
      $dataProvider = new ActiveDataProvider([
          'query' => $query,
          'pagination' => [
              'pageSize' => 20,
              'pageSizeParam' => false,
          ],
      ]);

      return $this->render('index', [
          'dataProvider' => $dataProvider,
      ]);
    }
    public function actionUpdateSetting(){
        $data = Yii::$app->request->get();
        Settings::UpdateSetting($data['name'], $data['value']);

        //standard code below
        $query = Settings::find();
        //if(!empty(Yii::$app->request->get())) $query = Settings::filterTeachingTransaction();

        $this->setMeta('Bot settings');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'pageSizeParam' => false,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

//    /**
//     * Displays a single Teacher model.
//     * @param string $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
    /**
     * Creates a new Teacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeachingTransaction();
	
		if (
			$model->load(Yii::$app->request->post())
		) {
			echo "<pre>";
			var_dump($model);
			echo "</pre>";
			exit;
//			$new_action_status = TeachingTransaction::createAction($model->ruleId, $model->emittedLovestarsUser);
		
//			if($new_action_status['status']){
//				Yii::$app->session->setFlash('success', $new_action_status['message']);
//				return $this->redirect(['view', 'id' => $new_action_status['action_id']]);
//			} else {
//				Yii::$app->session->setFlash('danger', $new_action_status['message']);
//			}
		}

        return $this->render('create', [
            'model' => $model
        ]);
    }
//
//
//    /**
//     * Updates an existing Teacher model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param string $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if (
//          $model->load(Yii::$app->request->post())
//        ) {
//			if(!empty($model->hashtags)) $model->hashtags = implode(',', $model->hashtags);
//
//			if($model->save()) {
//				Yii::$app->session->setFlash('success', "The teacher was updated.");
//				return $this->redirect(['view', 'id' => $model->id]);
//			}
//        } else $model->hashtags = explode(',', $model->hashtags);
//
//        return $this->render('update', [
//            'model' => $model
//        ]);
//    }
//
//    /**
//     * Deletes an existing Teacher model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param string $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionDelete($id)
//    {
//        Yii::$app->session->setFlash('danger', "The teacher was deleted.");
//
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Teacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Teacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lovestar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
