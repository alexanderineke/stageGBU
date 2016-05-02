<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_image}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $image_id
 * @property integer $state
 */
class CollectionImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collection_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collection_id', 'image_id', 'state'], 'required'],
            [['collection_id', 'image_id', 'state'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection_id' => 'Collection ID',
            'image_id' => 'Image ID',
            'state' => 'State',
        ];
    }
}
