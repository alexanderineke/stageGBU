<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_image}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $image_id
 * @property integer $state
 */
class CollectionImage extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%collection_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['collection_id', 'image_id', 'state'], 'required'],
            [['collection_id', 'image_id', 'state'], 'integer'],
            [['id', 'collection_id', 'image_id', 'state'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'collection_id' => 'Collectie',
            'image_id' => 'Afbeelding',
            'state' => 'State',
        ];
    }

    public function search() {
        $query = CollectionImage::find();
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
                ->andFilterWhere(['like', 'image_id', $this->document_id])
                ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }

    public static function add($image_id, $collection_id) {
        $model = $sql_image->createCommand("INSERT INTO tbl_collection_document (document_id, collection_id, state) VALUES (:document_id, :collection_id, 1)");
        $model->bindParam(":document_id", $document_id, ":collection_id", $collection_id);
        $model->execute();

        $model = $sql_collection->createCommand("UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id");
        $model->bindParam(":collection_id", $collection_id);
        $model->execute();
        return true;
    }

    public function deleteImage($image_id, $collection_id) {

        if (!empty($collection_id)) {
            $model = $sql_image->createCommand("DELETE FROM tbl_collection_image WHERE image_id = :image_id AND collection_id = :collection_id");
            $model->bindParam(":document_id", $document_id, ":collection_id", $collection_id);
            $model->execute();

            $model = $sql_collection->createCommand("UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id");
            $model->bindParam(":collection_id", $collection_id);
            $model->execute();
            return true;
        } else {
            $model = $sql_image->createCommand("DELETE FROM tbl_collection_image WHERE image_id = :image_id");
            $model->bindParam(":document_id", $document_id);
            $model->execute();

            $model->$sql_collection = "UPDATE tbl_collection SET modified_on = NOW() WHERE id IN (:collection_id)";
            $model->bindParam(":collection_id", $collection_id);
            $model->execute();

            return true;
        }
        return true;
    }

    public function getImage() {
        return $this->hasMany(Image::className(), ['id' => 'image_id']);
    }

}
