<?php

namespace app\controllers;

use Yii;
use app\models;
use app\models\Tag;
use app\models\Image;
use app\models\Search;
use app\models\ImageTag;
use app\models\ImageFile;
use app\models\ImageTemp;
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
 * ImageController implements the CRUD actions for Image model.
 */
class ImageController extends Controller {

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
                    [   'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?'],
                    ],
                    [   'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload'],
                        'roles' => ['moderator'],
                    ],
                    [   'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'process', 'upload', 'batchupload', 'admin', 'delete'],
                        'roles' => ['@'],
                    ],
                    [   'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

      public function actionView($id) {
        $model = Image::findOne($id);
        if ($model) {
            return $this->render('view', ['model' => $model]);
        } else {
            throw new \yii\web\NotFoundHttpException;
        }
    }

    public function actionUpload(){
        $model = new ImageTemp;
        $uploadedFile = UploadedFile::getInstanceByName('Image[file]');
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
        $model = new ImageTemp;
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
                Yii::$app->session->set('filesToProcess', [$fileQueue]);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        }
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        if (isset($_POST['Image'])) {
            $fileQueue = Yii::$app->session->get('filesToProcess');
            if ($fileQueue) {
                $imageTempModel = ImageTemp::findOne($fileQueue[0]);
                $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
            }

            $model->setAttribute('user_id', Yii::$app->user->getId());
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));
            $model->attributes = $_POST['Image'];

            if ($this->generateTags()) {

                if ($this->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if (ImageFile::model()->saveImage($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);
                                $this->redirect(['view', 'id' => $model->id]);
                            } else {
                                Yii::$app->session->set('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                            }
                        } else {
                            $this - redirect(['view', 'id' => $model->id]);
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::$app->session->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        } else {
             // Yii::$app->session->setState('filesToProcess', []);

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
        $model = new Image();

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
        $id = Yii::$app->request->getQueryParam('id');

        if ($id) {
            $model = $this->loadModel($id);
        } else {
            $model = new Image();
        }

        $fileQueue = Yii::$app->session->get('filesToProcess');
        if (!$fileQueue) {
            $this->redirect(['index']);
        }
 
        if (!$id && isset($_POST['Image'])) {
            $imageTempModel = ImageTemp::findOne($fileQueue[0]);
            $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
            $model->attributes = $_POST['Image'];
        } elseif ($id) {

            if ($model->images) {
                $file = $model->images[0];
            } else {
                $imageTempModel = ImageTemp::findOne($fileQueue[0]);
                $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
            }
        } else {
            $imageTempModel = ImageTemp::findOne($fileQueue[0]);
            $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
        }

        if (isset($_POST['Image']['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('created_on', \yii\db\Expression('NOW()'));
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this -> saveTags($model->id)) {

                        if (!$model->images) {
                            if (ImageFile::model()->saveImage($this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);

                                if (!empty($fileQueue)) {
                                    $imageTempModel = ImageTemp::findOne($fileQueue[0]);
                                    $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
                                } else {
                                    Yii::$app->user->setFlash('succes', "Afbeelding bestand(en) met succes toegevoegd.");
                                }
                            } else {
                                Yii::$app->user-> setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                                $this->redirect(['process', 'id' => $model->id]);
                            }
                        }
                    } else {
                        Yii::$app->user->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                        $this->redirect(['process', 'id' => $model->id]);
                    }
                } else {
                    yii::$app->user->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
                }
            } else {
                Yii::$app->user->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        }

        if (!$fileQueue || !isset($file)) {
            $this -> redirect(['index']);
        }

        $list = ArrayHelper::map(Collection::find()->all(),'id', 'title');

       return $this -> render('process', [
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
                'query' => Image::find()->
                        where($condition)
            ]);
            return $this->render('index', [

                        'model' => new Image(),
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
       $condition = '';
        $dataProvider = new ActiveDataProvider([
            'query' => Image::find()
                 ->where($condition)
                ]);
       /* */
        return $this->render('admin', [
            'model' => new Image(),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function loadModel($id) {
        if (($model = Image::findOne($id)) !== null) {
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

    protected function saveTags($image_id) {
        $errorOccured = false;

        if (!ImageTag::model()->add($image_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $_POST['Image']['tags_previous']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i) {
                $prevTagsArr[] = (int) $i;
            }
        }
        $deleteTagsArr = array_dif($prevTagsArr, $this->tags);
        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            if (!ImageTag::model()->deleteTags($image_id, $deleteTagsArr)) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
