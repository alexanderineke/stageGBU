<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audio_tag}}".
 *
 * @property integer $id
 * @property integer $audio_id
 * @property integer $tag_id
 * @property integer $state
 */
class AudioTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audio_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audio_id', 'tag_id'], 'required'],
            [['audio_id', 'tag_id', 'state'], 'integer']
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
            'tag_id' => 'Tag ID',
            'state' => 'State',
        ];
    }
}
