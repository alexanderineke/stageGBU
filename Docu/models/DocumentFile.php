<?php

namespace app\models;

use Yii;
use yii\helpers\BaseFileHelper;
use Imagick;
use PhpThumbFactory;

/**
 * This is the model class for table "{{%document_file}}".
 *
 * @property integer $id
 * @property integer $document_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class DocumentFile extends \yii\db\ActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function tableName() {
        return '{{%document_file}}';
    }

    public function rules() {
        return [
            [['document_id', 'file', 'format', 'location'], 'required'],
            [['document_id', 'state'], 'integer'],
            [['file', 'location'], 'string', 'max' => 255],
            [['format'], 'string', 'max' => 4],
            [['id, document_id, file, format, location, state'], 'safe', 'on' => 'seach']
        ];
    }

    public function getDocument() {
        return $this->Belongs_to(Document::className(), ['id' => 'document_id']);
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'document_id' => 'Document',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
        ];
    }

    public function search() {
        $query = DocumentFile::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere([['like', 'id', $this->id],
                    ['like', 'document_id', $this->document_id],
                    ['like', 'file', $this->file],
                    ['like', 'format', $this->format],
                    ['like', 'location', $this->location]]);

        return $dataProvider;
    }

    //genereert de thumbs die bij het document horen
    public function genThumbs($file, $folder_name, $file_name) {

        //berekent de hoogte en breedte van het pdf bestand
        function calcDimensions($max, $original) {
            if ($original['height'] > $original['width']) {
                $dimensions = ['width' => ceil($max * $original['aspectRatio']), 'height' => $max];
            } elseif ($original['width'] > $original['height']) { //Landscape
                $dimensions = ['width' => $max, 'height' => ceil($max / $original['aspectRatio'])];
            } else { //Vierkant
                $dimensions = ['width' => $max, 'height' => $max];
            }

            return $dimensions;
        }

        $original = [];
        $pdf = file_get_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);
        $thumb = new Imagick(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);
        $original['height'] = $thumb->getimageheight();
        $original['width'] = $thumb->getimagewidth();
        $original['aspectRatio'] = $original['width'] / $original['height'];
        $thumb->clear();

        //maakt allemaal verschillende versies aan en voegt deze toe 
        $dimensions = calcDimensions(1024, $original);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec('gswin32c -q -o "' . dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_b.jpg" -dLastPage=1 -sDEVICE=jpeg -dJPEGQ=100 -dPDFFitPage -g' . $dimensions['width'] . 'x' . $dimensions['height'] . ' -dGraphicsAlphaBits=4 -dTextAlphaBits=4 "' . dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file'] . '"', $output);
        } else {
            exec('gs -q -o "' . dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_b.jpg" -dLastPage=1 -sDEVICE=jpeg -dJPEGQ=100 -dPDFFitPage -g' . $dimensions['width'] . 'x' . $dimensions['height'] . ' -dGraphicsAlphaBits=4 -dTextAlphaBits=4 "' . dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file'] . '"', $output);
        }

        $dimensions = calcDimensions(800, $original);
        $thumb = new Imagick(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_b.jpg');
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_c.jpg');

        $dimensions = calcDimensions(640, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_z.jpg');

        $dimensions = calcDimensions(500, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '.jpg');

        $dimensions = calcDimensions(320, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_n.jpg');

        $dimensions = calcDimensions(240, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_m.jpg');

        $dimensions = calcDimensions(100, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], Imagick::FILTER_LANCZOS, 1);
        $thumb->writeImage(dirname(Yii::$app->request->scriptFile) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '_t.jpg');

        $thumb->clear();

        return true;
    }

    //slaat het document bestand op
    public function saveDocument($document_id, $tag_id, $file) {
        $errorOccured = false;

        if ($file) {
            $tags = Tag::find()->where(['id' => $tag_id])->one();
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);
            $file_name = $fileInfo['filename'];

            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR);
            }

            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR);
            }

            $this->genThumbs($file, $folder_name, $file_name);

            $fileContents = file_get_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);

            file_put_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $fileInfo['filename'] . '.pdf', $fileContents);

            if (!$fileContents || !file_put_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documenten' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $fileInfo['filename'] . '.pdf', $fileContents)) {
                return false;
            }
            echo 'sweet';

            $this->updateAll(['state' => 0], 'document_id=' . $document_id);

            $this->document_id = $document_id;
            $this->file = $fileInfo['filename'];
            $this->location = $folder_name;
            $this->format = '.pdf';
            $this->state = 1;
            $this->setIsNewRecord(true);
            // $this->attributes = $attributes;
            if (!$this->insert()) {
                $errorOccured = false;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
