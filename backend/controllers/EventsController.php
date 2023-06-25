<?php

namespace backend\controllers;

use app\components\PHPlibrary\simple_html_dom;
use app\models\GoogleCloud;
use Yii;
use app\models\Events;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * EventsController implements the CRUD actions for Events model.
 */
class EventsController extends AppController
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
                        'roles' => [User::ROLE_ADMIN, User::ROLE_EVENT_PROCESSOR],
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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Lists all Events models.
     * @return mixed
     */
    public function actionIndex()
    {
      $query = Events::find();

      $getData = Yii::$app->request->get();

	  if(!empty($getData)) {
          if(!empty($getData['open_by_id'])) {
              return $this->redirect(['update', 'id' => $getData['open_by_id']]);
          }
          $query = Events::filterEvents();
      }

        if(Yii::$app->user->identity->role === User::ROLE_EVENT_PROCESSOR) $query->andWhere(['status' => 'unprocessed']);

      $this->setMeta('Events');
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
     * Displays a single Events model.
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
     * Creates a new Events model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Events();
	
		if (
			$model->load(Yii::$app->request->post())
		) {
            if(!empty($model->description)) {
                $html = new simple_html_dom();
                $html->load($model->description);

                foreach ($html->find('img') as $img) {
                    $img->removeAttribute('alt');
                }

                $model->description = $html->outertext;
            }
            $model->start_timestamp = !empty($model->start_timestamp) ? strtotime($model->start_timestamp) : '';
            $model->end_timestamp = !empty($model->end_timestamp) ? strtotime($model->end_timestamp) : '';
			if($model->save()) {
				Yii::$app->session->setFlash('success', "The event was created.");
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}

        return $this->render('create', [
            'model' => $model
        ]);
    }


    /**
     * Updates an existing Events model.
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
            if(!empty($model->description)) {
                $html = new simple_html_dom();
                $html->load($model->description);

                foreach ($html->find('img') as $img) {
                    $img->removeAttribute('alt');
                }

                $model->description = $html->outertext;
            }
            $model->start_timestamp = !empty($model->start_timestamp) ? strtotime($model->start_timestamp) : '';
            $model->end_timestamp = !empty($model->end_timestamp) ? strtotime($model->end_timestamp) : '';
			if($model->save()) {
				Yii::$app->session->setFlash('success', "The event was updated.");
				return $this->redirect(['view', 'id' => $model->id]);
			}
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    private function showDOMNode(DOMNode $domNode) {
        foreach ($domNode->childNodes as $node)
        {
            print $node->nodeName.':'.$node->nodeValue;
            if($node->hasChildNodes()) {
                $this->showDOMNode($node);
            }
        }
    }

    /**
     * Deletes an existing Events model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->session->setFlash('danger', "The event was deleted.");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGetTimestampFromFacebookEventDateString() {
        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return Events::parseDateStartAndDateEndByFacebookDateString(Yii::$app->request->post('date_string'), 'Y-m-d H:i');
        }
        return $this->redirect(Url::to(['events/index']));
    }

    public function actionGetAddressFromImage() {
        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $target_dir = Yii::getAlias('@webroot').'/uploads/screenshots/';
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

            $fileUploaded = move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

            if($fileUploaded) {
                $googleCloud = new GoogleCloud();
                $textFromImage = $googleCloud->getTextFromImage($target_file, 'array');
                return $googleCloud->getAdressObjectFromAdressString($textFromImage);
            }

            return false;
        }
        return $this->redirect(Url::to(['events/index']));
    }

    /**
     * Finds the Events model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Events the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Events::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSetStatusOpenedToEvent() {
        if(Yii::$app->request->isAjax){
            $event = $this->findModel(Yii::$app->request->post('event_id'));
            $event->status = 'opened';
            $event->save(false);
            return true;
        }
        return $this->redirect(Url::to(['events/index']));
    }

}
