<?php

namespace app\controllers;

use Yii;
use app\models\Collection;
use app\models\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CollectionController implements the CRUD actions for Collection model.
 */
class CollectionController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function accessRules() {
        return [
            ['allow', // allow all users to perform 'index' and 'view' actions
                'actions' => ['view'],
                'users' => ['*'],
            ],
            ['allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => ['index', 'create', 'update', 'add', 'delete', 'deleteimage', 'deletedocument', 'deletecollection'],
                'users' => ['@'],
            ],
            ['allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => ['delete'],
                'users' => ['admin'],
            ],
            ['deny', // deny all users
                'users' => ['*'],
            ],
        ];
    }

    /**
     * Lists all Collection models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
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
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Collection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Collection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Collection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Collection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAdmin() {
        $model = new Collection('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Collection'])) {
            $model->attributes = $_GET['Collection'];
        }
        return $this->render('admin', [
                    'model' => $model,
        ]);
    }

    /**
     * Add item to a collection
     */
    public function actionAdd() {

        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'image') {
                $model = new CollectionImage();
            } elseif ($_POST['type'] === 'document') {
                $model = new CollectionDocument();
            } elseif ($_POST['type'] === 'collection') {
                $model = new CollectionCollection();
            } else {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
            if ($model->add((int) $_POST['id'], (int) $_POST['collection'])) {
                $this->redirect(['view', 'id' => (int) $_POST['collection']]);
            } else {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        } else {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
    }

    /**
     * Deletes a particular image from CollectionImage.
     */
    public function actionDeleteImage($id, $image) {
        if (!empty($id)) {
            $model = $this->loadModel($id);

            if ($model->checkOwnership()) {
                $model = new CollectionImage;
                if ($model->deleteImage($image, $id)) {
                    Yii::$app->user->setFlash('success', "Afbeelding met succes uit collectie verwijderd");
                    $this->redirect(['view', 'id' => $id]);
                } else {
                    throw new CHttpException(401, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
                }
            } else {
                throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            $model = new CollectionImage;
            if ($model->deleteImage($image)) {
                Yii::$app->user->setFlash('success', "Afbeeldingen met succes uit collecties verwijderd");
                $this->redirect(['image/index']);
            } else {
                throw new CHttpException(401, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        }
    }

    /**
     * Deletes a particular document from CollectionDocument.
     */
    public function actionDeleteDocument($id, $document) {
        $model = $this->loadModel($id);

        if ($model->checkOwnership()) {
            $model = new CollectionDocument;
            if ($model->deleteDocument($document, $id)) {
                Yii::$app->user->setFlash('success', "Document met succes uit collectie verwijderd");
                $this->redirect(['view', 'id' => $id]);
            } else {
                throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
        }
    }

    /**
     * Deletes a particular collection from CollectionCollection.
     */
    public function actionDeleteCollection($id, $collection) {
        if (!empty($id)) {
            $model = $this->loadModel($id);

            if ($model->checkOwnership()) {
                $model = new CollectionCollection;
                if ($model->deleteCollection($collection, $id)) {
                    Yii::$app->user->setFlash('success', "Collectie met succes uit collectie verwijderd");
                    $this->redirect(['view', 'id' => $id]);
                } else {
                    throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
                }
            } else {
                throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        } else {
            $model = new CollectionCollection;
            if ($model->deleteCollection($collection, null)) {
                Yii::$app->user->setFlash('success', "Collectie met succes uit collecties verwijderd");
            } else {
                throw new CHttpException(400, 'Ongeldig verzoek. Probeer dit a.u.b. niet nog eens.');
            }
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Collection::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'De gevraagde pagina bestaat niet.');
        }
        return $model;
    }

}
