<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\ImageFile;

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
class Collection extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%collection}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'published'], 'integer'],
            [['title', 'published'], 'required'],
            [['description'], 'string'],
            [['id', 'user_id', 'title', 'description', 'created_on', 'modified_on', 'published'], 'safe'],
            [['title'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Gebruiker',
            'title' => 'Titel',
            'description' => 'Omschrijving',
            'created_on' => 'Aanmaakdatum',
            'modified_on' => 'Laatste wijzigingsdatum',
            'published' => 'Gepubliceerd',
        ];
    }

    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $query = Collection::find();
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
                    ['like', 'created_on', $this->year],
                    ['like', 'modified_on', $this->owner],
                    ['like', 'published', $this->published]]);

        return $dataProvider;
    }

    public function checkOwnership() {
        $query = Collection::find()
                ->where(['id', $this->id])
                ->where(['user_id', Yii::$app->user->identity->id]);

        if (!empty($query)) {
            return true;
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Collection the static model class
     */
    public function getCollection_images() {
        return $this->hasMany(CollectionImage::className(), ['id' => 'collection_id']);
    }

    public function getImages() {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
                        ->viaTable('tbl_collection_image', ['collection_id' => 'id']);
    }

    public function getThumb() {
        return $this->hasOne(ImageFile::className(), ['image_id' => 'image_id'])
                        ->viaTable('tbl_collection_image', ['collection_id' => 'id']);
    }

    public function getCollection_documents() {
        return $this->hasMany(CollectionDocument::className(), ['id' => 'collection_id']);
    }

    public function getCollection_audios() {
        return $this->hasMany(CollectionAudio::className(), ['id' => 'collection_id']);
    }

    public function getDocuments() {
        return $this->hasMany(Document::className(), ['id' => 'document_id'])
                        ->viaTable('tbl_collection_document', ['collection_id' => 'id']);
    }

    public function getCollection_collections() {
        return $this->hasMany(CollectionCollection::className(), ['id' => 'collection_id']);
    }

    public function getCollections() {
        return $this->hasMany(Collection::className(), ['id' => 'collection_col_id'])
                        ->viaTable('tbl_collection_collection', ['collection_id' => 'id']);
    }

    public function getAudios() {
        return $this->hasMany(Audio::className(), ['id' => 'audio_id'])
                        ->viaTable('tbl_collection_audio', ['collection_id' => 'id']);
    }

}
