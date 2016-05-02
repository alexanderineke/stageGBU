<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%image}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property integer $year
 * @property string $owner
 * @property string $created_on
 * @property string $modified_on
 * @property integer $published
 *
 * @property User $user
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'created_on', 'modified_on', 'published'], 'required'],
            [['user_id', 'year', 'published'], 'integer'],
            [['description'], 'string'],
            [['created_on', 'modified_on'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['owner'], 'string', 'max' => 45]
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
            'year' => 'Year',
            'owner' => 'Owner',
            'created_on' => 'Created On',
            'modified_on' => 'Modified On',
            'published' => 'Published',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
