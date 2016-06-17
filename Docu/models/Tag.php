<?php

namespace app\models;

use Yii;
use app\models\Image;
use app\models\Document;
use app\models\Audio;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $state
 */
class Tag extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['state'], 'integer'],
            [['slug'], 'unique'],
            [['name', 'slug'], 'string', 'max' => 32],
            [['id', 'name', 'slug', 'state'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Naam',
            'slug' => 'Veilige naam',
            'state' => 'Status',
        ];
    }

    public function getImages() {
        return $this->hasMany(Image::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_image_tag', ['image_id' => 'id']);
    }

    public function getDocuments() {
        return $this->hasMany(Document::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_document_tag', ['document_id' => 'id']);
    }

    public function getAudios() {
        return $this->hasMany(Audio::className(), ['id' => 'tag_id'])
                        ->viaTable('tbl_audio_tag', ['audio_id' => 'id']);
    }

    public function search($params) {
        $query = Tag::find();
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
                    ['like', 'name', $this->name],
                    ['like', 'slug', $this->slug],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    // Warning: Please modify the following code to remove attributes that
    // should not be searched.
    public function add($tagArr) {
        //       $addedSlugs = [];
//        foreach ($tagArr as $slug => $name) {
//            $addedSlugs[] = $slug;
//        }
        foreach ($tagArr as $i => $tag) {
            Yii::$app->db->createCommand()
                    ->insert('tbl_tag', [
                        'name' => $tag,
                        'slug' => $i,
                        'state' => 1])
                    ->execute();
            $addedTags[] = Yii::$app->db->getLastInsertID();
        }
      //  print_r($addedTags);
        if (sizeof($addedTags) == sizeof($tagArr)) {
            return $addedTags;
        }
    }

    public function check($tags) {
        $slugs = [];
        foreach ($tags as $slug => $name) {
            $slugs[] = $slug;
        }
        $query = Tag::find()
                ->select(['id', 'slug'])
                // ->andFilterWhere(['id' => $id])
                ->where(['slug' => $slugs])
                ->andWhere(['state' => 1])
                ->all();
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
        return $query;
    }

    public function findTags($slugArr) {
        foreach ($slugArr as $i => $tag) {
            $query = Tag::find()
                    ->select(['id'])
                    //    ->andFilterWhere(['id' => $id])
                    //    ->andFilterWhere(['name' => $name])
                    ->andWhere(['state' => 1])
                    ->andWhere(['slug' => $i])
                    ->all();
        }
        return $query;
    }

    public function findTagsByID($term) {
        $query = Tag::find()
                ->andFilterWhere(['id' => $this->id])
                ->andFilterWhere(['name' => $this->name])
                ->andFilterWhere(['state' => 1])
                ->andFilterWhere(['id' => $term])
                ->limit(10)
                ->all();
        return $query;
    }

}
