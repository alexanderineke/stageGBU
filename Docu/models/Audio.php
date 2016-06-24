<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use app\models\AudioFile;

class Audio extends \yii\db\ActiveRecord {

    public $tag_search, $count, $tagName;

    public function getTagsHelper() {
        return implode(', ', array_values(Html::listData($this->tags, 'id', 'name')));
    }

    public static function tableName() {
        return '{{%audio}}';
    }

    public function rules() {
        return [
            [['user_id', 'title', 'published'], 'required'],
            [['user_id', 'year', 'published'], 'integer'],
            [['description'], 'string'],
            [['tag_search, id, user_id, title, description, year, owner, published'], 'safe', 'on' => 'search'],
            [['title'], 'string', 'max' => 64],
            [['owner'], 'string', 'max' => 45]
        ];
    }

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

    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_audio_tag', ['audio_id' => 'id']);
    }

    public function getAudios() {
        return $this->hasOne(AudioFile::className(), ['audio_id' => 'id'])->andWhere('state=1');
    }

    public function search() {
        $query = Audio::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere([['like', 'id', $this->id],
                    ['like', 'user_id', $this->user_id],
                    ['like', 'title', $this->title],
                    ['like', 'description', $this->description],
                    ['like', 'year', $this->year],
                    ['like', 'owner', $this->owner],
                    ['like', 'published', $this->published]]);

        return $dataProvider;
    }

}
