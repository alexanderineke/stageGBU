<?php

/**
 * This is the model class for table "{{document_temp}}".
 *
 * The followings are the available columns in table '{{document_temp}}':
 * @property string $id
 * @property string $create_date
 * @property integer $user_id
 * @property string $file
 * @property string $format
 * @property string $location
 */
class DocumentTemp extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{document_temp}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_date, user_id, file, format, location', 'required'),
            array('user_id', 'numerical', 'integerOnly'=>true),
            array('file, location', 'length', 'max'=>255),
            array('format', 'length', 'max'=>4),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, create_date, user_id, file, format, location', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'create_date' => 'Create Date',
            'user_id' => 'User',
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('file',$this->file,true);
        $criteria->compare('format',$this->format,true);
        $criteria->compare('location',$this->location,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function addTempFile($filename, $location){
        $sql = "insert into tbl_document_temp (user_id, create_date, file, format, location) values (:user_id, NOW(), :file, :format, :location)";
        $parameters = array(":user_id"=>Yii::app()->user->getId(),
                            ":file"=>$filename,
                            ":format"=>'pdf',
                            ":location"=>$location);
        if(Yii::app()->db->createCommand($sql)->execute($parameters))
            $id = Yii::app()->db->getLastInsertID();
        else
            $id = false;

        return $id;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DocumentTemp the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}