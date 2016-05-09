<?php

namespace app\models;

use Yii;

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
class ImageFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
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
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'image_id', $this->image_id])
                ->andFilterWhere(['like', 'file', $this->file])
                ->andFilterWhere(['like', 'format', $this->format])
                ->andFilterWhere(['like', 'location', $this->location]);
               
        return $dataProvider;
    }
    
    public function saveImage($image_id, $tag_id, $file) {
        $errorOccured = false;

        if ($file) {
            //Bestandsnamen, bestandslocaties
            $tags = Tag::model()->find('id=?', [$tag_id]);
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);

            //Map voor afbeeldingen
            if (!is_dir(Yii::app()->basePath . '/../uploads/afbeeldingen/')) {
                mkdir(Yii::app()->basePath . '/../uploads/afbeeldingen/');
            }

            //Map voor de normale versie (max: 750x500)
            if (!is_dir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/')) {
                mkdir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/');
            }

            //Map voor de thumbnail (fixed: 100x100)
            if (!is_dir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/thumb/')) {
                mkdir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/thumb/');
            }

            //Map voor de full versie
            if (!is_dir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/full/')) {
                mkdir(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/full/');
            }

            //Genereer normale versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);
            $thumb->resize(750, 500);
            if (!$thumb->save(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/' . $fileInfo['filename'] . '.jpg', 'JPG')) {
                $errorOccured = true;
            }
            //Genereer thumbnail versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);
            $thumb->adaptiveResize(100, 100);
            if (!$thumb->save(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/thumb/' . $fileInfo['filename'] . '.jpg', 'JPG')) {
                $errorOccured = true;
            }
            //Genereer full versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);
            if (!$thumb->save(Yii::app()->basePath . '/../uploads/afbeeldingen/' . $folder_name . '/full/' . $fileInfo['filename'] . '.jpg', 'JPG')) {
                $errorOccured = true;
            }
            //Up into the clouds~
            // $fileContents = file_get_contents(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            // require_once 'google-api-php-client/src/Google_Client.php';
            // require_once 'google-api-php-client/src/contrib/Google_StorageService.php';
            // $client = new Google_Client();
            // $client->setApplicationName('Documentatie Centrum Website');
            // $key = file_get_contents('protected/6ecc407906ebf13b86f5413e89de1ca36ae1e7fe-privatekey.p12');
            // $client->setAssertionCredentials(new Google_AssertionCredentials('405222382916-rr450c1pf978fcfnhkfeq37n3er0t591@developer.gserviceaccount.com',
            //   array('https://www.googleapis.com/auth/devstorage.full_control'),
            //   $key));
            // $StorageService = new Google_StorageService($client);
            // $objects = $StorageService->objects;
            // $postbody = array('data' => $fileContents, 'mimeType' =>'image/jpeg');
            // $gso = new Google_StorageObject();
            // $gso->setName($folder_name.'/'.$fileInfo['filename'].'.jpg');
            // $resp = $objects->insert('dcu-image', $gso, $postbody);
            //Alle oude afbeeldingen op state 0
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

    public function getImage(){
        return $this->Belongs_to(Image::className(), ['id' => 'image_id']);
    }
}
