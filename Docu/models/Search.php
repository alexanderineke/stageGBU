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

    public function searchDocumentsByTag($documentModel, $query) {
        $q = DocumentTag::find()
                ->with('tags');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
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

        $q
        ->where(['and', ['tags.slug = :tags.slug', [':tags.slug' => $query]]]);
    }

    public function searchAudio($audioModel, $query) {
        
    }

    public function searchAudioByTag($audioModel, $query) {
        
    }

    public function searchImages($query) {
        
    }

    public function searchImagesByTag($query) {
        
    }

    public function popularTags() {
        
    }

}
