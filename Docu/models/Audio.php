<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audio}}".
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
 */
class Audio extends \yii\db\ActiveRecord {

    public $tag_search, $count, $tagName;

    public function getTagsHelper() {
        return implode(', ', array_values(CHtml::listData($this->tags, 'id', 'name')));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%audio}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
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
    public function attributeLabels() {
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

    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'tags' => array(self::MANY_MANY, 'Tag', 'tbl_audio_tag(audio_id, tag_id)'),
            'audio_tags' => array(self::HAS_MANY, 'AudioTag', 'audio_id'),
            'audios' => array(self::HAS_MANY, 'AudioFile', 'audio_id', 'condition' => 'state=1'),
        ];
    }

}