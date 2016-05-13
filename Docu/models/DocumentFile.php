<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

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
class DocumentFile extends ActiveRecord {

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

    public function genThumbs($file, $folder_name, $file_name) {

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
        $thumb = new \Imagick(Yii::getAlias('@app' . '/../uploads/' . $file['location'] . '/' . $file['file'] . '[0]'));
        $original['height'] = $thumb->getimageheight();
        $original['width'] = $thumb->getimagewidth();
        $original['aspectRatio'] = $original['width'] / $original['heigth'];
        $thumb->clear();

        $dimensions = calcDimensions(1024, $original);
        exec('gs -q -o "' . dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_b.jpg" -dLastPage=1 -sDEVICE=jpeg -dJPEGQ=100 -dPDFFitPage -g' . $dimensions['width'] . 'x' . $dimensions['height'] . ' -dGraphicsAlphaBits=4 -dTextAlphaBits=4 "' . dirname(Yii::app()->request->scriptFile) . '/uploads/' . $file['location'] . '/' . $file['file'] . '"', $output);

        $dimensions = calcDimensions(800, $original);
        $thumb = new Imagick(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_b.jpg');
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_c.jpg')) {
            return false;
        }

        $dimensions = calcDimensions(640, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_z.jpg')) {
            return false;
        }

        $dimensions = calcDimensions(500, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '.jpg')) {
            return false;
        }

        $dimensions = calcDimensions(320, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_n.jpg')) {
            return false;
        }

        $dimensions = calcDimensions(240, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_m.jpg')) {
            return false;
        }

        $dimensions = calcDimensions(100, $original);
        $thumb->resizeImage($dimensions['width'], $dimensions['height'], imagick::FILTER_LANCZOS, 1);
        if (!$thumb->writeImage(dirname(Yii::$app->request->scriptFile) . '/uploads/documenten/' . $folder_name . '/' . $file_name . '_t.jpg')) {
            return false;
        }

        $thumb->clear();

        return true;
    }

    public function saveDocument($document_id, $tag_id, $file) {
        if ($file) {
            $tags = Tag::findOne($tag_id);
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::getAlias('@app' . '/../uploads/' . $file['location'] . '/' . $file['file']));
            $file_name = $fileInfo['filename'];

            if (!is_dir(Yii::getAlias('@app' . '/../uploads/documenten/'))) {
                mkdir(Yii::getAlias('@app' . '/../uploads/documenten/'));
            }

            if (!is_dir(Yii::getAlias('@app' . '/../uploads/documenten/' . $folder_name . '/'))) {
                mkdir(Yii::getAlias('@app' . '/../uploads/documenten/' . $folder_name . '/'));
            }

            if (!$this->genThumbs($file, $folder_name, $file_name)) {
                return false;
            }

            $fileContents = file_get_contents(Yii::getAlias('@app' . '/../uploads/' . $file['location'] . '/' . $file['file']));
            if (!$fileContents || !file_put_contents(Yii::getAlias('@app' . '/../uploads/documenten/' . $folder_name . '/' . $file_name . '.pdf', $fileContents))) {
                return false;
            }
            echo 'sweet';

            $this->updateAll(['state' => 0], 'document_id=' . $document_id);

            $attributes['document_id'] = $document_id;
            $attributes['file'] = $file_name;
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.pdf';
            $attributes['state'] = 1;
            $this->setIsNewRecord(true);
            $this->attributes = $attributes;
            if (!$this->insert())
                return false;
        }
        return true;
    }

}
