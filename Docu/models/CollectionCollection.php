<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_collection}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $collection_col_id
 * @property integer $state
 */
class CollectionCollection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collection_collection}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collection_id', 'collection_col_id', 'state'], 'required'],
            [['collection_id', 'collection_col_id', 'state'], 'integer']
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
            'collection_col_id' => 'Collection Col ID',
            'state' => 'State',
        ];
    }
}
