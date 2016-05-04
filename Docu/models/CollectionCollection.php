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

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'collection_id', $this->collection_id])
                ->andFilterWhere(['like', 'collection_col_id', $this->collection_col_id])
                ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }

    public function add($collection_col_id, $collection_id) {
            $model = $sql_collection_col->createCommand("INSERT INTO tbl_collection_collection (collection_col_id, collection_id, state) VALUES (:collection_col_id, :collection_id, 1)");
            $model->bindParam(":collection_col_id", $collection_col_id, ":collection_id", $collection_id);
            $model->execute();

            $model = $sql_collection->createCommand("UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id");
            $model->bindParam(":collection_id", $collection_id);
            $model->execute();
            return true;
    }

    public function deleteCollection($collection_col_id, $collection_id) {

        if (!empty($collection_id)) {
            $model = $sql_collection_col->createCommand("DELETE FROM tbl_collection_collection WHERE collection_col_id = :collection_col_id AND collection_id = :collection_col_id");
            $model->bindParam(":collection_col_id", $collection_col_id, ":collection_id", $collection_id);
            $model->execute();

            $model = $sql_collection->createCommand("UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id");
            $model->bindParam(":collection_id", $collection_id);
            $model->execute();
            return true;
        } else {
            $model = $sql_collection_col->createCommand("DELETE FROM tbl_collection_collection WHERE collection_col_id = :collection_col_id OR collection_id = :collection_col_id");
            $model->bindParam(":collection_col_id", $collection_col_id);
            $model->execute();

            $model->$sql_collection = "UPDATE tbl_collection SET modified_on = NOW() WHERE id IN (:collection_id)";
            $model->bindParam(":collection_id", $collection_id);
            $model->execute();

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
