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
            'image_id' => 'Image ID',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
            'state' => 'State',
        ];
    }
}
