<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

    public function behaviors() {
        return [
            'acces' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'update', 'create', 'admin', 'delete'],
                'rules' => [
                    [ 'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?'],
                    ],
                    [ 'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create'],
                        'roles' => ['moderator'],
                    ],
                    [ 'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'admin', 'delete'],
                        'roles' => ['@'],
                    ],
                    [ 'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex() {
        $condition = '';
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()
                 ->where($condition)
                ]);
        return $this->render('index', [
            'model' => new User(),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    } 
    public function actionCreate() {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }
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
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAdmin() {
        $condition = '';
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()
                 ->where($condition)
                ]);
       /* */
        return $this->render('admin', [
            'model' => new User(),
            'dataProvider' => $dataProvider,
        ]);      
    }
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
