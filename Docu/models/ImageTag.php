<?php

namespace app\models;

use Yii;

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

    public function getTags(){
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

    public function add($image_id, $tagIds) {
        $inDB = [];
        foreach ($this->check($image_id, $tagIds) as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            $sql = "insert into tbl_image_tag (image_id, tag_id, state) values (:image_id, :tag_id, 1)";
            $parameters = [":image_id" => $image_id,
                ":tag_id" => $i];
            if (Yii::$app->db->createCommand($sql)->execute($parameters)) {
                $v++;
            }
        }

        if ($v == sizeof($notInDB)) {
            return true;
        }
    }

    public function check($image_id, $tagIds) {

        $query = ImageTag::find()
                ->andFilterWhere(['image_id' => $image_id])
                ->andFilterWhere(['tag_id' => $tagIds])
                ->andFilterWhere(['state' => 1]);
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

    public function deleteTags($image_id, $tagIds) {
        $query = ImageTag::find()
                ->andFilterWhere(['image_id' => $image_id])
                ->andFilterWhere(['tag_id' => $tagIds])
                ->andFilterWhere(['state' => 1])
                ->delete()
                ->execute();
        return $query;
    }

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
