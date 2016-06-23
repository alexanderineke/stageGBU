<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_collection}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $collection_col_id
 * @property integer $state
 */
class CollectionCollection extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%collection_collection}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['collection_id', 'collection_col_id', 'state'], 'required'],
            [['collection_id', 'collection_col_id', 'state'], 'integer'],
            [['id', 'collection_id', 'collection_col_id', 'state'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'collection_id' => 'Collectie',
            'collection_col_id' => 'Collectie',
            'state' => 'Status',
        ];
    }

    public function search() {
        $query = CollectionCollection::find();
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
                    ['like', 'collection_id', $this->collection_id],
                    ['like', 'collection_col_id', $this->collection_col_id],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    public function add($collection_col_id, $collection_id) {
        Yii::$app->db->createCommand()
                ->insert('tbl_collection_collection', [
                    'collection_col_id' => $collection_col_id,
                    'collection_id' => $collection_id,
                    'state' => 1])
                ->execute();


        Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                ->bindValue(':date', date("Y-m-d H:i:s"))
                ->bindValue(':collection_id', $collection_id)
                ->execute();
        return true;
    }

    public function deleteCollection($collection_col_id, $collection_id) {

        if (!empty($collection_id)) {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_collection WHERE collection_col_id = :collection_col_id AND collection_id = :collection_col_id")
                    ->bindValue(":collection_col_id", $collection_col_id)
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            return true;
        } else {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_collection WHERE collection_col_id = :collection_col_id OR collection_id = :collection_col_id")
                    ->bindValue(":collection_col_id", $collection_col_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            return true;
        }
    }

    public function getCollection() {
        return $this->hasMany(Collection::className(), ['id' => 'collection_id']);
    }

    public function getCollection_col() {
        return $this->hasMany(Collection::className(), ['id' => 'collection_col_id']);
    }

}
