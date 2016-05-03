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
            [['audio_id', 'tag_id', 'state'], 'integer']
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

    public function search($params) {
        $query = AudioTag::find();
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
                ->andFilterWhere(['like', 'audio_id', $this->audio_id])
                ->andFilterWhere(['like', 'tag_id', $this->tag_id])
                ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }

    public function add($audio_id, $tagIds) {
        $inDB = [];
        foreach (AudioTag::find()->where(['audio_id' => $audio_id])->andFilterWhere(['tag_id' => $tagIds])->exists() as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            $sql->createCommand()
                    ->insert('tbl_audio_tag', [
                        'audio_id' => '',
                        'tag_id' => '',
                        'state' => 1])
                    ->execute();
        }
    }

}
