
<?php

/**
 * This is the model class for table "{{audio_file}}".
 *
 * The followings are the available columns in table '{{audio_file}}':
 * @property integer $id
 * @property integer $audio_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class AudioFile extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AudioFile the static model class
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
        return '{{audio_file}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('audio_id, file, format, location', 'required'),
            array('audio_id, state', 'numerical', 'integerOnly'=>true),
            array('file, location', 'length', 'max'=>255),
            array('format', 'length', 'max'=>4),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, audio_id, file, format, location, state', 'safe', 'on'=>'search'),
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
            'audio' => array(self::BELONGS_TO, 'Audio', 'audio_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'audio_id' => 'Audio',
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
        $criteria->compare('audio_id',$this->audio_id);
        $criteria->compare('file',$this->file,true);
        $criteria->compare('format',$this->format,true);
        $criteria->compare('location',$this->location,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function saveAudio($audio_id, $tag_id, $file){
        $errorOccured = false;

        if($file){
            //Bestandsnamen, bestandslocaties
            $tags = Tag::model()->find('id=?',array($tag_id));
            $folder_name = preg_replace('/[^a-z0-9-_\.]/','', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);

            //Map voor audio files
            if(!is_dir(Yii::app()->basePath.'/../uploads/audio/')){
                mkdir(Yii::app()->basePath.'/../uploads/audio/');
            }

            //Map voor normale versie
            if(!is_dir(Yii::app()->basePath.'/../uploads/audio/'.$folder_name.'/')){
                mkdir(Yii::app()->basePath.'/../uploads/audio/'.$folder_name.'/');
            }

            //Schrijf bestand weg
            $fileContents = file_get_contents(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            file_put_contents(Yii::app()->basePath.'/../uploads/audio/'.$folder_name.'/'.$fileInfo['filename'].'.mp3', $fileContents);


            //Up into the clouds~
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
            // $postbody = array('data' => $fileContents, 'mimeType' =>'audio/mpeg3');
            // $gso = new Google_StorageObject();
            // $gso->setName($folder_name.'/'.$fileInfo['filename'].'.mp3');
            // $resp = $objects->insert('dcu-audio', $gso, $postbody);

            //Alle oude audio op state 0
            $this->updateAll(array('state'=>0),'audio_id='.$audio_id);

            //Insert de nieuwe doucment
            $attributes['audio_id'] = $audio_id;
            $attributes['file'] = $fileInfo['filename'];
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.mp3';
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