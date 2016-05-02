<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%image_tag}}".
 *
 * @property integer $id
 * @property integer $image_id
 * @property integer $tag_id
 * @property integer $state
 */
class ImageTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image_id', 'tag_id'], 'required'],
            [['image_id', 'tag_id', 'state'], 'integer']
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
            'tag_id' => 'Tag ID',
            'state' => 'State',
        ];
    }
}
