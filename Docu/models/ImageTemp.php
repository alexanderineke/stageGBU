<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%image_temp}}".
 *
 * @property string $id
 * @property string $create_date
 * @property integer $user_id
 * @property string $file
 * @property string $format
 * @property string $location
 */
class ImageTemp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image_temp}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'user_id', 'file', 'format', 'location'], 'required'],
            [['create_date'], 'safe'],
            [['user_id'], 'integer'],
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
            'create_date' => 'Create Date',
            'user_id' => 'User ID',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
        ];
    }
}
