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

    protected $tags = [];

    public function filters() {
        return ['accesControl'];
    }

    //Geeft rechten aan gebruikers
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

    //returns view
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
            //maakt folder aan
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
            //maakt folder aan
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
            $fileQueue = Yii::$app->session->get('filesToProcess');

            if ($fileQueue) {
                $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            }
            //geeft attributen aan model mee
            $model->setAttribute('user_id', \Yii::$app->user->identity->id);
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->attributes = $request->post('Audio');

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if ((new AudioFile)->saveAudio($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);
                                $this->redirect(['view', 'id' => $model->id]);
                            } else {
                                Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                            }
                        } else {
                            $this->redirect(['view', 'id' => $model->id]);
                        }
                    } else {
                        Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::$app->getSession()->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        } else {
            Yii::$app->session->set('filesToProcess', []);
        }
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
            return $this->redirect(['index']);
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
            //geeft attributen aan model
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

//        $list = ArrayHelper::map(Collection::find()
//                                    ->where(['user_id' => \Yii::$app->user->id])
//                                    ->andWhere(['published' => 1])
//                                    ->all(), 'id', 'title');
        return $this->render('process', [
                    'model' => $model,
                    'file' => $file,
                        //     'collection_list' => $list,
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

    //haalt tags op
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
            //checkt of de tags al bestaan
            $existingTags = (new Tag)->check($tags);
            foreach ($existingTags as $i => $tag) {
                $tagArr[] = $tag->id;
                unset($tags[$tag->slug]);
            }

            //maakt nieuwe tags aan
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
    //slaat tags in tbl_audio_tags op
    protected function saveTags($audio_id) {
        $request = Yii::$app->request;
        $errorOccured = false;
        (new AudioTag)->add($audio_id, array_unique($this->tags));

        if (!(new AudioTag)->add($audio_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $request->post('Audio')['tags_previous']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i) {
                $prevTagsArr[] = (int) $i;
            }
        }
        $deleteTagsArr = array_diff($prevTagsArr, $this->tags);

        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            //verwidjerd tags uit tbl_audio_tags
            AudioTag::deleteAll(['audio_id' => $audio_id, 'tag_id' => $deleteTagsArr, 'state' => 1]);
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
