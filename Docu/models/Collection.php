<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $created_on
 * @property string $modified_on
 * @property integer $published
 */
class Collection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collection}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'published'], 'integer'],
            [['title', 'description', 'created_on', 'modified_on'], 'required'],
            [['description'], 'string'],
            [['created_on', 'modified_on'], 'safe'],
            [['title'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'description' => 'Description',
            'created_on' => 'Created On',
            'modified_on' => 'Modified On',
            'published' => 'Published',
        ];
    }
}
