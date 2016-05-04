<?php

namespace app\controllers;

use Yii;
use app\models\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

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

    public function actionTag($params) {
         $model = new Search();

        //Images
        $imageSearch = $model->searchImagesByTag($tag);

        //Documents
        $documentModel = new Document;
        if (Yii::app()->request->getParam('Document')) {
            $doc = Yii::app()->request->getParam('Document');
            $documentModel->unsetAttributes();
            $documentModel->attributes = $doc;
            $documentModel->tag_search = $doc['tag_search'];
        }
        $documentSearch = $model->searchDocumentsByTag($documentModel, $tag);

        //Audio
        $audioModel = new Audio;
        if (Yii::app()->request->getParam('Audio')) {
            $au = Yii::app()->request->getParam('Audio');
            $audioModel->unsetAttributes();
            $audioModel->attributes = $au;
            $audioModel->tag_search = $au['tag_search'];
        }
        $audioSearch = $model->searchAudioByTag($audioModel, $tag);

        $this->render('results', array(
            'model' => $model,
            'imageSearch' => $imageSearch,
            'documentSearch' => $documentSearch,
            'documentModel' => $documentModel,
            'audioSearch' => $audioSearch,
            'audioModel' => $audioModel,
        ));
    }

}
