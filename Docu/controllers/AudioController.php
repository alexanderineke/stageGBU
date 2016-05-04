<?php

namespace app\controllers;

use Yii;
use app\models\Audio;
use app\models\Search;
use app\models\AudioTag;
use app\models\AudioFile;
use app\models\AudioTemp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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

    public function accesRules() {
        return [
            ['allow',
                'actions' => ['index', 'view'],
                'users' => ['*'],
            ],
            ['allow',
                'actions' => ['update', 'create', 'process', 'upload', 'batchupload'],
                'roles' => ['moderator'],
            ],
            ['allow',
                'actions' => ['admin', 'delete'],
                'roles' => ['admin'],
            ],
            ['deny',
                'users' => ['*'],
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

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function actionUpload() {
        $model = new AudioTemp;
    }

    public function actionBatchupload() {
        
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        if (isset($_POST['Audio'])) {
            $fileQueue = yii::app()->user->getState('filesToProcess');
            if ($fileQueue) {
                $audioTempModel = AudioTemp::findOne($fileQueue[0]);
                $file = $audioTempModel->getAttributes(['file', 'format', 'location']);
            }

            $model->setAttribute('user_id', yii::app()->user->getId());
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));
            $model->attributes = $_POST['Audio'];

            if ($this->generateTags()) {

                if ($this->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if (AudioFile::model()->saveAudio($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                yii::app()->user->setState('filesToProcess', $fileQueue);
                                $this->redirect(['view', 'id' => $model->id]);
                            } else {
                                yii::app()->user - setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                            }
                        } else {
                            $this - redirect(['view', 'id' => $model->id]);
                        }
                    } else {
                        yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                    }
                } else {
                    Yii::app()->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::app()->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        } else {
            Yii::app()->user->setState('filesToProcess', array());
        }

        $this->render('update', [
            'model' => $model,
        ]);


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!





        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCreate() {
        $model = new Audio();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!    
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionIndex() {
        if (!Yii::app()->user->checkAccess('moderator')) {
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
        $model->unsetAttributes();
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
            $selectedTags = Tag::model()->check($newSlugs);
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
                if ($addedTags = Tag::model()->add($remainingTags, $remainingSlugs)) {
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

        if (!AudioTag::model()->add($audio_id, array_unique($this->tags))) {
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
            if (!AudioTag::model()->deleteTags($audio_id, $deleteTagsArr)) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
