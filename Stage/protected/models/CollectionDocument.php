<?php

/**
 * This is the model class for table "{{collection_document}}".
 *
 * The followings are the available columns in table '{{collection_document}}':
 * @property integer $id
 * @property integer $collection_id
 * @property integer $document_id
 * @property integer $state
 */
class CollectionDocument extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{collection_document}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('collection_id, document_id, state', 'required'),
			array('collection_id, document_id, state', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, collection_id, document_id, state', 'safe', 'on'=>'search'),
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
            'collection' => array(self::BELONGS_TO, 'Collection', 'collection_id'),
            'document' => array( self::BELONGS_TO, 'Document', 'document_id' ),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'collection_id' => 'Collection',
			'document_id' => 'Document',
			'state' => 'State',
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
		$criteria->compare('collection_id',$this->collection_id);
		$criteria->compare('document_id',$this->document_id);
		$criteria->compare('state',$this->state);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function add($document_id, $collection_id){
        $sql_document 			= "INSERT INTO tbl_collection_document (document_id, collection_id, state) VALUES (:document_id, :collection_id, 1)";
        $parameters_document 	= array(":document_id"=>$document_id, ":collection_id"=>$collection_id);

		$sql_collection			= "UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id";
    	$parameters_collection	= array(":collection_id"=>$collection_id);

        if(Yii::app()->db->createCommand($sql_document)->execute($parameters_document) && Yii::app()->db->createCommand($sql_collection)->execute($parameters_collection))
           return true;
    }

    public function deleteDocument($document_id, $collection_id){
	    if(!empty($collection_id)){
		    $sql_document 			= "DELETE FROM tbl_collection_document WHERE document_id = :document_id AND collection_id = :collection_id";
	        $parameters_document 	= array(":document_id"=>$document_id, ":collection_id"=>$collection_id);

			$sql_collection			= "UPDATE tbl_collection SET modified_on = NOW() WHERE id = :collection_id";
	        $parameters_collection	= array(":collection_id"=>$collection_id); 

	        if(Yii::app()->db->createCommand($sql_document)->execute($parameters_document) && Yii::app()->db->createCommand($sql_collection)->execute($parameters_collection))
	           return true;
	    }else{

	    	//Kijk of document in collecties voor komt
	        $criteria=new CDbCriteria;
	        $criteria->select = array('collection_id');
	        $criteria->condition = 'document_id='.$document_id;

	        $results = $this->findAll($criteria);

	        if($results){
	        	//Verwijder document uit collecties
	   			$sql_collection_doc 	= "DELETE FROM tbl_collection_document WHERE document_id = :document_id";
	       		$parameters_document 	= array(":document_id"=>$document_id);

        		Yii::app()->db->createCommand($sql_collection_doc)->execute($parameters_document);

        		//Vertel de collecties dat ze gewijzigd zijn
		        $ids = array();
				foreach ($results as $result) {
					$ids[] = $result->collection_id;
				}
				$ids = implode(',', array_unique($ids));

				$sql_collection			= "UPDATE tbl_collection SET modified_on = NOW() WHERE id IN (:collection_id)";
		        $parameters_collection	= array(":collection_id"=>$ids); 

		        Yii::app()->db->createCommand($sql_collection)->execute($parameters_collection);
		        return true;	    
		    }else{
		    	return true;
		    }
	    }
    } 

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CollectionDocument the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
