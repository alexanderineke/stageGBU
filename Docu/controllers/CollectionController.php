<?php

namespace app\controllers;

use Yii;
use app\models\Collection;
use app\models\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\models\CollectionImage;
use app\models\CollectionDocument;
use app\models\CollectionCollection;
use yii\web\HttpException;
use yii\bootstrap\Button;
use yii\helpers\ArrayHelper;

/**
 * CollectionController implements the CRUD actions for Collection model.
 */
class CollectionController extends Controller {

    public $file_type = false;
    public $file_id = false;

    public function behaviors() {
        return [
            'acces' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'add', 'update', 'create', 'process', 'deleteimage', 'deletedocument', 'deletecollection', 'delete', 'view'],
                'rules' => [
                    [
                        'allow' => ['true'],
                        'actions' => ['view'],
                        'roles' => ['?'],
                    ],
                    ['allow' => ['true'],
                        'actions' => ['index', 'create', 'update', 'add', 'delete', 'deleteimage', 'deletedocument', 'deletecollection', 'view'],
                        'roles' => ['@'],
                    ],
                    ['allow' => ['false'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Collection models.
     * @return mixed
     */
    public function actionIndex() {
        $condition = '';
        $dataProvider = new ActiveDataProvider([
            'query' => Collection::find()
                    ->where($condition)
        ]);
        return $this->render('index', [
                    'model' => new Collection(),
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Collection model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->loadModel($id),
        ]);
    }

    /**
     * Creates a new Collection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new Collection();

        if ($request->post('Collection')) {
            $model->attributes = $request->post('Collection');
            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('created_on', date("Y-m-d H:i:s"));
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Collection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $request = Yii::$app->request;

        if ($request->post('Collection')) {
            $model->attributes = $request->post('Collection');
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Collection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $this->actionDeleteCollection(null, $id);
            $this->loadModel($id)->delete();
            return $this->redirect(['index']);
        } else
            throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
    }

    /**
     * Finds the Collection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function loadModel($id) {
        if (($model = Collection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAdmin() {
        $condition = '';
        $dataProvider = new ActiveDataProvider([
            'query' => Collection::find()
                    ->where($condition)
        ]);

        return $this->render('index', [
                    'model' => new Collection(),
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdd() {
        $request = Yii::$app->request;
        if ($request->post('type')) {
            if ($request->post('type') == 'image') {
                $model = new CollectionImage();
            } else if ($request->post('type') == 'document') {
                $model = new CollectionDocument();
            } else if ($request->post('type') == 'collection') {
                $model = new CollectionCollection();
            } else {
                throw new HttpException(404, 'The requested page does not exist.');
            }
            if ($model->add((int) $request->post('id'), (int) $request->post('collection'))) {
                $this->redirect(['view', 'id' => (int) $request->post('collection')]);
            } else {
                throw new HttpException(404, 'The requested page does not exist.');
            }
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

    public function actionDeleteImage($id, $image) {
        if (!empty($id)) {
            $model = $this->loadModel($id);

            if ($model->checkOwnership()) {
                $model = new CollectionImage;
                if ($model->deleteImage($image, $id)) {
                    Yii::$app->session->setFlash('success', "Afbeelding met succes uit collectie verwijderd");
                    $this->redirect(['view', 'id' => $id]);
                } else {
                    throw new HttpException(401, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
                }
            } else {
                throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            $model = new CollectionImage;
            if ($model->deleteImage($image)) {
                Yii::$app->session->setFlash('success', "Afbeelding met succes uit collectie verwijderd");
                $this->redirect(['image/index']);
            } else {
                throw new HttpException(401, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        }
    }

    public function actionDeleteDocument($id, $document) {
        $model = $this->loadModel($id);
        if ($model->checkOwnership()) {
            $model = new CollectionDocument;
            if ($model->deleteDocument($document, $id)) {
                Yii::$app->session->setFlash('success', "Document met succes uit collectie verwijderd");
                $this->redirect(['view', 'id' => $id]);
            } else {
                throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
        }
    }

    public function actionDeleteCollection($id, $collection) {
        if (!empty($id)) {
            $model = $this->loadModel($id);
            if ($model->checkOwnership()) {
                $model = new CollectionCollection;
                if ($model->deleteCollection($collection, $id)) {
                    Yii::$app->session->setFlash('success', "Collectie met succes uit collecties verwijderd");
                    $this->redirect(['view', 'id' => $id]);
                } else {
                    throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
                }
            } else {
                throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            $model = new CollectionCollection;
            if ($model->deleteCollection($collection, null)) {
                Yii::$app->session->setFlash('success', "Collectie met succes uit collecties verwijderd");
            } else {
                throw new HttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        }
    }

    public function performAjaxValidation($model) {
        $request = Yii::$app->request;
        if ($request->post('ajax') && $request->post('ajax') === 'collection-form') {
            echo \yii\bootstrap\ActiveForm::validate($model);
            Yii::$app->end();
        }
    }

//    public function runModal($type, $id) {
//        if (!Yii::$app->user->isGuest) {
//            echo Button::widget([
//                'label' => 'Voeg toe aan collectie',
//                    //  'type' => 'primary',
//                    //   'data' => [
//                    //     'toggle' => 'modal',
//                    //      'target' => '#collection_modal'
//                    //  ],
//            ]);
//
//            $list = ArrayHelper::map(Collection::find()
//                                    ->where(['user_id' => \Yii::$app->user->id])
//                                    ->andWhere(['published' => 1])
//                                    ->orWhere(['id' => 17]), 'id', 'title'
//            );
//
//            $this->render('_modal', [
//                'type' => $type,
//                'id' => $id,
//                'list' => $list,
//            ]);
//        }
//    }

}
