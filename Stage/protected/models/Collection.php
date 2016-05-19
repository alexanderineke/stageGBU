
<?php

/**
 * This is the model class for table "{{collection}}".
 *
 * The followings are the available columns in table '{{collection}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $created_on
 * @property string $modified_on  
 * @property integer $published
 */
class Collection extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{collection}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, description, published', 'required'),
            array('user_id, published', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, title, description, created_on, modified_on, published', 'safe', 'on'=>'search'),
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
            'collection_images' => array( self::HAS_MANY, 'CollectionImage', 'collection_id'),
            'images' => array(self::HAS_MANY,'Image',array('image_id'=>'id'),'through'=>'collection_images'),
            'thumb' => array(self::HAS_ONE,'ImageFile',array('image_id'=>'image_id'),'through'=>'collection_images'),
            'collection_documents' => array( self::HAS_MANY, 'CollectionDocument', 'collection_id'),
            'documents' => array(self::HAS_MANY,'Document',array('document_id'=>'id'),'through'=>'collection_documents'),
            'collection_collections' => array( self::HAS_MANY, 'CollectionCollection', 'collection_id'),
            'collections' => array(self::HAS_MANY,'Collection',array('collection_col_id'=>'id'),'through'=>'collection_collections'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'Gebruiker',
            'title' => 'Titel',
            'description' => 'Omschrijving',
            'created_on' => 'Aanmaakdatum',
            'modified_on' => 'Laatste wijzigingsdatum',            
            'published' => 'Gepubliceerd',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('created_on',$this->created_on,true);
        $criteria->compare('modified_on',$this->modified_on,true);        
        $criteria->compare('published',$this->published);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function checkOwnership()
    {
        $criteria = new CDbCriteria;

        $criteria->condition='id=:id AND user_id=:user_id';
        $criteria->params=array(':id'=>$this->id, ':user_id'=>Yii::app()->user->getId());
        if($this->find($criteria))
            return true;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Collection the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}