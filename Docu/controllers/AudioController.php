<?php

namespace app\controllers;

use Yii;
use app\models\Audio;
use app\models\Search;
use app\models\Tag;
use app\models\AudioTag;
use app\models\AudioFile;
use app\models\AudioTemp;
use app\models\Collection;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * AudioController implements the CRUD actions for Audio model.
 */
class AudioController extends Controller {

    public $layout = '/layouts/column2';
    protected $tags = [];

    public function filters() {
        return ['accesControl'];
    }

    public function behaviors() {
        return [
            'acces' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete'],
                'rules' => [
                    [
                        'allow' => ['true'],
                        'actions' => ['index', 'view'],
                        'users' => ['*'],
                    ],
                    ['allow' => ['true'],
                        'actions' => ['update', 'create', 'process', 'upload', 'batchupload'],
                        'roles' => ['moderator'],
                    ],
                    ['allow' => ['true'],
                        'actions' => ['admin', 'delete'],
                        'roles' => ['admin'],
                    ],
                    ['deny' => ['true'],
                        'users' => ['*'],
                    ],
                ],
            ],
        ];
    }

    /*
      public function action() !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!{
      return [
      'upload'=>[
      'class'=>''
      ]
      ]
      }
     */

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->loadModel($id),
        ]);
    }

    public function actionUpload() {
        $model = new AudioTemp;
        $uploadedfile = UploadedFile::getInstanceByName('Audio[file]');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";
        if (!is_dir(Yii::getAlias('@app' . '/../uploads/' . $folderName))) {
            mkdir(Yii::getAlias('@app' . '/../uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('@app' . '/../uploads/' . $folderName . '/' . $fileName))) {
            $id = $model->addTempFile($fileName, $folderName);
            if ($id) {
                // Yii::$app->user->setState('filesToProcess', array($id));
                Yii::$app->session->set('filesToProcess', [$id]);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        }
    }

    public function actionBatchupload() {
        $model = new AudioTemp;
        $uploadedFile = UploadedFile::getInstanceByName('Audio[file]');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";
        if (!is_dir(Yii::getAlias('@app' . '/../uploads/' . $folderName))) {
            mkdir(Yii::getAlias('@app' . '/../uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('@app' . '/../uploads/' . $folderName . '/' . $fileName))) {
            $id = $model->addTempFile($fileName, $folderName);
            if ($id) {
                $fileQueue = Yii::app()->user->getState('filesToProcess');
                array_push($fileQueue, $id);
                Yii::$app->user->setState('filesToProcess', $fileQueue);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        }
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        if (isset($_POST['Audio'])) {
            // $fileQueue = Yii::app()->user->getState('filesToProcess');
            $fileQueue = Yii::$app->session->get('filesToProcess');
            if ($fileQueue) {
                $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            }

            $model->setAttribute('user_id', yii::$app->user->getId());
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));
            $model->attributes = $_POST['Audio'];

            if ($this->generateTags()) {

                if ($this->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if (AudioFile::model()->saveAudio($model->id, $this->tags[0], $file)) {
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

        $this->render('update', [
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

        Yii::$app->getSession()->setFlash('filesToProcess', []);

//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
        return $this->render('create', [
                    'model' => $model,
        ]);
//        }
    }

    public function actionProcess() {
        //$id = Yii::app()->request->getQueryParam('id');
        $id = Yii::$app->request->getParams('id');

        if ($id) {
            $model = $this->loadModel($id);
        } else {
            $model = new Audio();
        }

        $fileQueue = Yii::$app->session->setFlash('filesToProcess');
        if (!fileQueue) {
            $this->redirect(['index']);
        }

        if (!$id && isset($_POST['Audio'])) {
            $audioTempModel = AudioTemp::findOne($fileQueue[0]);
            $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            $model->attributes = $_POST['Audio'];
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

        if (isset($_POST['Audio']['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->getId());
            $model->setAttribute('created_on', \yii\db\Expression('NOW()'));
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this -> saveTags($model->id)) {

                        if (!$model->audios) {
                            if (AudioFile::model()->saveAudio($this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);

                                if (!empty($fileQueue)) {
                                    $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                                    $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
                                } else {
                                    Yii::$app->session->set('succes', "Audio bestand(en) met succes toegevoegd.");
                                }
                            } else {
                               Yii::$app->session->set('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                                $this->redirect(['process', 'id' => $model -> id]);
                            }
                        }
                    } else {
                        Yii::$app->session->set('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                        $this->redirect(['process', 'id' => $model->id]);
                    }
                } else {
                    Yii::$app->session->set('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::$app->session->set('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        }

        if (!$fileQueue || !isset($file)) {
            $this -> redirect(['index']);
        }

        $list = ArrayHelper::map(Collection::model()->findAll(
                                ['order' => 'title',
                                    'condition' => 'user_id=:id AND published=1',
                                    'params' => array(':id' => Yii::$app->user->getId())
                                ]
                        ), 'id', 'title');

        $this->render('process', [
            'model' => $model,
            'file' => $file,
            'collection_list' => $list,
        ]);
    }

    public function actionIndex() {
        if (!Yii::$app->user->getIdentity('moderator')) {
            $condition = 'published=1';
        } else {
            $condition = '';
        }

        $dataProvider = new ActiveDataProvider([
            'criteria' => [
                'condition' => $condition,
                'order' => 'title ASC',
            ],
        ]);

        $this->render('index', [
            'model' => new Audio(),
            'dataProvider' => $dataProvider,
        ]);

        /*
          $searchModel = new Search();
          $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

          return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
          ]);
         */
    }

    public function actionAdmin() {
        $model = new Audio('search');
        $model->unsetAttributes(); // Bestaat niet!
        if (isset($_GET['Audio'])) {
            $model->attributes = $_GET['Audio'];
        }
        $this - render('admin', [
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
        $tags = [];
        if (isset($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag) {
                $tags[] = (int) $tag;
            }
        }
        if (isset($_POST['newtags'])) {
            $newSlugs = [];
            $newTags = [];
            foreach ($_POST['newtags'] as $i => $newtag) {
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
            $remainingTags = array();
            $remainingSlugs = array();

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
        $prevTags = explode(',', $_POST['Audio']['tags_previous']);
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
