<?php

namespace app\controllers;

use Yii;
use app\models\Document;
use yii\web\Controller;
use app\models\CollectionDocument;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\DocumentTemp;
use app\models\DocumentFile;
use app\models\Tag;
use app\models\DocumentTag;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\web\HttpException;

class DocumentController extends Controller {

    protected $tags = [516];

    public function behaviors() {
        return [
            'acces' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete', 'batchdocs'],
                'rules' => [
                    [
                        'allow' => ['true'],
                        'actions' => ['index', 'view'],
                        'roles' => ['?'],
                    ],
                    ['allow' => ['true'],
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload'],
                        'roles' => ['moderator'],
                    ],
                    ['allow' => ['true'],
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete', 'batchdocs'],
                        'roles' => ['@'],
                    ],
                    ['allow' => ['false'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->loadModel($id),
        ]);
    }

    public function actionUpload() {
        $model = new DocumentTemp;
        $uploadedFile = UploadedFile::getInstanceByName('filename');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";
        if (!is_dir(Yii::getAlias('uploads/' . $folderName))) {
            BaseFileHelper::createDirectory(Yii::getAlias('uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('uploads/' . $folderName . '/' . $fileName))) {
            $id = $model->addTempFile($fileName, $folderName);
            if ($id) {
                Yii::$app->session->set('filesToProcess', [$id]);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        }
    }

    public function actionBatchupload() {
        $model = new DocumentTemp;
        $uploadedFile = UploadedFile::getInstanceByName('filename');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";


        if (!is_dir(Yii::getAlias('uploads/' . $folderName))) {
            BaseFileHelper::createDirectory(Yii::getAlias('uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('uploads/' . $folderName . '/' . $fileName))) {
            $id = $model->addTempFile($fileName, $folderName);
            if ($id) {
                $fileQueue = Yii::$app->session->get('filesToProcess');
                array_push($fileQueue, $id);
                Yii::$app->session->set('filesToProcess', $fileQueue);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        } else {
            throw new HttpException(400, 'Upload niet gelukt.');
        }
    }

    public function actionUpdate($id) {
        $request = Yii::$app->request;
        $model = $this->loadModel($id);

        if ($request->post('Document')) {
            $fileQueue = Yii::$app->session->get('filesToProcess');
            if ($fileQueue) {
                $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
                $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
                $existingFile = false;
            } else {
                if ($model->documents) {
                    $file = $model->documents[0];
                    $existingFile = true;
                }
            }

            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->setAttribute('published', 1);
            $model->attributes = $request->post('Document');

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if ((new DocumentFile)->saveDocument($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                //Yii::app()->user->setState('filesToProcess', $fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);
                                $this->redirect(['view', 'id' => $model->id]);
                            } else {
                                // Yii::app()->user - setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                                Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                            }
                        } else {
                            $this->redirect(['view', 'id' => $model->id]);
                        }
                    } else {
                        //Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                        Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                    }
                } else {
                    //Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                    Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                }
            } else {
                //Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
                Yii::$app->getSession()->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        } else {
            //Yii::app()->user->setState('filesToProcess', array());
            Yii::$app->getSession()->setFlash('filesToProcess', array());
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->post()) {
            $model = new CollectionDocument;
            if (!$model->deleteDocument($id, null)) {
                throw new HttpException(401, 'Invalid request. Please do not repeat this request again.');
            }
            $this->loadModel($id)->delete();
        } else
            throw new HttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionCreate() {
        $model = new Document();
        Yii::$app->session->set('filesToProcess', []);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionProcess() {
        $request = Yii::$app->request;

        $id = Yii::$app->getRequest()->getQueryParam('id');

        if ($id) {
            $model = $this->loadModel($id);
        } else {
            $model = new Document();
        }

        $fileQueue = Yii::$app->session->get('filesToProcess');
        if (!$fileQueue) {
            $this->redirect(['index']);
        }

        if (!$id && $request->post('Document')) {

            $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
            $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
            $model->attributes = $request->post('Document');
        } else if ($id) {

            if ($model->documents) {
                $file = $model->documents[0];
            } else {
                $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
                $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
            }
        } else {
            $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
            $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
        }

        if (isset($request->post('Document')['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('created_on', date("Y-m-d H:i:s"));
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->setAttribute('published', 1);

            if ($this->generateTags()) {

                if ($this->getDocumentContent($model, $file)) {

                    if ($model->save()) {

                        if ($this->saveTags($model->id)) {

                            //       if (isset($_POST['Document']['collection'])) {
                            //           $collection = (int) $_POST['Document']['collection'];
                            //          if ($collection > 0) {
                            //                if (!CollectionDocument::add($model->id, $collection)) {
                            //                    Yii::$app->session->setFlash('warning', "Het document kon niet aan de door u geselecteerde collectie worden toegevoegd.");
                            //                }


                            if (!$model->documents) {

                                if ((new DocumentFile)->saveDocument($model->id, $this->tags[0], $file)) {
                                    array_shift($fileQueue);
                                    Yii::$app->session->set('filesToProcess', $fileQueue);

                                    if (!empty($fileQueue)) {
                                        $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
                                        $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
                                    } else {
                                        Yii::$app->session->setFlash('succes', "Document(en) met succes toegevoegd.");
                                    }
                                } else {
                                    Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                                    $this->redirect(['process', 'id' => $model->id]);
                                }
                                //          
                            }
                        } else {
                            Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                            $this->redirect(['process', 'id' => $model->id]);
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
                    }
                    //          } else {
                    Yii::$app->session->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
                }
            }
        }
        if (!$fileQueue || !isset($file)) {
            $this->redirect(['index']);
        }

//            $list = ArrayHelper::map(Collection::model()->findAll(
//                                    ['order' => 'title',
//                                        'condition' => 'user_id=:id AND published=1',
//                                        'params' => [':id' => Yii::$app->user->getId()]
//                                    ]
//                            ), 'id', 'title');

        return $this->render('process', [
                    'model' => $model,
                    'file' => $file,
                        //       'collection_list' => $list,
        ]);
    }

    public function actionIndex() {
        if (!Yii::$app->user->getIdentity('moderator')) {
            $condition = 'published=1';
        } else {
            $condition = '';
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Document::find()
                    ->where($condition)
                    ->orderBy('title ASC'),
                //    'pagination' => [
                //    'pageSize' => 20,
                // ],
                //      'criteria' => [
                //         'condition' => $condition,
                //         'order' => 'title ASC',
                //     ],
        ]);

        return $this->render('index', [
                    'model' => new Document(),
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdmin() {
        $request = Yii::$app->request;
        $model = new Document();
        // $model->unsetAttributes(); // Functie bestaat niet, dus moet naar gekeken worden
        if ($request->get('Document')) {
            $model->attributes = $_GET['Document'];
        }

        return $this->render('admin', [
                    'model' => $model,
        ]);
    }

    public function loadModel($id) {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function actionBatchdocs() {
        Document::find()
                ->where(['state' => 1])
                ->andWhere(['order' => 'id'])
                ->orderBy(['sort' => SORT_DESC]);

        foreach ($files as $record => $colums) {
            $file['file'] = $columns['file'];
            $file['format'] = $columns['format'];
            $file['location'] = $columns['location'];

            $documentModel = Document::findOne((int) $columns['document_id']);
            if ($documentModel) {
                if ($this->getDocumentContent($documentModel, $file, true)) {
                    $documentModel->save();
                }
            }
        }
    }

    protected function generateTags() {
        $request = Yii::$app->request;

        if ($request->post('tags')) {
            $tags = [];
            $tagArr = [];
            foreach ($request->post('tags') as $i => $tag) {
                $name = (string) $tag;
                setlocale(LC_ALL, 'nl_NL');
                $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
                $name = preg_replace('/[^ \w]+/', '-', $name);
                $name = mb_strtolower($name);
                $name = trim($name, '-');

                $tags[$name] = mb_strtolower((string) $tag);
            }
            $existingTags = (new Tag)->check($tags);
            foreach ($existingTags as $i => $tag) {
                $tagArr[] = $tag->id;
                unset($tags[$tag->slug]);
            }

            if (isset($tags) && sizeof($tags)) {
                $addedTags = (new Tag)->add($tags);
                foreach ($addedTags as $i) {
                    $tagArr[] = $i;
                }
            } else {
                $errorOccured = true;
            }

            $this->tags = $tagArr;

            if (sizeof($tagArr)) {
                return true;
            }
        }
    }

    protected function saveTags($document_id) {
        $request = Yii::$app->request;
        $errorOccured = false;
        (new DocumentTag)->add($document_id, array_unique($this->tags));

        if (!(new DocumentTag)->add($document_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $request->post('Document')['tags_previous']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i) {
                $prevTagsArr[] = (int) $i;
            }
        }
        $deleteTagsArr = array_diff($prevTagsArr, $this->tags);

        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            DocumentTag::deleteAll(['document_id' => $document_id, 'tag_id' => $deleteTagsArr, 'state' => 1]);
        }
        if (!$errorOccured) {
            return true;
        }
    }

    public function getDocumentContent($model, $file, $existing = false) {
        if ($existing) {
            $file = Yii::getAlias('uploads/documenten/' . $file['location'] . '/' . $file['file'] . $file['format']);
        } else {
            $file = Yii::getAlias('uploads/' . $file['location'] . '/' . $file['file']);
        }

        if (file_exists($file)) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file);
            $pages = $pdf->getPages();

            $text = '';
            foreach ($pages as $page) {
                $text .= $page->getText();
            }
            //setlocale(LC_ALL, 'nl_NL'); //Nodig voor iconv
            //$text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
            $text = preg_replace('/[^0-9a-zA-Z ]/', ' ', $text);
            $text = preg_replace(array('/\b\w{1,2}\b/', '/\s+/'), array('', ' '), $text);
            $model->setAttribute('content', $text);
            return true;
        } else
            return false;
    }

}
