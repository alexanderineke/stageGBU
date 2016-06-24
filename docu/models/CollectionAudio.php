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
class CollectionAudio extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%collection_audio}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['collection_id', 'audio_id', 'state'], 'required'],
            [['collection_id', 'audio_id', 'state'], 'integer'],
            [['id', 'collection_id', 'audio_id', 'state'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'collection_id' => 'Collectie',
            'audio_id' => 'Audio',
            'state' => 'State',
        ];
    }

    public function search() {
        $query = CollectionAudio::find();
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
                    ['like', 'audio_id', $this->audio_id],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    //voegt een audio bestand aan een collectie toe
    public static function add($audio_id, $collection_id) {
        Yii::$app->db->createCommand()
                ->insert('tbl_collection_audio', [
                    'audio_id' => $audio_id,
                    'collection_id' => $collection_id,
                    'state' => 1])
                ->execute();


        Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                ->bindValue(':date', date("Y-m-d H:i:s"))
                ->bindValue(':collection_id', $collection_id)
                ->execute();
        return true;
    }

    //verwijderd een audio bestand uit de collectie
    public function deleteAudio($audio_id, $collection_id) {

        if (!empty($collection_id)) {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_audio WHERE audio_id = :audio_id AND collection_id = :collection_id")
                    ->bindValue(":audio_id", $audio_id)
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();
            return true;
        } else {
            Yii::$app->db->createCommand("DELETE FROM tbl_collection_audio WHERE audio_id = :audio_id")
                    ->bindValue(":audio_id", $audio_id)
                    ->execute();

            Yii::$app->db->createCommand("UPDATE tbl_collection SET modified_on = :date WHERE id = :collection_id")
                    ->bindValue(':date', date("Y-m-d H:i:s"))
                    ->bindValue(":collection_id", $collection_id)
                    ->execute();

            return true;
        }
        return true;
    }

    public function getAudio() {
        return $this->hasMany(Audio::className(), ['id' => 'audio_id']);
    }

}
