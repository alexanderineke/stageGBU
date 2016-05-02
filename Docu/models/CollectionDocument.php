<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_document}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $document_id
 * @property integer $state
 */
class CollectionDocument extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collection_document}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collection_id', 'document_id', 'state'], 'required'],
            [['collection_id', 'document_id', 'state'], 'integer']
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
            'document_id' => 'Document ID',
            'state' => 'State',
        ];
    }
}
