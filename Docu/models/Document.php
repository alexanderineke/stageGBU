<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Document extends ActiveRecord {

    public $tag_search, $count, $tagName;

    public function getTagsHelper() {
        return implode(', ', array_values(Html::listData($this->tags, 'id', 'name')));
    }

    public static function tableName() {
        return '{{%document}}';
    }

    public function rules() {
        return [
            [['user_id', 'title', 'published'], 'required'],
            [['user_id', 'year', 'published'], 'integer'],
            [['description', 'content'], 'string'],
            [['tag_search, id, user_id, title, description, year, owner, published'], 'safe', 'on' => 'search'],
            [['title'], 'string', 'max' => 64],
            [['owner'], 'string', 'max' => 45]
        ];
    }
/*
    public function getUser() {
        return $this->Belongs_to(User::className(), ['id' => 'user_id']);
    }
*/
    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_document_tag', ['document_id' => 'id']);
    }

    public function getDocumentTags() {
        return $this->hasMany(DocumentTag::className(), ['id' => 'document_id']);
    }

    public function getDocuments() {
        return $this->hasMany(DocumentFile::className(), ['document_id' => 'id'])->andWhere('state=1');
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Naam van uploader',
            'title' => 'Titel',
            'description' => 'Omschrijving',
            'tags' => 'Steekwoorden',
            'year' => 'Jaar',
            'owner' => 'Eigenaar',
            'created_on' => 'Aanmaakdatum',
            'modified_on' => 'Laatste wijziging',
            'published' => 'Gepubliceerd',
            'file' => 'Bestand',
            'collection' => 'Collectie',
        ];
    }

    public function search() {
        $query = Document::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
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

    public function searchDocuments($model, $q) {
        $query = Document::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
                ->with('tags')
                ->andFilterWhere(['or',
                    ['like', 'description', $q],
                    ['like', 'year', $q],
                    ['like', 'title', $q],
                    ['like', 'tags.slug', $q]])
                ->andFilterWhere([
                    ['like', 'title', $model->title],
                    ['like', 'description', $q],
                    ['like', 'tags.slug', $q],
                    ['like', 'year', $q],
                    ['like', 'tags.state', 1]
                ])
                ->groupBy('t.id');
        $dataProvider->setSort([
            'tag_search' => [
                'asc' => 'tags.slug',
                'desc' => 'tags.slug DESC',
            ],
            '*',
        ]);

        return $dataProvider;
    }

}
