<?php
class Search extends CFormModel
{
    /**
     * Declares the validation rules.
     */
    public function rules(){
        return array(
                array('q', 'required'),
        );
    }

    public function searchDocuments($documentModel, $query, $content=false, $content_only=false) {

        $criteria = new CDbCriteria();
        $criteria->with = array(
            'tags' // Tabel tbl_tag toevoegen via de relations.
        );
        $criteria->compare('content', $query, true, "OR");
        $criteria->compare('description', $query, true, "OR");
        $criteria->compare('year', $query, true, "OR");
        $criteria->compare('title', $query, true, "OR");
        $criteria->compare('tags.slug', $query, true, "OR");
        $criteria->compare('title', $documentModel->title, true, "AND");
        $criteria->compare('description', $documentModel->description, true, "AND");
        $criteria->compare('tags.slug', $documentModel->tag_search, true, "AND");
        $criteria->compare('year', $documentModel->year, true, "AND");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->group = 't.id';

        $criteria->together = true;

        return new CActiveDataProvider( $documentModel, array(
                'pagination'=>array('pageSize'=>25),
                'criteria'  => $criteria,
                'sort'=>array(
                    'attributes'=>array(
                        'tag_search'=>array(
                            'asc'=>'tags.slug',
                            'desc'=>'tags.slug DESC',
                        ),
                        '*',
                    ),
                ),
            ) 
        );

    }

    public function searchDocumentsByTag($documentModel, $query) {

        $criteria = new CDbCriteria();

        $criteria->with = array(
            'tags' // Tabel tbl_tag toevoegen via de relations.
        );
            
        $criteria->compare('tags.slug', $query, false, "OR");
        $criteria->compare('title', $documentModel->title, true, "AND");
        $criteria->compare('description', $documentModel->description, true, "AND");
        $criteria->compare('tags.slug', $documentModel->tag_search, true, "AND");
        $criteria->compare('year', $documentModel->year, true, "AND");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->group = 't.id';

        $criteria->together = true;

        return new CActiveDataProvider( $documentModel, array(
                'pagination'=>array('pageSize'=>25),
                'criteria'  => $criteria,
                'sort'=>array(
                    'attributes'=>array(
                        'tag_search'=>array(
                            'asc'=>'tags.slug',
                            'desc'=>'tags.slug DESC',
                        ),
                        '*',
                    ),
                ),
            ) 
        );
    }

    public function searchAudio($audioModel, $query) {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            'tags' // Tabel tbl_tag toevoegen via de relations.
        );

        $criteria->compare('description', $query, true, "OR");
        $criteria->compare('year', $query, true, "OR");
        $criteria->compare('title', $query, true, "OR");
        $criteria->compare('tags.slug', $query, true, "OR");
        $criteria->compare('title', $audioModel->title, true, "AND");
        $criteria->compare('description', $audioModel->description, true, "AND");
        $criteria->compare('tags.slug', $audioModel->tag_search, true, "AND");
        $criteria->compare('year', $audioModel->year, true, "AND");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->group = 't.id';

        $criteria->together = true;

        return new CActiveDataProvider( $audioModel, array(
            'pagination'=>array('pageSize'=>25),
            'criteria'  => $criteria,
            'sort'=>array(
                'attributes'=>array(
                    'tag_search'=>array(
                        'asc'=>'tags.slug',
                        'desc'=>'tags.slug DESC',
                    ),
                    '*',
                ),
            ),
        ) );
    }

    public function searchAudioByTag($audioModel, $query) {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            'tags' // Tabel tbl_tag toevoegen via de relations.
        );

        $criteria->compare('tags.slug', $query, false, "OR");
        $criteria->compare('title', $audioModel->title, true, "AND");
        $criteria->compare('description', $audioModel->description, true, "AND");
        $criteria->compare('tags.slug', $audioModel->tag_search, true, "AND");
        $criteria->compare('year', $audioModel->year, true, "AND");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->group = 't.id';

        $criteria->together = true;

        return new CActiveDataProvider( $audioModel, array(
            'pagination'=>array('pageSize'=>25),
            'criteria'  => $criteria,
            'sort'=>array(
                'attributes'=>array(
                    'tag_search'=>array(
                        'asc'=>'tags.slug',
                        'desc'=>'tags.slug DESC',
                    ),
                    '*',
                ),
            ),
        ) );
    }    

    public function searchImages($query) {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            'tags', // Tabel tbl_tag toevoegen via de relations.
            'images'
        );
        $criteria->compare('title', $query, true, "OR");
        $criteria->compare('year', $query, true, "OR");
        $criteria->compare('description', $query, true, "OR");
        $criteria->compare('tags.slug', $query, true, "OR");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->together = true;
        $criteria->group = 't.id';

        return new CActiveDataProvider( 'Image', array(
            'criteria'  => $criteria,
            'pagination'=>array('pageSize'=>30),


        ) );
    }

    public function searchImagesByTag($query) {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            'tags', // Tabel tbl_tag toevoegen via de relations.
            'images'
        );
        $criteria->compare('tags.slug', $query, false, "OR");
        $criteria->compare('tags.state', 1 , false, "AND");
        $criteria->together = true;
        $criteria->group = 't.id';

        return new CActiveDataProvider( 'Image', array(
                'criteria'  => $criteria,
                'pagination'=>array('pageSize'=>30),
            ) 
        );
    }    

    public function popularTags() {
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

        $tag_ids = array();
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
}