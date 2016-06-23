<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collection_document}}".
 *
 * @property integer $id
 * @property integer $collection_id
 * @property integer $document_id
 * @property integer $state
 */
class CollectionDocument extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%collection_document}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['collection_id', 'document_id', 'state'], 'required'],
            [['collection_id', 'document_id', 'state'], 'integer'],
            [['id', 'collection_id', 'document_id', 'state'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'collection_id' => 'Collection',
            'document_id' => 'Document',
            'state' => 'State',
        ];
    }

    public function search() {
        $query = CollectionDocument::find();
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
                    ['like', 'collection_id', $this->collection_id],
                    ['like', 'document_id', $this->document_id],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    public static function add($document_id, $collection_id) {
        Yii::$app->db->createCommand()
                ->insert('tbl_collection_document', [
                    'document_id' => $document_id,
                    'collection_id' => $collection_id,
                    'state' => 1])
                ->execute();


        Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                ->bindValue(':date', date("Y-m-d H:i:s"))
                ->bindValue(':collection_id', $collection_id)
                ->execute();
        return true;
    }

    public function deleteDocument($document_id, $collection_id) {

        if (!empty($collection_id)) {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_document WHERE document_id = :document_id AND collection_id = :collection_id")
                    ->bindValue(":document_id", $document_id)
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            return true;
        } else {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_document WHERE document_id = :document_id")
                    ->bindValue(":document_id", $document_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            return true;
        }
        return true;
    }

    public function getCollection() {
        return $this->hasMany(Collection::className(), ['id' => 'collection_id']);
    }

    public function getDocument() {
        return $this->hasMany(Document::className(), ['id' => 'document_id']);
    }

}
