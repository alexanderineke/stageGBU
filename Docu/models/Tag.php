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

    public function search() {
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
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'slug', $this->slug])
                ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.
}
