<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use app\models\Image;
use app\models\Document;
use app\models\Audio;
use app\models\Tag;

class Search {

    public function rules() {
        return [
            [['q'], 'required'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public static function searchDocuments($params, $keyword) {
        $query = Document::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'content', $keyword],
            ['like', 'description', $keyword],
            ['like', 'year', $keyword],
            ['like', 'title', $keyword],
            ['like', 'tags.slug', $keyword],
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);

        return $provider;
    }

    public function searchDocumentsByTag($documentsModel, $keyword) {
        $query = Document::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'tags.state', 1],
            ['like', 'tags.slug', $keyword],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
        return $dataProvider;
    }

    public function searchAudio($audioModel, $keyword) {
        $query = Audio::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'description', $keyword],
            ['like', 'year', $keyword],
            ['like', 'title', $keyword],
            ['like', 'tags.slug', $keyword],
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);
        return $provider;
    }

    public function searchAudioByTag($audioModel, $keyword) {
        $query = Audio::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'tags.state', 1],
            ['like', 'tags.slug', $keyword],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
        return $dataProvider;
    }

    public function searchImages($keyword) {
        $query = Image::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'description', $keyword],
            ['like', 'year', $keyword],
            ['like', 'title', $keyword],
            ['like', 'tags.slug', $keyword],
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);

        return $provider;
    }

    public function searchImagesByTag($keyword) {
        $query = Image::find()
                ->joinWith('tags tags')
                ->andFilterWhere([
            'or',
            ['like', 'tags.state', 1],
            ['like', 'tags.slug', $keyword],
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $provider;
    }

    public function popularTags() {
        $most_popular_tags_sql = 'SELECT tag_id, magnitude
            FROM (
                SELECT * FROM 
                (
                    SELECT tag_id, COUNT(*) AS magnitude 
                    FROM tbl_document_tag
                    GROUP BY tag_id 
                    ORDER BY magnitude DESC
                    LIMIT 10
                ) popular_document_tags

                UNION ALL

                SELECT * FROM
                ( 
                    SELECT tag_id, COUNT(*) AS magnitude 
                    FROM tbl_image_tag
                    GROUP BY tag_id 
                    ORDER BY magnitude DESC
                    LIMIT 10
                ) popular_image_tags

                UNION ALL

                SELECT * FROM
                ( 
                    SELECT tag_id, COUNT(*) AS magnitude 
                    FROM tbl_audio_tag
                    GROUP BY tag_id 
                    ORDER BY magnitude DESC
                    LIMIT 10
                ) popular_audio_tags
            ) top_results
        GROUP BY tag_id
        ORDER BY magnitude DESC
        LIMIT 10';

        $tag_ids = [];
        if ($result = \Yii::$app->db->createCommand($most_popular_tags_sql)->query()) {
            foreach ($result as $row) {
                $tag_ids[] = $row['tag_id'];
            }
            $tagModel = new Tag();
            $tags = $tagModel->findTagsByID($tag_ids, true);
        }
        if ($tags) {
            return $tags;
        }
    }

}
