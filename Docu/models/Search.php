<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search represents the model behind the search form about `app\models\Audio`.
 */
class Search {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['q'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /*

      public function searchDocuments($documentModel, $query, $content = false, $content_only = false) {
      $q = Document::find()
      ->with('tags');

      $dataProvider = new ActiveDataProvider([
      'query' => $q,
      'pagination' => ['pagaSize' => 25],
      'sort' => [
      'attributes' => [
      'tag_search' => [
      'asc' => ['tags.slug' => SORT_ASC],
      'desc' => ['tags.slug' => SORT_DESC],
      ],
      '*',
      ],
      ],
      ]);

      $q
      ->where(['like', 'content', $query])
      ->orWhere(['like', 'description', $query])
      ->orWhere(['like', 'year', $query])
      ->orWhere(['like', 'title', $query])
      ->orWhere(['like', 'tags.slug', $query])
      ->andFilterWhere(['like', 'title', $documentModel->title])
      ->andFilterWhere(['like', 'description', $documentModel->description])
      ->andFilterWhere(['like', 'tags.slug', $documentModel->tag_search])
      ->andFilterWhere(['like', 'year', $documentModel->year])
      ->andFilterWhere(['like', 'tags.state', 1])
      ->groupBy(['t.id']);
      return $dataProvider;
      }
     */

    public static function searchDocuments($params) {
        $query = Document::find()
                ->joinWith('tags')
                ->andWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andWhere([
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

    /*
      public function searchDocuments($params) {
      $query = Document::find()
      ->joinWith(['tags' => function($q) {
      $q->where('tags.slug LIKE "%' . $this->tag . '%"');
      }]);
      return $dataProvider;
      }
     */

    public function searchDocumentsByTag($params) {
        $query = DocumentTag::find()
                ->joinWith('tags')
                ->andWhere('or', ['like', 'tags.slug', $params['tags.slug'] . '%', true])
                ->andWhere([
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
                ->andWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andWhere([
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
                ->andWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andWhere([
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
                ->andWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andWhere([
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

    public function searchImagesByTag($params) {
        $query = ImageTag::find()
                ->joinWith('tags')
                ->joinWith('images')
                ->andWhere([
                    'or',
                    ['like', 'content', $params['content'] . '%', true],
                    ['like', 'description', $params['description'] . '%', true],
                    ['like', 'year', $params['year'] . '%', true],
                    ['like', 'title', $params['title'] . '%', true],
                    ['like', 'tags.slug', $params['tags.slug'] . '%', true],
                ])
                ->andWhere([
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
        if($result = \Yii::$app->db->createCommand($most_popular_tags_sql)->query()){
            foreach($result as $row){
                $tag_ids[] = $row['tag_ids'];
            }
            $tagModel = new Tag();
            $tags = $tagModel->findTagsByID($tag_ids, true);
        }
        if($tags){
            return $tags;
        }
    }

}
