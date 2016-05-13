<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audio_temp}}".
 *
 * @property string $id
 * @property string $create_date
 * @property integer $user_id
 * @property string $file
 * @property string $format
 * @property string $location
 */
class AudioTemp extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%audio_temp}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['create_date', 'user_id', 'file', 'format', 'location'], 'required'],
            [['id, create_date, user_id, file, format, location'], 'safe'],
            [['user_id'], 'integer'],
            [['file', 'location'], 'string', 'max' => 255],
            [['format'], 'string', 'max' => 4]
        ];
    }

    public function getUser() {
        return $this->Belongs_to(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'create_date' => 'Create Date',
            'user_id' => 'User ID',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
        ];
    }

    public function search() {
        $query = AudioTemp::find();
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
                    ['like', 'create_date', $this->create_id],
                    ['like', 'user_id', $this->user_id],
                    ['like', 'file', $this->file],
                    ['like', 'format', $this->format],
                    ['like', 'location', $this->location]]);

        return $dataProvider;
    }

    public function addTempFile($filename, $location) {
        $sql->createCommand()
                ->insert('tbl_audio_temp', [
                    'user_id' => Yii::$app->user->getId(),
                    'create_date' => 'NOW()',
                    'file' => $filename,
                    'format' => 'pdf',
                    'location' => $location])
                ->execute();

        return $sql;
    }

}
