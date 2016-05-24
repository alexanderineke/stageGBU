<?php

namespace app\controllers;

use Yii;
use app\models\Document;
use app\models\Search;
use yii\web\Controller;
use app\models\CollectionDocument;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\DocumentTemp;
use app\models\DocumentFile;
use app\models\Tag;
use app\models\DocumentTag;
use yii\data\ActiveDataProvider;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller {

    protected $tags = [];

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
        $uploadedFile = UploadedFile::getInstanceByName('Document[file]');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";
        if (!is_dir(Yii::getAlias('@app' . '/../uploads/' . $folderName))) {
            mkdir(Yii::getAlias('@app' . '/../uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('@app' . '/../uploads/' . $folderName . '/' . $fileName))) {
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
        $uploadedFile = UploadedFile::getInstanceByName('Document[file]');
        $rnd = rand(0, 9999);
        $folderName = date("d M Y");
        $fileName = "{$rnd}_{$uploadedFile}";
        if (!is_dir(Yii::getAlias('@app' . '/../uploads/' . $folderName))) {
            mkdir(Yii::getAlias('@app' . '/../uploads/' . $folderName));
        }
        if ($uploadedFile->saveAs(Yii::getAlias('@app' . '/../uploads/' . $folderName . '/' . $fileName))) {
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
        $model = $this->loadModel($id);

        if (isset($_POST['Document'])) {
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

            $model->setAttribute('user_id', Yii::$app->user->getId());
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));
            $model->attribute = $_POST['Document'];
            $model->setAttribute('published', -1);

            if (!$this->generateTags()) {
                Yii::$app->session->setFlash('error', "De steekwoorden zijn ongeldig. Probeert u het alstublieft nog eens.");
                goto render;
            }

            if (!$this->getDocumentContent($model, $file, $existingFile)) {
                Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het uitlezen van het document.");
                goto render;
            }

            if (!$model->save()) {
                Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan. Probeert u het alstublieft nog eens.");
                goto render;
            }

            if (!$this->saveTags($model->id)) {
                Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van de steekwoorden. Probeert u het alstublieft nog eens.");
                goto render;
            }

            if ((new DocumentFile)->saveDocument($model->id, $this->tags[0], $file)) {
                array_shift($fileQueue); //Verwijder dit bestand uit de wachtrij
                Yii::$app->session->set('filesToProcess', $fileQueue);
                $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', "Er is een fout opgetreden bij het opslaan van het bestand. Probeert u het alstublieft nog eens.");
                goto render;
            }

            if (!$fileQueue) {
                $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            //Uploads die nog in de sessie leven verwijderen
            Yii::$app->session->set('filesToProcess', []);
            goto render;
        }

        render: {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        if (Yii::$app->request->post()) {
            $model = new CollectionDocument;
            if (!$model->deleteDocument($id, null)) {
                throw new HttpException(401, 'Invalid request. Please do not repeat this request again.');
            }

            $this->loadModel($id)->delete();

            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['index']);
        } else
            throw new HttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionCreate() {
        $model = new Document();
        Yii::$app->session->set('filesToProcess', []);

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    //////////////////////////////////////////
    /////////////////////////////////////////
    ////////////////////////////////////////
    public function actionProcess() {
        $id = Yii::$app->request->getParams('id');

        if ($id) {
            $model = $this->loadModel($id);
        } else {
            $model = new Document;
        }

        $fileQueue = Yii::$app->session->get('filesToProcess');
        if (!$fileQueue) {
            $this->redirect(['index']);
        }

        if (!$id && isset($_POST['Document'])) {
            $documentTempModel = DocumentTemp::findOne($fileQueue[0]);
            $file = $documentTempModel->getAttrbutes(['file', 'format', 'location']);
            $model->attributes = $_POST['Document'];
        } else if ($id) {

            if ($model->documents) {
                $file = $model->documents[0];
            } else {
                $documentTempModel = DocumentTemp::findOne(fileQueue[0]);
                $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
            }
        } else {
            $documentTempModel = DocumentTemp::findOne(fileQueue[0]);
            $file = $documentTempModel->getAttributes(['file', 'format', 'location']);
        }

        if (isset($_POST['Document']['included_file'])) {
            $model->setAttribute('user_id', Yii::$app->user->getId());
            $model->setAttribute('created_on', \yii\db\Expression('NOW()'));
            $model->setAttribute('modified_on', \yii\db\Expression('NOW()'));

            if ($this->generateTags()) {

                if ($this->getDocumentContent($model, $file)) {

                    if ($model->save()) {

                        if ($this->saveTags($model->id)) {

                            if (isset($_POST['Document']['collection'])) {
                                $collection = (int) $_POST['Document']['collection'];
                                if ($collection > 0) {
                                    if (!CollectionDocument::add($model->id, $collection)) {
                                        Yii::$app->session->setFlash('warning', "Het document kon niet aan de door u geselecteerde collectie worden toegevoegd.");
                                    }
                                }

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

            $list = ArrayHelper::map(Collection::model()->findAll(
                                    ['order' => 'title',
                                        'condition' => 'user_id=:id AND published=1',
                                        'params' => [':id' => Yii::$app->user->getId()]
                                    ]
                            ), 'id', 'title');

            $this->render('process', [
                'model' => $model,
                'file' => $file,
                'collection_list' => $list,
            ]);
        }
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
                //      'criteria' => [
                //         'condition' => $condition,
                //         'order' => 'title ASC',
                //     ],
        ]);

        return $this->render('index', [
                    'model' => new Document(),
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
        $model = new Document();
        // $model->unsetAttributes(); // Functie bestaat niet, dus moet naar gekeken worden
        if (isset($_GET['Document'])) {
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
        $tags = [];
        if (isset($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag) {
                $tags[] = (int) $tag; //Zorg dat de id's nummers zijn
            }
        }
        if (isset($_POST['newtags'])) {
            $newSlugs = [];
            $newTags = [];
            foreach ($_POST['newtags'] as $i => $newtag) {
                //Convert to ASCII, remove spaces and convert to lowercase
                $name = (string) $newtag; //Zorg dat elke tag een string is
                setlocale(LC_ALL, 'nl_NL'); //Nodig voor iconv
                $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
                $name = preg_replace('/[^ \w]+/', '-', $name);
                $name = mb_strtolower($name);
                $name = trim($name, '-');

                $newSlugs[$i] = $name;
                $newTags[$i] = mb_strtolower($newtag);
            }

            $selectedTags = (new Tag)->check($newSlugs);
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

    protected function saveTags($document_id) {
        $errorOccured = false;

        if (!(new DocumentTag)->add($document_id, array_unique($this->tags))) {
            $errorOccured = true;
        }
        $prevTags = explode(',', $_POST['Document']['tags_previous']);
        $prevTagsArr = [];
        foreach ($prevTags as $i) {
            if ((int) $i)
                $prevTagsArr[] = (int) $i;
        }
        $deleteTagsArr = array_diff($prevTagsArr, $this->tags);
        if (sizeof($deleteTagsArr) && sizeof($prevTagsArr)) {
            if ((new DocumentTag)->deleteTags($document_id, $deleteTagsArr)) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

    public function getDocumentContent($model, $file, $existing = false) {
        if ($existing) {
            $file = Yii::getAlias('@app' . '/../uploads/documenten/' . $file['location'] . '/' . $file['file'] . $file['format']);
        } else {
            $file = Yii::getAlias('@app' . '/../uploads/' . $file['location'] . '/' . $file['file']);
        }

        if (file_exists($file)) {
            //importeer pdf parser
        }
    }

}
