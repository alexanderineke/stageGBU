<?php

namespace app\controllers;

use Yii;
use app\models\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Document;
use app\models\Image;
use app\models\Tag;
use app\models\Audio;

/**
 * UserController implements the CRUD actions for User model.
 */
class SearchController extends Controller {

 //   public function behaviors() {
 //       return [
 //           'verbs' => [
  //              'class' => VerbFilter::className(),
   //             'actions' => [
  //                  'delete' => ['post'],
  //              ],
  //          ],
  //      ];
  //  }

    public function actionTag() {
         $model = new Search();
         $tag = Yii::$app->getRequest()->getQueryParam('tag');

        //Images
        $imageSearch = $model->searchImagesByTag($tag);

        //Documents
        $documentModel = new Document;
        if (Yii::$app->getRequest()->getQueryParam('Document')) {
            $doc = Yii::$app->getRequest()->getQueryParam('Document');
        //    $documentModel->unsetAttributes();
            $documentModel->attributes = $doc;
            $documentModel->tag_search = $doc['tag_search'];
        }
        $documentSearch = $model->searchDocumentsByTag($documentModel, $tag);

        //Audio
        $audioModel = new Audio;
        if (Yii::$app->getRequest()->getQueryParam('Audio')) {
            $au = Yii::$app->getRequest()->getQueryParam('Audio');
       //     $audioModel->unsetAttributes();
            $audioModel->attributes = $au;
            $audioModel->tag_search = $au['tag_search'];
        }
        $audioSearch = $model->searchAudioByTag($audioModel, $tag);

        return $this->render('results', [
            'model' => $model,
            'imageSearch' => $imageSearch,
            'documentSearch' => $documentSearch,
            'documentModel' => $documentModel,
            'audioSearch' => $audioSearch,
            'audioModel' => $audioModel,
        ]);
    }
    public function actionResults()
    {
        //Zoek variablen
        $keyword = Yii::$app->getRequest()->getQueryParam('q');

        $model = new Search();

        $imageSearch = null;
        //$imageSearch = $model->searchImages($keyword);

        $documentModel = null;
        $documentSearch = null;
        $documentModel = new Document;
//        if(Yii::$app->getRequest()->getQueryParam('Document')){
//            $doc = Yii::$app->getRequest()->getQueryParam('Document');
//         //   $documentModel->unsetAttributes();
//            $documentModel->attributes=$doc;
//            $documentModel->tag_search = $doc['tag_search'];
//        }
        $documentSearch = $model->searchDocuments($documentModel, $keyword);

        $audioModel = null;
        $audioSearch = null;
//        $audioModel = new Audio;
        
           
//        if(Yii::$app->getRequest()->getQueryParam('Audio')){
//            $au = Yii::$app->getRequest()->getQueryParam('Audio');
//      //      $audioModel->unsetAttributes();
//            $audioModel->attributes=$au;
//            $audioModel->tag_search = $au['tag_search'];
//        }
//        $audioSearch = $model->searchAudio($audioModel, $keyword);


        return $this->render('results',[
            'model'=>$model,
            'imageSearch'=>$imageSearch,
            'documentSearch'=>$documentSearch,
            'documentModel'=>$documentModel,  
            'audioSearch'=>$audioSearch,
            'audioModel'=>$audioModel,  
        ]);
    }

}