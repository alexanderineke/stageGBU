<?php

class SearchController extends Controller
{
    public function actionTag(){
        $tag = Yii::app()->request->getParam('tag');
        $model = new Search();

        //Images
        $imageSearch = $model->searchImagesByTag($tag);

        //Documents
        $documentModel = new Document;
        if(Yii::app()->request->getParam('Document')){
            $doc = Yii::app()->request->getParam('Document');
            $documentModel->unsetAttributes();
            $documentModel->attributes=$doc;
            $documentModel->tag_search = $doc['tag_search'];
        }
        $documentSearch = $model->searchDocumentsByTag($documentModel, $tag);

        //Audio
        $audioModel = new Audio;
        if(Yii::app()->request->getParam('Audio')){
            $au = Yii::app()->request->getParam('Audio');
            $audioModel->unsetAttributes();
            $audioModel->attributes=$au;
            $audioModel->tag_search = $au['tag_search'];
        }
        $audioSearch = $model->searchAudioByTag($audioModel, $tag);

        $this->render('results', array(
            'model'=>$model,
            'imageSearch'=>$imageSearch,
            'documentSearch'=>$documentSearch,
            'documentModel'=>$documentModel,  
            'audioSearch'=>$audioSearch,
            'audioModel'=>$audioModel,  
        ));

    }

    public function actionResults()
    {
        //Zoek variablen
        $keyword = Yii::app()->request->getParam('q');

        $model = new Search();

        $imageSearch = null;
        $imageSearch = $model->searchImages($keyword);

        $documentModel = null;
        $documentSearch = null;
        $documentModel = new Document;
        if(Yii::app()->request->getParam('Document')){
            $doc = Yii::app()->request->getParam('Document');
            $documentModel->unsetAttributes();
            $documentModel->attributes=$doc;
            $documentModel->tag_search = $doc['tag_search'];
        }
        $documentSearch = $model->searchDocuments($documentModel, $keyword);

        $audioModel = null;
        $audioSearch = null;
        $audioModel = new Audio;
        if(Yii::app()->request->getParam('Audio')){
            $au = Yii::app()->request->getParam('Audio');
            $audioModel->unsetAttributes();
            $audioModel->attributes=$au;
            $audioModel->tag_search = $au['tag_search'];
        }
        $audioSearch = $model->searchAudio($audioModel, $keyword);

        $this->render('results', array(
            'model'=>$model,
            'imageSearch'=>$imageSearch,
            'documentSearch'=>$documentSearch,
            'documentModel'=>$documentModel,  
            'audioSearch'=>$audioSearch,
            'audioModel'=>$audioModel,  
        ));
    }

}