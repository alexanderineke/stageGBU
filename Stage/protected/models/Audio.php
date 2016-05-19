<?php
/**
 * This is the model class for table "{{Audio}}".
 *
 * The followings are the available columns in table '{{Audio}}':
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
class Audio extends CActiveRecord
{
    public $tag_search, $count, $tagName;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Audio the static model class
	 */

    public function getTagsHelper()
    {
        return implode(', ', array_values(CHtml::listData($this->tags, 'id', 'name')));
    }

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{audio}}';
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
			'tags' => array(self::MANY_MANY, 'Tag', 'tbl_audio_tag(audio_id, tag_id)'),
			'audio_tags' => array( self::HAS_MANY, 'AudioTag', 'audio_id' ),
			'audios' => array(self::HAS_MANY, 'AudioFile', 'audio_id', 'condition'=>'state=1'),
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
}