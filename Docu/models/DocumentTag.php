<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%document_tag}}".
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $tag_id
 * @property integer $state
 */
class DocumentTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%document_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'tag_id'], 'required'],
            [['document_id', 'tag_id', 'state'], 'integer']
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
            'tag_id' => 'Tag ID',
            'state' => 'State',
        ];
    }
}
