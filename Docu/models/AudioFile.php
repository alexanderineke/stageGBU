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
class AudioFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audio_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audio_id', 'file', 'format', 'location'], 'required'],
            [['audio_id', 'state'], 'integer'],
            [['file', 'location'], 'string', 'max' => 255],
            [['format'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'audio_id' => 'Audio ID',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
            'state' => 'State',
        ];
    }
}
