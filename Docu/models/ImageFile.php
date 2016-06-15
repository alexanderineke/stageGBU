<?php

namespace app\models;

use Yii;
use yii\helpers\BaseFileHelper;
use yii\data\ActiveDataProvider;
use app\models\Tag;
/**
 * This is the model class for table "{{%image_file}}".
 *
 * @property integer $id
 * @property integer $image_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class ImageFile extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%image_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['image_id', 'file', 'format', 'location'], 'required'],
            [['image_id', 'state'], 'integer'],
            [['file', 'location'], 'string', 'max' => 255],
            [['format'], 'string', 'max' => 4],
            [['id', 'image_id', 'file', 'format', 'location', 'state'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'image_id' => 'Image',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
        ];
    }

    public function search($params) {
        $query = Image::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere([['like', 'id', $this->id],
                    ['like', 'image_id', $this->image_id],
                    ['like', 'file', $this->file],
                    ['like', 'format', $this->format],
                    ['like', 'location', $this->location]]);

        return $dataProvider;
    }

    public function saveImage($image_id, $tag_id, $file) {
        $errorOccured = false;

        if ($file) {
            //Bestandsnamen, bestandslocaties
            $tags = Tag::find()->where(['id' => $tag_id])->one();
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);

            //Map voor afbeeldingen
            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'afbeeldingen' . DIRECTORY_SEPARATOR)) {
               BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'afbeeldingen' . DIRECTORY_SEPARATOR);
            }

            //Map voor de normale versie (max: 750x500)
            if (!is_dir(\Yii::getAlias('@web') . '/uploads/afbeeldingen/' . $folder_name . '/')) {
                BaseFileHelper::createDirectory(Yii::getAlias('@web') . '/uploads/afbeeldingen/' . $folder_name . '/');
            }

            //Map voor de thumbnail (fixed: 100x100)
            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. 'afbeeldingen' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. 'afbeeldingen' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR);
            }

            //Map voor de full versie
            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. 'afbeeldingen' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . 'full' . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. 'afbeeldingen' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . 'full' . DIRECTORY_SEPARATOR);
            }

//            //Genereer normale versie 
//            $thumb = \Yii::$app->phpThumb->create(\Yii::getAlias('uploads/' . $file['location'] . '/' . $file['file']));
//            $thumb->resize(750, 500);
//            if (!$thumb->save(\Yii::getAlias('uploads/afbeeldingen/' . $folder_name . '/' . $fileInfo['filename'] . '.jpg', 'JPG'))) {
//                $errorOccured = true;
//            }
//            //Genereer thumbnail versie
//            $thumb = \Yii::$app->phpThumb->create(\Yii::getAlias('uploads/' . $file['location'] . '/' . $file['file']));
//            $thumb->adaptiveResize(100, 100);
//            if (!$thumb->save(\Yii::getAlias('uploads/afbeeldingen/' . $folder_name . '/thumb/' . $fileInfo['filename'] . '.jpg', 'JPG'))) {
//                $errorOccured = true;
//            }
//            //Genereer full versie
//            $thumb = \Yii::$app->phpThumb->create(\Yii::getAlias('uploads/' . $file['location'] . '/' . $file['file']));
//            if (!$thumb->save(\Yii::getAlias('uploads/afbeeldingen/' . $folder_name . '/full/' . $fileInfo['filename'] . '.jpg', 'JPG'))) {
//                $errorOccured = true;
//            }

            $this->updateAll(['state' => 0], 'image_id=' . $image_id);

            //Insert de nieuwe afbeelding
            $attributes['image_id'] = $image_id;
            $attributes['file'] = $fileInfo['filename'];
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.jpg';
            $attributes['state'] = 1;
            $this->setIsNewRecord(true);
            $this->attributes = $attributes;
            if (!$this->insert()) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

    public function getImage() {
        return $this->Belongs_to(Image::className(), ['id' => 'image_id']);
    }
}
