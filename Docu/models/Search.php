<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


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

    public static function searchDocuments($params) {
        $query = Document::find()
                ->joinWith('tags')
                ->andFilterWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andFilterWhere([
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'tags.state', 1 . '%', false],
                ])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 25]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
    }

    public function searchDocumentsByTag($params) {
        $query = DocumentTag::find()
                ->joinWith('tags')
                ->andFilterWhere('or', ['like', 'tags.slug', $params['tags.slug'] . '%', true])
                ->andFilterWhere([
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'tags.state', 1 . '%', false],
                ])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 25]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
    }

    public function searchAudio($params) {
        $query = Audio::find()
                ->joinWith('tags')
                ->andFilterWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andFilterWhere([
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'tags.state', 1 . '%', false],
                ])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 25]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
    }

    public function searchAudioByTag($params) {
        $query = AudioTag::find()
                ->joinWith('tags')
                ->andFilterWhere(['or', ['like', 'tags.slug', $params['tags.slug'] . '%', true],])
                ->andFilterWhere([
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'tags.state', 1 . '%', false],
                ])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 25]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
    }

    public function searchImages($params) {
        $query = Image::find()
                ->joinWith('tags')
                ->joinWith('images')
                ->andFilterWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andFilterWhere(['like', 'tags.state', 1 . '%', false])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 30]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
    }

    public function searchImagesByTag($params) {
        $query = ImageTag::find()
                ->joinWith('tags')
                ->joinWith('images')
                ->andFilterWhere('or', ['like', 'tags.slug', $params['tags.slug'] . '%', true])
                ->andFilterWhere(['like', 'tags.state', 1 . '%', false])
                ->groupBy('id');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 30]);
        $datas = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [
            'datas' => $datas,
            'pages' => $pages
        ];
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
