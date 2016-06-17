<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audio_tag}}".
 *
 * @property integer $id
 * @property integer $audio_id
 * @property integer $tag_id
 * @property integer $state
 */
class AudioTag extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%audio_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['audio_id', 'tag_id'], 'required'],
            [['audio_id', 'tag_id', 'state'], 'integer'],
            [['id, audio_id, tag_id, state'], 'safe', 'on' => 'search']
        ];
    }

    public function getTag() {
        return $this->Belongs_to(Tag::className(), ['id' => 'tag_id']);
    }

    public function getAudio() {
        return $this->Belongs_to(Audio::className(), ['id' => 'audio_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'audio_id' => 'Audio',
            'tag_id' => 'Tag',
            'state' => 'State',
        ];
    }

    public function search() {
        $query = AudioTag::find();
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
                    ['like', 'audio_id', $this->audio_id],
                    ['like', 'tag_id', $this->tag_id],
                    ['like', 'state', $this->state]]);

        return $dataProvider;
    }

    public function add($audio_id, $tagIds) {
        $inDB = [];
        foreach ($this->check($audio_id, $tagIds) as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            Yii::$app->db->createCommand()
                    ->insert('tbl_audio_tag', [
                        'audio_id' => $audio_id,
                        'tag_id' => $i,
                        'state' => 1])
                    ->execute();
            $v++;
            if ($v == sizeof($notInDB)) {
                return true;
            }
        }
    }

    public function check($audio_id, $tagIds) {
        foreach ($tagIds as $i => $tag) {
            $query = AudioTag::find()
                    ->select(['tag_id'])
                    ->where(['audio_id' => $audio_id])
                    ->andWhere(['tag_id' => $i])
                    ->andWhere(['state' => 1])
                    ->all();
        }
        return $query;
    }

    public function getTagg($audio_id) {
        $query = AudioTag::find()
                ->select(['tag_id'])
                ->andFilterWhere(['audio_id' => $audio_id])
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
