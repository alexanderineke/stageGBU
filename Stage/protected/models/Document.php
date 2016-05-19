<?php
/**
 * This is the model class for table "{{Document}}".
 *
 * The followings are the available columns in table '{{Document}}':
 * @property string $id
 * @property integer $user_id
 * @property string $filename
 * @property string $location
 * @property string $format
 * @property string $title
 * @property string $description
 * @property string $tags
 * @property integer $year
 * @property string $owner
 */
class Document extends CActiveRecord
{
    public $tag_search, $count, $tagName;


    public function getTagsHelper()
    {
        return implode(', ', array_values(CHtml::listData($this->tags, 'id', 'name')));
    }

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Document the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{document}}';
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, title, published', 'required'),
            array('user_id, year, published', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>64),
            array('owner', 'length', 'max'=>45),
            array('description', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('tag_search, id, user_id, title, description, year, owner, published', 'safe', 'on'=>'search'),
        );
    }


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'tags' => array(self::MANY_MANY, 'Tag', 'tbl_document_tag(document_id, tag_id)'),
			'document_tags' => array( self::HAS_MANY, 'DocumentTag', 'document_id' ),
			'documents' => array(self::HAS_MANY, 'DocumentFile', 'document_id', 'condition'=>'state=1'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'Naam van uploader',
			'title' => 'Titel',
			'description' => 'Omschrijving',
			'tags' => 'Steekwoorden',
			'year' => 'Jaar',
			'owner' => 'Eigenaar',
            'created_on' => 'Aanmaakdatum',
            'modified_on' => 'Laatste wijzigingsdatum',     			
			'published' => 'Gepubliceerd',
			'file' => 'Bestand',
			'collection' => 'Collectie',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('year',$this->year);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('published',$this->published,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function searchDocuments($model, $query) {
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'tags' // Tabel tbl_tag toevoegen via de relations.
	    );

	    $criteria->compare('description', $query, true, "OR");
	    $criteria->compare('year', $query, true, "OR");
	    $criteria->compare('title', $query, true, "OR");
	    $criteria->compare('tags.slug', $query, true, "OR");
	    $criteria->compare('title', $model->title, true, "AND");
	    $criteria->compare('description', $model->description, true, "AND");
	    $criteria->compare('tags.slug', $model->tag_search, true, "AND");
	    $criteria->compare('year', $model->year, true, "AND");
	    $criteria->compare('tags.state', 1 , false, "AND");
	    $criteria->group = 't.id';

	    $criteria->together = true;

	    return new CActiveDataProvider( $model, array(
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
}
