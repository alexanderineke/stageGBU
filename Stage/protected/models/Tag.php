
<?php

/**
 * This is the model class for table "{{tag}}".
 *
 * The followings are the available columns in table '{{tag}}':
 * @property integer $id
 * @property string $name
 * @property integer $state
 */
class Tag extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Tag the static model class
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
        return '{{tag}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('state', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, slug, state', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Naam',
            'slug' => 'Veilige naam',
            'state' => 'Status',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('slug',$this->slug,true);
        $criteria->compare('state',$this->state);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function add($tagArr, $slugArr){
        $addedTags = array();
        foreach ($tagArr as $i => $tag) {
            $sql = "insert into tbl_tag (name, slug, state) values (:name, :slug, 1)";
            $parameters = array(":name"=>$tag,
                                ":slug"=>$slugArr[$i]);
            if(Yii::app()->db->createCommand($sql)->execute($parameters))
                $addedTags[] = Yii::app()->db->getLastInsertID();
        }
        if(sizeof($addedTags) == sizeof($tagArr)) return $addedTags;
    }

    public function check($slugArr){
        $criteria=new CDbCriteria;
        $criteria->select = array('id', 'slug');
        $criteria->condition = 'state=1';
        $criteria->addInCondition("slug", $slugArr);

        return $this->findAll($criteria);
    }


    public function findTags($term){
        $criteria=new CDbCriteria;
        $criteria->select = array('id', 'name');
        $criteria->condition = 'state=1';
        $criteria->compare('slug',$term,true);
        $criteria->limit = 10;

        return $this->findAll($criteria);
    }

    public function findTagsByID($arr, $slug=false){
        $criteria=new CDbCriteria;
        $criteria->select = ($slug ? array('id', 'name', 'slug') : array('id', 'name'));
        $criteria->condition = 'state=1';
        $criteria->compare('id',$arr);
        $criteria->limit = 10;

        return $this->findAll($criteria);       
    }
}