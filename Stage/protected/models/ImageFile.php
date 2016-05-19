
<?php

/**
 * This is the model class for table "{{image_file}}".
 *
 * The followings are the available columns in table '{{image_file}}':
 * @property integer $id
 * @property integer $image_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class ImageFile extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ImageFile the static model class
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
        return '{{image_file}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('image_id, file, format, location', 'required'),
            array('image_id, state', 'numerical', 'integerOnly'=>true),
            array('file, location', 'length', 'max'=>255),
            array('format', 'length', 'max'=>4),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, image_id, file, format, location, state', 'safe', 'on'=>'search'),
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
            'image' => array(self::BELONGS_TO, 'Image', 'image_id'),
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
            'file' => 'File',
            'format' => 'Format',
            'location' => 'Location',
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
        $criteria->compare('file',$this->file,true);
        $criteria->compare('format',$this->format,true);
        $criteria->compare('location',$this->location,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function saveImage($image_id, $tag_id, $file){
        $errorOccured = false;

        if($file){
            //Bestandsnamen, bestandslocaties
            $tags = Tag::model()->find('id=?',array($tag_id));
            $folder_name = preg_replace('/[^a-z0-9-_\.]/','', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);

            //Map voor afbeeldingen
            if(!is_dir(Yii::app()->basePath.'/../uploads/afbeeldingen/')){
                mkdir(Yii::app()->basePath.'/../uploads/afbeeldingen/');
            }

            //Map voor de normale versie (max: 750x500)
            if(!is_dir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/')){
                mkdir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/');
            }

            //Map voor de thumbnail (fixed: 100x100)
            if(!is_dir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/thumb/')){
                mkdir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/thumb/');
            }

            //Map voor de full versie
            if(!is_dir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/full/')){
                mkdir(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/full/');
            }

            //Genereer normale versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            $thumb->resize(750,500);
            if(!$thumb->save(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/'.$fileInfo['filename'].'.jpg', 'JPG'))
                $errorOccured = true;

            //Genereer thumbnail versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            $thumb->adaptiveResize(100,100);
            if(!$thumb->save(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/thumb/'.$fileInfo['filename'].'.jpg', 'JPG'))
                $errorOccured = true;

            //Genereer full versie
            $thumb = Yii::app()->phpThumb->create(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            if(!$thumb->save(Yii::app()->basePath.'/../uploads/afbeeldingen/'.$folder_name.'/full/'.$fileInfo['filename'].'.jpg', 'JPG'))
                $errorOccured = true;

            //Up into the clouds~
            // $fileContents = file_get_contents(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);

            // require_once 'google-api-php-client/src/Google_Client.php';
            // require_once 'google-api-php-client/src/contrib/Google_StorageService.php';

            // $client = new Google_Client();
            // $client->setApplicationName('Documentatie Centrum Website');

            // $key = file_get_contents('protected/6ecc407906ebf13b86f5413e89de1ca36ae1e7fe-privatekey.p12');
            // $client->setAssertionCredentials(new Google_AssertionCredentials('405222382916-rr450c1pf978fcfnhkfeq37n3er0t591@developer.gserviceaccount.com',
            //   array('https://www.googleapis.com/auth/devstorage.full_control'),
            //   $key));

            // $StorageService = new Google_StorageService($client);
            // $objects = $StorageService->objects;
            // $postbody = array('data' => $fileContents, 'mimeType' =>'image/jpeg');
            // $gso = new Google_StorageObject();
            // $gso->setName($folder_name.'/'.$fileInfo['filename'].'.jpg');
            // $resp = $objects->insert('dcu-image', $gso, $postbody);

            //Alle oude afbeeldingen op state 0
            $this->updateAll(array('state'=>0),'image_id='.$image_id);

            //Insert de nieuwe afbeelding
            $attributes['image_id'] = $image_id;
            $attributes['file'] = $fileInfo['filename'];
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.jpg';
            $attributes['state'] = 1;
            $this->setIsNewRecord(true);
            $this->attributes = $attributes;
            if(!$this->insert())
                $errorOccured = true;
        }
        if(!$errorOccured)
            return true;
    }
}