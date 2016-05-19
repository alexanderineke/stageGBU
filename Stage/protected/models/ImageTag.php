<?php

/**
 * This is the model class for table "{{image_tag}}".
 *
 * The followings are the available columns in table '{{image_tag}}':
 * @property integer $id
 * @property integer $image_id
 * @property integer $tag_id
 * @property integer $state
 */
class ImageTag extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ImageTag the static model class
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
        return '{{image_tag}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('image_id, tag_id', 'required'),
            array('image_id, tag_id, state', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, image_id, tag_id, state', 'safe', 'on'=>'search'),
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
            'tag' => array(self::BELONGS_TO, 'Tag', 'tag_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'image_id' => 'Image',
            'tag_id' => 'Tag',
            'state' => 'State',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('image_id',$this->image_id);
        $criteria->compare('tag_id',$this->tag_id);
        $criteria->compare('state',$this->state);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function add($image_id, $tagIds){
        $inDB = array();
        foreach ($this->check($image_id, $tagIds) as $i) {
            $inDB[] = $i->tag_id;
        }
        $notInDB = array_diff($tagIds, $inDB);

        $v = 0;
        foreach ($notInDB as $i) {
            $sql = "insert into tbl_image_tag (image_id, tag_id, state) values (:image_id, :tag_id, 1)";
            $parameters = array(":image_id"=>$image_id,
                                ":tag_id"=>$i);
            if(Yii::app()->db->createCommand($sql)->execute($parameters))
                $v++;
        }
        if($v == sizeof($notInDB)) return true;
    }

    public function check($image_id, $tagIds){
        $criteria=new CDbCriteria;
        $criteria->select = array('tag_id');
        $criteria->condition = 'image_id='.$image_id.' AND state=1';
        $criteria->addInCondition("tag_id", $tagIds);
        return $this->findAll($criteria);
    }

    public function deleteTags($image_id, $tagIds){
        $criteria = new CDbCriteria;
        $criteria->condition = 'image_id='.$image_id.' AND state=1';
        $criteria->addInCondition('tag_id',$tagIds);
        return $this->deleteAll($criteria);
    }

    public function getTag($image_id){
        $criteria=new CDbCriteria;
        $criteria->select = array('tag_id');
        $criteria->condition = 'image_id='.$image_id.' AND state=1';
        $criteria->limit = 1;
        return $this->findAll($criteria);
    }
}