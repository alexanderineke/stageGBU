<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%image_tag}}".
 *
 * @property integer $id
 * @property integer $image_id
 * @property integer $tag_id
 * @property integer $state
 */
class ImageTag extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%image_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['image_id', 'tag_id'], 'required'],
            [['image_id', 'tag_id', 'state'], 'integer'],
            [['id', 'image_id', 'tag_id', 'state'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'image_id' => 'Image',
            'tag_id' => 'Tag',
            'state' => 'State',
        ];
    }

    public function getTags() {
        return $this->belongs_to(Tag::className(), ['id' => 'tag_id']);
    }

    public function search($params) {
        $query = ImageTag::find();
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
                    ['like', 'image_id', $this->image_id],
                    ['like', 'tag_id', $this->tag_id],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    //voegt Imagetag toe aan de database
    public function add($image_id, $tagIds) {
        $inDB = [];
        foreach ($this->check($image_id, $tagIds) as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            Yii::$app->db->createCommand()
                    ->insert('tbl_image_tag', [
                        'image_id' => $image_id,
                        'tag_id' => $i,
                        'state' => 1])
                    ->execute();
            $v++;
            if ($v == sizeof($notInDB)) {
                return true;
            }
        }
    }

    //checkt of de model al de tag bevat
    public function check($image_id, $tagIds) {
        foreach ($tagIds as $i => $tag) {
            $query = ImageTag::find()
                    ->select(['tag_id'])
                    ->where(['image_id' => $image_id])
                    ->andWhere(['tag_id' => $i])
                    ->andWhere(['state' => 1])
                    ->all();
        }
        return $query;
    }

    //haalt alle tags op van het document
    public function getTag($image_id) {
        $query = ImageTag::find()
                ->select(['tag_id'])
                ->andFilterWhere(['image_id' => $image_id])
                ->andFilterWhere(['state' => 1])
                ->limit(1);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        return $dataProvider;
    }

}
