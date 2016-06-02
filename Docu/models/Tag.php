<?php

namespace app\models;

use Yii;

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
    public function add($tagArr, $slugArr) {
        $addedTags = [];
        foreach (Tag::find()
                ->where(['name' => $tagArr])
                ->andFilterWhere(['slug' => $slugArr])
                ->exists() as $i) {
            $addedTags[] = $i->tag_id;
        }
        $notInDB = array_diff($tagArr, $addedTags);
        $v = 0;
        foreach ($addedTags as $i) {
            $sql->createCommand()
                    ->insert('tbl_tag', [
                        'name' => $tagArr,
                        'slug' => $slugArr,
                        'state' => 1])
                    ->execute();
            $v++;
            if ($v == sizeof($notInDB)) {
                return true;
            }
        }
    }

    public function check($slugArr) {
        $query = Tag::find()
                ->andFilterWhere(['id' => $id])
                ->andFilterWhere(['slug' => $slugArr])
                ->andFilterWhere(['state' => 1])
                ->all()
                ->execute();
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

    public function findTags($term) {
        $query = Tag::find()
                ->andFilterWhere(['id' => $id])
                ->andFilterWhere(['name' => $name])
                ->andWhere(['state' => 1])
                ->andWhere(['slug' => $term])
                ->limit(10)
                ->all()
                ->execute();
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
