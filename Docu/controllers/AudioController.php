<?php

namespace app\controllers;

use Yii;
use app\models;
use app\models\Search;
use app\models\Audio;
use app\models\Tag;
use app\models\AudioTag;
use app\models\AudioFile;
use app\models\AudioTemp;
use app\models\Collection;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\widgets\dropzone\UploadAction;
use yii\widgets\dropzone\RemoveAction;

/**
 * AudioController implements the CRUD actions for Audio model.
 */
class AudioController extends Controller {

    //  public $layout = '@app/views/layouts/column2.php';
    protected $tags = [];

    // public $layout='/column2';

    public function filters() {
        return ['accesControl'];
    }

    public function behaviors() {
        return [
            'acces' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete'],
                'rules' => [
                    [ 'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?'],
                    ],
                    [ 'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload'],
                        'roles' => ['moderator'],
                    ],
                    [ 'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete'],
                        'roles' => ['@'],
                    ],
                    [ 'allow' => false,
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
        $model = new AudioTemp;
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
        $model = new AudioTemp;
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
        if ($request->post('Audio')) {
            // $fileQueue = Yii::app()->user->getState('filesToProcess');
            $fileQueue = Yii::$app->session->get('filesToProcess');

            if ($fileQueue) {
                $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            }

            $model->setAttribute('user_id', \Yii::$app->user->identity->id);
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->attributes = $request->post('Audio');

            //           $modelTag = new Tag;
            //           $this->generateAllTags($modelTag->tags, $request->post('tags'));
//            
//                if ($this->generateTags()) {

            if ($model->save()) {

                //                  if ($this->saveTags($model->id)) {
                if ($fileQueue) {
                    if ((new AudioFile)->saveAudio($model->id, $this->tags[0], $file)) {
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
//            } else {
//                //Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
//                Yii::$app->getSession()->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
//            }
//        } else {
//            Yii::$app->session->set('filesToProcess', []);
//        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->post()) {
            $this->loadModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new HttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionCreate() {
        $model = new Audio();

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
            $model = new Audio();
        }

        $fileQueue = Yii::$app->session->get('filesToProcess');

        if (!$fileQueue) {
            $this->redirect(['index']);
        }

        if (!$id && $request->post('Audio')) {
            $audioTempModel = AudioTemp::findOne($fileQueue[0]);
            $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            $model->attributes = $request->post('Audio');
        } else if ($id) {

            if ($model->audios) {
                $file = $model->audios[0];
            } else {
                $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            }
        } else {
            $audioTempModel = AudioTemp::findOne($fileQueue[0]);
            $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
        }

        if (isset($request->post('Audio')['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('created_on', date("Y-m-d H:i:s"));
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->setAttribute('published', 1);

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this->saveTags($model->id)) {
                        if (!$model->audios) {
                            if ((new AudioFile)->saveAudio($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);

                                if (!empty($fileQueue)) {
                                    $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                                    $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
                                } else {
                                    Yii::$app->session->setFlash('succes', "Audio bestand(en) met succes toegevoegd.");
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                                $this->redirect(['process', 'id' => $model->id]);
                            }
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                        $this->redirect(['process', 'id' => $model->id]);
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::$app->session->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        }

        if (!$fileQueue || !isset($file)) {
            $this->redirect(['index']);
        }

//        $list = ArrayHelper::map(Collection::model()->findAll(
//                                ['order' => 'title',
//                                    'condition' => 'user_id=:id AND published=1',
//                                    'params' => array(':id' => Yii::$app->user->getId())
//                                ]
//                        ), 'id', 'title');
        return $this->render('process', [
                    'model' => $model,
                    'file' => $file,
                        //      'collection_list' => $list,
        ]);
    }

    public function actionIndex() {
        if (!Yii::$app->user->getIdentity('moderator')) {
            $condition = 'published=1';
        } else {
            $condition = '';
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Audio::find()
                    ->where($condition)
                    ->orderBy('title ASC'),
        ]);

        return $this->render('index', [
                    'model' => new Audio(),
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdmin() {
        $model = new Audio();
        // $model->unsetAttributes(); // Bestaat niet!
        if (isset($_GET['Audio'])) {
            $model->attributes = $_GET['Audio'];
        }
        return $this->render('admin', [
                    'model' => $model,
        ]);
    }

    public function loadModel($id) {
        if (($model = Audio::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function generateTags() {
        $request = Yii::$app->request;
        $tags = [];

        if ($request->post('tags')) {
            $newSlugs = [];
            $newTags = [];
            foreach ($request->post('tags') as $i => $newtag) {
                $name = (string) $newtag;
                setlocale(LC_ALL, 'nl_NL');
                $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
                $name = preg_replace('/[^ \w]+/', '-', $name);
                $name = mb_strtolower($name);
                $name = trim($name, '-');

                $newSlugs[$i] = $name;
                $newTags[$i] = mb_strtolower($newtag);
            }
            //   $f = new Tag();
            $selectedTags = (new Tag)->check($newSlugs); //Moet nog naar gekeken worden
            $remainingTags = [];
            $remainingSlugs = [];


            if (isset($selectedTags)) {
                $compareTags = [];
                foreach ($selectedTags as $t) {
                    $compareTags[$t->slug] = $t->id;
                }
                foreach ($newSlugs as $i => $newslug) {
                    if (!array_key_exists($newslug, $compareTags)) {
                        if (!in_array($newslug, $remainingSlugs)) {
                            $remainingTags[] = $newTags[$i];
                            $remainingSlugs[] = $newslug;
                        }
                    }
                }
            } else {
                $remainingTags = $newTags;
            }

            if (isset($remainingTags) && sizeof($remainingTags)) {
                if ($addedTags = (new Tag)->add($remainingTags, $remainingSlugs)) {
                    foreach ($addedTags as $i) {
                        $tags[] = $i;
                    }
                } else {
                    $errorOccured = true;
                }
            }

            if (isset($compareTags) && sizeof($compareTags)) {
                foreach ($compareTags as $i) {
                    $tags[] = $i;
                }
            }
        }
        $this->tags = $tags;

        if (sizeof($tags)) {
            return true;
        }
    }

    protected function saveTags($audio_id) {
        $errorOccured = false;

        if (!(new AudioTag)->add($audio_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $_POST['Audio']['tags']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i) {
                $prevTagsArr[] = (int) $i;
            }
        }
        $deleteTagsArr = array_dif($prevTagsArr, $this->tags);
        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            if (!(new AudioTag)->deleteTags($audio_id, $deleteTagsArr)) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
