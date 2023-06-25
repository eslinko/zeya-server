<?php

namespace backend\controllers;

use Yii;
use app\models\Teacher;
use app\models\TeacherOutcome;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * TeacherOutcomeController implements the CRUD actions for Teacher model.
 */
class TeacherOutcomeController extends AppController
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
     * Lists all TeacherOutcome models.
     * @return mixed
     */
    public function actionIndex()
    {
      $query = TeacherOutcome::find();
	  if(!empty(Yii::$app->request->get())) $query = TeacherOutcome::filterTeacherOutcome();

      $this->setMeta('Teacher Outcome');
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

    /**
     * Displays a single TeacherOutcome model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Teacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeacherOutcome();
	
		if (
			$model->load(Yii::$app->request->post())
		) {
			if(!empty($model->hashtags)) $model->hashtags = implode(',', $model->hashtags);
		
			if($model->save()) {
				Yii::$app->session->setFlash('success', "The outcome was created.");
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}

        return $this->render('create', [
            'model' => $model
        ]);
    }


    /**
     * Updates an existing TeacherOutcome model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (
          $model->load(Yii::$app->request->post())
        ) {
			if(!empty($model->hashtags)) $model->hashtags = implode(',', $model->hashtags);
			
			if($model->save()) {
				Yii::$app->session->setFlash('success', "The outcome was updated.");
				return $this->redirect(['view', 'id' => $model->id]);
			}
        } else $model->hashtags = explode(',', $model->hashtags);

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing TeacherOutcome model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->session->setFlash('danger', "The outcome was deleted.");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Teacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TeacherOutcome the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherOutcome::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
