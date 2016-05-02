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
            'user_id' => 'Naam van Uploader',
            'title' => 'Titel',
            'description' => 'Omschrijving',
            'year' => 'Jaar',
            'owner' => 'Eigenaar',
            'created_on' => 'Aanmaakdatum',
            'modified_on' => 'Laatste wijzigingsdatum',
            'published' => 'Gepubliceerd',
            'file' => 'Bestand',
        ];
    }
/*
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'user' => [self::BELONGS_TO, 'User', 'user_id'],
            'tags' => [self::MANY_MANY, 'Tag', 'tbl_audio_tag(audio_id, tag_id)'],
            'audio_tags' => [self::HAS_MANY, 'AudioTag', 'audio_id'],
            'audios' => [self::HAS_MANY, 'AudioFile', 'audio_id', 'condition' => 'state=1'],
        ];
    }
    */
    public function getUser(){
        return $this->BELONGS_TO(User::className(), ['id' => 'user_id']);
    }
    
    public function getTags(){
        return $this->hasMany(Tag::className(),['id' => 'tag_id'])
                ->viaTable('tbl_audio_tag', ['audio_id' => 'id']);
    }
    
    public function getAudio_tags(){
        $this->HasMany(AudioTag::className(),['id' => 'audio_id']);
    }
    
    public function getAudios(){
        $this->hasMany(AudioFile::className(), ['id' => 'audio_id']);
    }

    public function search($params) {
        $query = Audio::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'user_id', $this->user_id])
                ->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'year', $this->year])
                ->andFilterWhere(['like', 'owner', $this->owner])
                ->andFilterWhere(['like', 'published', $this->published]);

        return $dataProvider;
    }

}
