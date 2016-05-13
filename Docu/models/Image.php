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
class Image extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'title', 'published'], 'required'],
            [['user_id', 'year', 'published'], 'integer'],
            [['description'], 'string'],
            [['created_on', 'modified_on'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['owner'], 'string', 'max' => 45],
            [['id', 'user_id', 'title', 'description', 'year', 'owner'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * @inheritdoc
     */
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
            'modified_on' => 'Laatste wijzigingsdatum',
            'published' => 'Gepubliceerd',
        ];
    }

    public function search($params) {
        $query = Image::find();
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
                ->andFilterWhere([['like', 'id', $this->id],
                    ['like', 'user_id', $this->user_id],
                    ['like', 'title', $this->title],
                    ['like', 'description', $this->description],
                    ['like', 'year', $this->year],
                    ['like', 'owner', $this->owner],
                    ['like', 'published', $this->published]]);

        return $dataProvider;
    }

    public function getUser() {
        return $this->Belongs_to(User::className(), ['id' => 'user_id']);
    }

    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'user_id'])
                        ->viaTable('tbl_image_tag', ['image_id' => 'id']);
    }

    public function getImages() {
        return $this->hasMany(ImageFile::className(), ['id' => 'image_id'])->andWhere('state=1');
    }

}
