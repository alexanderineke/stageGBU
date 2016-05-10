<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search represents the model behind the search form about `app\models\Audio`.
 */
class Search extends Model {

    public function rules() {
        return [
            [['id', 'user_id', 'year', 'published'], 'integer'],
            [['title', 'description', 'owner', 'created_on', 'modified_on'], 'safe'],
        ];
    }

    public function searchDocuments($documentModel, $query, $content = false, $content_only = false) {
        $q = Document::find()
                ->joinWith('tbl_tag')
                ->with('tags');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['attributes' => ['tag_search' => [
                        'asc' => ['tags.slug' => SORT_ASC],
                        'desc' => ['tags.slug' => SORT_DESC],
                    ]],
                '*'],
        ]);

        $q->orWhere(['like', 'content', $query])
                ->orWhere(['like', 'description', $query])
                ->orWhere(['like', 'year', $query])
                ->orWhere(['like', 'title', $query])
                ->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'title', $documentModel->title])
                ->andFilterWhere(['like', 'description', $documentModel->description])
                ->andFilterWhere(['like', 'tags.slug', $documentModel->tags_search])
                ->andFilterWhere(['like', 'year', $documentModel->year])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }

    public function searchDocumentsByTag($documentModel, $query) {
        $q = DocumentTag::find()
                ->joinWith('tbl_tag')
                ->with('tags');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['attributes' => ['tag_search' => [
                        'asc' => ['tags.slug' => SORT_ASC],
                        'desc' => ['tags.slug' => SORT_DESC],
                    ]],
                '*'],
        ]);

        $q->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'title', $documentModel->title])
                ->andFilterWhere(['like', 'description', $documentModel->description])
                ->andFilterWhere(['like', 'tags.slug', $documentModel->tags_search])
                ->andFilterWhere(['like', 'year', $documentModel->year])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }

    public function searchAudio($audioModel, $query) {
        $q = Audio::find()
                ->joinWith('tbl_tag')
                ->with('tags');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['attributes' => ['tag_search' => [
                        'asc' => ['tags.slug' => SORT_ASC],
                        'desc' => ['tags.slug' => SORT_DESC],
                    ]],
                '*'],
        ]);

        $q->orWhere(['like', 'description', $query])
                ->orWhere(['like', 'year', $query])
                ->orWhere(['like', 'title', $query])
                ->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'title', $audioModel->title])
                ->andFilterWhere(['like', 'description', $audioModel->description])
                ->andFilterWhere(['like', 'tags.slug', $audioModel->tags_search])
                ->andFilterWhere(['like', 'year', $audioModel->year])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }

    public function searchAudioByTag($audioModel, $query) {
        $q = AudioTag::find()
                ->joinWith('tbl_tag')
                ->with('tags');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['attributes' => ['tag_search' => [
                        'asc' => ['tags.slug' => SORT_ASC],
                        'desc' => ['tags.slug' => SORT_DESC],
                    ]],
                '*'],
        ]);

        $q->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'title', $audioModel->title])
                ->andFilterWhere(['like', 'description', $audioModel->description])
                ->andFilterWhere(['like', 'tags.slug', $audioModel->tags_search])
                ->andFilterWhere(['like', 'year', $audioModel->year])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }

    public function searchImages($query) {
        $q = Image::find()
                ->joinWith('tbl_tag')
                ->with('tags')
                ->joinWith('tbl_image_file')
                ->with('images');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $q->orWhere(['like', 'description', $query])
                ->orWhere(['like', 'year', $query])
                ->orWhere(['like', 'title', $query])
                ->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }

    public function searchImagesByTag($query){
                $q = ImageTag::find()
                ->joinWith('tbl_tag')
                ->with('tags')
                ->joinWith('tbl_image_file')
                ->with('images');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $q->orWhere(['like', 'title', $query])
                ->orWhere(['like', 'tags.slug', $query])
                ->andFilterWhere(['like', 'tags.state', 1])
                ->groupBy(['t.id']);
    }
    
    public function popularTags(){
        $most_popular_tags_sql =
        'SELECT tag_id, magnitude
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
        
        $tags_ids = [];
        if($result = Yii::app()->db->createCommand($most_popular_tags_sql)->query()){
            foreach($result as $row){
                $tag_ids[] = $row['tag_id'];
            }     
            $tagModel = new Tag();
            $tags = $tagModel->findTagsByID($tag_ids, true);   
        }

        if($tags) 
            return $tags;
    }
    
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

}
