<?php

namespace backend\controllers;

use Yii;
use app\models\CreativeTypes;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * CreativeTypesController implements the CRUD actions for Teacher model.
 */
class CreativeTypesController extends AppController
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
     * Lists all CreativeTypes models.
     * @return mixed
     */
    public function actionIndex()
    {
      $query = CreativeTypes::find();

      $this->setMeta('Creative Types');
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
     * Displays a single CreativeTypes model.
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
     * Creates a new CreativeTypes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreativeTypes();

		if (
			$model->load(Yii::$app->request->post()) &&
			$model->save()
		) {
			
			Yii::$app->session->setFlash('success', "The creative type was created.");
			return $this->redirect(['view', 'id' => $model->id]);
		}

        return $this->render('create', [
            'model' => $model
        ]);
    }


    /**
     * Updates an existing CreativeTypes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (
          $model->load(Yii::$app->request->post()) && $model->save()
        ) {
			Yii::$app->session->setFlash('success', "The creative type was updated.");
			return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing CreativeTypes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->session->setFlash('danger', "The creative type was deleted.");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CreativeTypes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Teacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CreativeTypes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
