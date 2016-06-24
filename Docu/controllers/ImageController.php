<?php

namespace app\controllers;

use Yii;
use app\models\Tag;
use app\models\Image;
use app\models\ImageTag;
use app\models\ImageFile;
use app\models\ImageTemp;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;

/**
 * ImageController implements the CRUD actions for Image model.
 */
class ImageController extends Controller {

    protected $tags = [516];

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

    public function actionView($id) {
        $model = Image::findOne($id);
        if ($model) {
            return $this->render('view', ['model' => $model]);
        } else {
            throw new \yii\web\NotFoundHttpException;
        }
    }

    public function actionUpload() {
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
                Yii::$app->session->set('filesToProcess', $fileQueue);
            } else {
                throw new HttpException(400, 'Upload niet gelukt.');
            }
        }
    }

    public function actionUpdate($id) {
        $request = Yii::$app->request;
        $model = $this->loadModel($id);

        if ($request->post('Image')) {
            $fileQueue = Yii::$app->session->get('filesToProcess');
            if ($fileQueue) {
                $imageTempModel = ImageTemp::findOne($fileQueue[0]);
                $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
            }

            $model->setAttribute('user_id', \Yii::$app->user->identity->id);
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->setAttribute('published', 1);
            $model->attributes = $request->post('Image');

            if ($this->generateTags()) {

                if ($model->save()) {

                    if ($this->saveTags($model->id)) {
                        if ($fileQueue) {
                            if ((new ImageFile)->saveImage($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);
                                $this->redirect(['view', 'id' => $model->id]);
                            } else {
                                Yii::$app->session->set('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                            }
                        } else {
                            $this->redirect(['view', 'id' => $model->id]);
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
        $request = Yii::$app->request;

        $id = Yii::$app->request->getQueryParam('id');

        if ($id) {
            $model = $this->loadModel($id);
        } else {
            $model = new Image();
        }

        $fileQueue = Yii::$app->session->get('filesToProcess');
        if (empty($fileQueue)) {
            return $this->redirect(['index']);
        }

        if (!$id && $request->post('Image')) {
            $imageTempModel = ImageTemp::findOne($fileQueue[0]);
            $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
            $model->attributes = $request->post('Image');
        } else if ($id) {

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
        if (isset($request->post('Image')['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->identity->id);
            $model->setAttribute('created_on', date("Y-m-d H:i:s"));
            $model->setAttribute('modified_on', date("Y-m-d H:i:s"));
            $model->setAttribute('published', 1);

            if ($this->generateTags()) {

                if ($model->save()) {
                    if ($this->saveTags($model->id)) {

                        if (!$model->images) {

                            if ((new ImageFile)->saveImage($model->id, $this->tags[0], $file)) {
                                array_shift($fileQueue);
                                Yii::$app->session->set('filesToProcess', $fileQueue);

                                if (!empty($fileQueue)) {
                                    $imageTempModel = ImageTemp::findOne($fileQueue[0]);
                                    $file = $imageTempModel->getAttributes(['file', 'format', 'location']);
                                } else {
                                    Yii::$app->session->setFlash('succes', "Afbeelding bestand(en) met succes toegevoegd.");
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
                Yii::$app->getSession()->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
            }
        }

        if (!$fileQueue || !isset($file)) {
            $this->redirect(['index']);
        }

        // $list = ArrayHelper::map(Collection::find()->all(), 'id', 'title');

        return $this->render('process', [
                    'model' => $model,
                    'file' => $file,
                        //            'collection_list' => $list,
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

    //koppelt de tags aan Image
    protected function saveTags($image_id) {
        $request = Yii::$app->request;
        $errorOccured = false;
        (new ImageTag)->add($image_id, array_unique($this->tags));

        if (!(new ImageTag)->add($image_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $request->post('Image')['tags_previous']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i) {
                $prevTagsArr[] = (int) $i;
            }
        }
        $deleteTagsArr = array_diff($prevTagsArr, $this->tags);

        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            ImageTag::deleteAll(['image_id' => $image_id, 'tag_id' => $deleteTagsArr, 'state' => 1]);
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
