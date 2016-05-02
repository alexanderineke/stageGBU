<?php

namespace app\models;

use Yii;

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
class DocumentFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%document_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'file', 'format', 'location'], 'required'],
            [['document_id', 'state'], 'integer'],
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
            'document_id' => 'Document ID',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
            'state' => 'State',
        ];
    }
}
