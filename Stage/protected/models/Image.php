<?php

/**
 * This is the model class for table "{{image}}".
 *
 * The followings are the available columns in table '{{image}}':
 * @property string $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property integer $year
 * @property string $owner
 * @property integer $published
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Image extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Image the static model class
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
        return '{{image}}';
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
            array('id, user_id, title, description, year, owner, published', 'safe', 'on'=>'search'),
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
			'tags' => array(self::MANY_MANY, 'Tag', 'tbl_image_tag(image_id, tag_id)'),
			'images' => array(self::HAS_MANY, 'ImageFile', 'image_id', 'condition'=>'images.state=1'),
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
        $criteria->compare('published',$this->published);
        $criteria->with=array('images');

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
