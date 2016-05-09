<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%document_tag}}".
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $tag_id
 * @property integer $state
 */
class DocumentTag extends \yii\db\ActiveRecord {

    public static function model($className = __CLASS__) {
        
    }

    public static function tableName() {
        return '{{%document_tag}}';
    }

    public function rules() {
        return [
            [['document_id', 'tag_id'], 'required'],
            [['document_id', 'tag_id', 'state'], 'integer'],
            [['id, document_id, tag_id, state'], 'safe', 'on' => 'search']
        ];
    }

    public function getTag() {
        return $this->Belongs_to(Tag::className(), ['id' => 'tag_id']);
    }

    public function getDocument() {
        return $this->Belongs_to(Document::className(), ['id' => 'document_id']);
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'document_id' => 'Document',
            'tag_id' => 'Tag',
            'state' => 'State',
        ];
    }

    public function search() {
        $query = DocumentTag::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'document_id', $this->document_id])
                ->andFilterWhere(['like', 'tag_id', $this->tag_id])
                ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }

    public function add($document_id, $tagIds) {
        $inDB = [];
        foreach ($this->check($document_id, $tagIds) as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            $connection->createCommand()->insert('tbl_document_tag', [
                'document_id' => $document_id,
                'tag_id' => $i,
            ])->execute();
            if ($connection->queryAll()) {
                $v++;
            }
        }
        if ($v == sizeof($notInDB)) {
            return true;
        }
    }

    public function check($document_id, $tagIds) {
        $query = DocumentTag::find()
                ->select(['tag_id'])
                ->andFilterWhere(['document_id' => $document_id])
                ->andFilterWhere(['tag_id' => $tagIds])
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

    public function deleteTags($document_id, $tagIds) {
        $query = DocumentTag::find()
                ->andFilterWhere(['document_id' => $document_id])
                ->andFilterWhere(['tag_id' => $tagIds])
                ->andFilterWhere(['state' => 1])
                ->all()
                ->delete();
        return $query;
    }

    public function getTagg($document_id) {
        $query = DocumentTag::find()
                ->select(['tag_id'])
                ->andFilterWhere(['document_id' => $document_id])
                ->andFilterWhere(['state' => 1])
                ->limit(1)
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

}
