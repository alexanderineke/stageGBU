<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%document}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property integer $year
 * @property string $owner
 * @property string $created_on
 * @property string $modified_on
 * @property integer $published
 *
 * @property User $user
 */
class Document extends ActiveRecord {

    public $tag_search, $count, $tagName;

    public function getTagsHelper() {
        return implode(', ', array_values(Html::listData($this->tags, 'id', 'name')));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function tableName() {
        return '{{%document}}';
    }

    public function rules() {
        return [
            [['user_id', 'title', 'published'], 'required'],
            [['user_id', 'year', 'published'], 'integer'],
            [['description', 'content'], 'string'],
            [['description'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['owner'], 'string', 'max' => 45]
        ];
    }

    public function getUser() {
        return $this->Belongs_to(User::className(), ['id' => 'user_id']);
    }

    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_document_tag', ['document_id' => 'id']);
    }

    public function getDocumentTags() {
        return $this->hasMany(DocumentTag::className(), ['id' => 'document_id']);
    }

    public function getDocuments() {
        return $this->hasMany(DocumentFile::className(), ['id' => 'document_id'])->andWhere('state=1');
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
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'user_id', $this->user_id])
                ->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'year', $this->year])
                ->andFilterWhere(['like', 'owner', $this->owner])
                ->andFilterWhere(['like', 'published', $this->published]);

        return $dataProvider;
    }

    public function searchDocuments($model, $query) {
        $q = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['attributes' => ['tag_search' => [
                        'asc' => ['tags.slug' => SORT_ASC],
                        'desc' => ['tags.slug' => SORT_DESC],
                    ]],
                '*'],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $q
                ->orWhere(['like', 'description', $query])
                ->orWhere(['like', 'year', $query])
                ->orWhere(['like', 'title', $query])
                ->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'tags.slug', $this->year])
                ->andFilterWhere(['like', 'year', $this->owner])
                ->andFilterWhere(['like', 'tags.state', $this->published])
                ->groupBy('t.id');

        return $dataProvider;
    }

}
