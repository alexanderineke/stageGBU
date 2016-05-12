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
      public function searchDocuments($params) {
      $query = Document::find()
      ->joinwith('tags');

      $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => ['pageSize' => 25],
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

      $this->load($params);

      if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
      }

      $query
      ->andFilterWhere('or', [['like', 'content', $this->content],
      ['like', 'description', $this->description],
      ['like', 'year', $this->year],
      ['like', 'title', $this->title],
      ['like', 'tags.slug', $this->tags.slug]])
      ->andFilterWhere([['like', 'title', $this->title],
      ['like', 'description', $this->description],
      ['like', 'tags.slug', $this->tag_search],
      ['like', 'year', $this->year],
      ['like', 'tags.state', 1]])
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
      public function searchDocumentsByTag($params) {
      $query = DocumentTag::find()
      ->joinwith('tags');

      $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => ['pageSize' => 25],
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

      $this->load($params);

      if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
      }

      $query
      ->andFilterWhere('or', [['like', 'tags.slug', $this->tags . slug]])
      ->andFilterWhere([['like', 'title', $this->title],
      ['like', 'description', $this->description],
      ['like', 'tags.slug', $this->tag_search],
      ['like', 'year', $this->year],
      ['like', 'tags.state', 1]])
      ->groupBy(['t.id']);
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

    /*
      public function searchAudio($params){
      $query = Audio::find()
      ->joinwith('tags');

      $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => ['pageSize' => 25],
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

      $this->load($params);

      if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
      }

      $query
      ->andFilterWhere('or', [['like', 'content', $this->content],
      ['like', 'description', $this->description],
      ['like', 'year', $this->year],
      ['like', 'title', $this->title],
      ['like', 'tags.slug', $this->tags.slug]])
      ->andFilterWhere([['like', 'title', $this->title],
      ['like', 'description', $this->description],
      ['like', 'tags.slug', $this->tag_search],
      ['like', 'year', $this->year],
      ['like', 'tags.state', 1]])
      ->groupBy(['t.id']);
      return $dataProvider;
      }
     */

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

    /*
      public function searchAudioByTag($params){
      $query = AudioTag::find()
      ->joinwith('tags');

      $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => ['pageSize' => 25],
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

      $this->load($params);

      if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
      }

      $query
      ->andFilterWhere('or', [['like', 'tags.slug', $this->tags.slug]])
      ->andFilterWhere([['like', 'title', $this->title],
      ['like', 'description', $this->description],
      ['like', 'tags.slug', $this->tag_search],
      ['like', 'year', $this->year],
      ['like', 'tags.state', 1]])
      ->groupBy(['t.id']);
      return $dataProvider;
      }

     */

    public function searchAudioByTag($params) {
        $query = AudioTag::find()
                ->joinWith('tags')
                ->andWhere(['or', ['like', 'tags.slug', $params['tags.slug'] . '%', true],])
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
      public function searchImages($params) {
      $query = Image::find()
      ->joinwith('tags')
      ->joinWith('images');

      $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => ['pageSize' => 30],
      ]);

      $this->load($params);

      if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
      }

      $query
      ->andFilterWhere('or', [['like', 'content', $this->content],
      ['like', 'description', $this->description],
      ['like', 'year', $this->year],
      ['like', 'title', $this->title],
      ['like', 'tags.slug', $this->tags . slug]])
      ->andFilterWhere(['like', 'tags.state', 1])
      ->groupBy(['t.id']);
      return $dataProvider;
      }
     */

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
                ->andWhere(['like', 'tags.state', 1 . '%', false])
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

    
    
    /*
    public function searchImagesByTag($params) {
        $query = Document::find()
                ->joinwith('tags')
                ->joinWith('images');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30]]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query
                ->andFilterWhere('or', ['like', 'tags.slug', $this->tags . slug])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
        return $dataProvider;
    }
*/


    public function searchImagesByTag($params) {
        $query = ImageTag::find()
                ->joinWith('tags')
                ->joinWith('images')
                ->andWhere('or', ['like', 'tags.slug', $params['tags.slug'] . '%', true])
                ->andWhere(['like', 'tags.state', 1 . '%', false])
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
                $tag_ids[] = $row['tag_ids'];
            }
            $tagModel = new Tag();
            $tags = $tagModel->findTagsByID($tag_ids, true);
        }
        if ($tags) {
            return $tags;
        }
    }

}
