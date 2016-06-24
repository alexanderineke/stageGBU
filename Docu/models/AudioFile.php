<?php

namespace app\models;

use Yii;
use yii\helpers\BaseFileHelper;

/**
 * This is the model class for table "{{%audio_file}}".
 *
 * @property integer $id
 * @property integer $audio_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class AudioFile extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%audio_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['audio_id', 'file', 'format', 'location'], 'required'],
            [['audio_id', 'state'], 'integer'],
            [['file', 'location'], 'string', 'max' => 255],
            [['format'], 'string', 'max' => 4],
            [['id, audio_id, file, format, location'], 'safe', 'on' => 'search']
        ];
    }

    public function getAudio() {
        return $this->Belongs_to(Audio::className(), ['id' => 'audio_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'audio_id' => 'Audio',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
        ];
    }

    public function search() {
        $query = AudioFile::find();
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
                    ['like', 'audio_id', $this->audio_id],
                    ['like', 'file', $this->file],
                    ['like', 'format', $this->format],
                    ['like', 'location', $this->location]]);

        return $dataProvider;
    }

    public function saveAudio($audio_id, $tag_id, $file) {
        $errorOccured = false;

        if ($file) {
            //Bestandsnamen, bestandslocaties
            $tags = Tag::find()->where(['id' => $tag_id])->one();
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);

            //Map voor audio files
            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR);
            }


            //Map voor normale versie
            if (!is_dir(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR)) {
                BaseFileHelper::createDirectory(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . $folder_name . '/');
            }

            //Schrijf bestand weg
            $fileContents = file_get_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file['location'] . DIRECTORY_SEPARATOR . $file['file']);

            file_put_contents(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $fileInfo['filename'] . '.mp3', $fileContents);
            $this->updateAll(['state' => 0], 'audio_id=' . $audio_id);

            $this->audio_id = $audio_id;
            $this->file = $fileInfo['filename'];
            $this->location = $folder_name;
            $this->format = '.mp3';
            $this->state = 1;
            $this->setIsNewRecord(true);
            if (!$this->insert()) {
                $errorOccured = true;
            }
        }
        if (!$errorOccured) {
            return true;
        }
    }

}
