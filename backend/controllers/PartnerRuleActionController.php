<?php

namespace backend\controllers;

use app\models\PartnerRule;
use Yii;
use app\models\PartnerRuleAction;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * PartnerRuleActionController implements the CRUD actions for Teacher model.
 */
class PartnerRuleActionController extends AppController
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
     * Lists all PartnerRuleAction models.
     * @return mixed
     */
    public function actionIndex()
    {
      $query = PartnerRuleAction::find();
	  if(!empty(Yii::$app->request->get())) $query = PartnerRuleAction::filterPartnerRuleAction();

      $this->setMeta('Partner Rule Action');
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
     * Displays a single PartnerRuleAction model.
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
     * Creates a new PartnerRuleAction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PartnerRuleAction();

		if (
			$model->load(Yii::$app->request->post())
		) {
			$new_action_status = PartnerRuleAction::createAction($model->ruleId, $model->emittedLovestarsUser);

			if($new_action_status['status']){
				Yii::$app->session->setFlash('success', $new_action_status['message']);
				return $this->redirect(['view', 'id' => $new_action_status['action_id']]);
			} else {
				Yii::$app->session->setFlash('danger', $new_action_status['message']);
			}
		}

        return $this->render('create', [
            'model' => $model
        ]);
    }


//    /**
//     * Updates an existing PartnerRuleAction model.
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
//          $model->load(Yii::$app->request->post()) && $model->save()
//        ) {
//			Yii::$app->session->setFlash('success', "The partner rule action was updated.");
//			return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model
//        ]);
//    }

//    /**
//     * Deletes an existing PartnerRuleAction model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param string $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionDelete($id)
//    {
//        Yii::$app->session->setFlash('danger', "The partner rule action was deleted.");
//
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the PartnerRuleAction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PartnerRuleAction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PartnerRuleAction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
