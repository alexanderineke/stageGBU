<?php

namespace app\models;

use Yii;

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
            [['format'], 'string', 'max' => 4]
        ];
    }

    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'audio' => [self::BELONGS_TO, 'Audio', 'audio_id'],
        );
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

    public function search($params) {
        $query = AudioFile::find();
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
                ->andFilterWhere(['like', 'audio_id', $this->audio_id])
                ->andFilterWhere(['like', 'file', $this->file])
                ->andFilterWhere(['like', 'format', $this->format])
                ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }

    public function saveAudio($audio_id, $tag_id, $file) {
        $errorOccured = false;

        if ($file) {
            //Bestandsnamen, bestandslocaties
            $tags = Tag::model()->find('id=?', array($tag_id));
            $folder_name = preg_replace('/[^a-z0-9-_\.]/', '', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);

            //Map voor audio files
            if (!is_dir(Yii::app()->basePath . '/../uploads/audio/')) {
                mkdir(Yii::app()->basePath . '/../uploads/audio/');
            }

            //Map voor normale versie
            if (!is_dir(Yii::app()->basePath . '/../uploads/audio/' . $folder_name . '/')) {
                mkdir(Yii::app()->basePath . '/../uploads/audio/' . $folder_name . '/');
            }

            //Schrijf bestand weg
            $fileContents = file_get_contents(Yii::app()->basePath . '/../uploads/' . $file['location'] . '/' . $file['file']);
            file_put_contents(Yii::app()->basePath . '/../uploads/audio/' . $folder_name . '/' . $fileInfo['filename'] . '.mp3', $fileContents);

            $this->updateAll(['state' => 0], 'audio_id=' . $audio_id);

            //Insert de nieuwe doucment
            /*
            $attributes['audio_id'] = $audio_id;
            $attributes['file'] = $fileInfo['filename'];
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.mp3';
            $attributes['state'] = 1;
            $this->setIsNewRecord(true);
            $this->attributes = $attributes;
             */
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
