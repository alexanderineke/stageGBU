<?php

/**
 * This is the model class for table "{{document_file}}".
 *
 * The followings are the available columns in table '{{document_file}}':
 * @property integer $id
 * @property integer $document_id
 * @property string $file
 * @property string $format
 * @property string $location
 * @property integer $state
 */
class DocumentFile extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DocumentFile the static model class
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
        return '{{document_file}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('document_id, file, format, location', 'required'),
            array('document_id, state', 'numerical', 'integerOnly'=>true),
            array('file, location', 'length', 'max'=>255),
            array('format', 'length', 'max'=>4),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, document_id, file, format, location, state', 'safe', 'on'=>'search'),
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
            'document' => array(self::BELONGS_TO, 'Document', 'document_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'document_id' => 'Document',
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
        $criteria->compare('document_id',$this->document_id);
        $criteria->compare('file',$this->file,true);
        $criteria->compare('format',$this->format,true);
        $criteria->compare('location',$this->location,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function genThumbs($file, $folder_name, $file_name)
    {
        function calcDimensions($max, $original)
        {
            if ($original['height'] > $original['width']) { //Portrait
                $dimensions = array('width' => ceil($max * $original['aspectRatio']), 'height' => $max);
            } elseif ($original['width'] > $original['height']) { //Landscape
                $dimensions = array('width' => $max, 'height' => ceil($max / $original['aspectRatio']));
            } else { //Vierkant
                $dimensions = array('width' => $max, 'height' => $max);
            }

            return $dimensions;
        }

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').''.date('H:i:s') . '-> Start <br/>');

        // Bekijk de aspect ratio van het bestand
        $original = array();
        $thumb = new Imagick(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file'].'[0]');
        $original['height'] = $thumb->getImageHeight();
        $original['width'] = $thumb->getImageWidth();   
        $original['aspectRatio'] = $original['width'] / $original['height'];
        $thumb->clear();

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Na aspect ratio <br/>');

        $dimensions = calcDimensions(1024, $original);
        exec('gs -q -o "'.dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_b.jpg" -dLastPage=1 -sDEVICE=jpeg -dJPEGQ=100 -dPDFFitPage -g'.$dimensions['width'].'x'.$dimensions['height'].' -dGraphicsAlphaBits=4 -dTextAlphaBits=4 "'.dirname(Yii::app()->request->scriptFile).'/uploads/'.$file['location'].'/'.$file['file'].'"', $output);
        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Na inlezen, resize en converteer actie #1 <br/>');

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #1 <br/>');

        $dimensions = calcDimensions(800, $original);
        $thumb = new Imagick(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_b.jpg');
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_c.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #2 <br/>');         

        $dimensions = calcDimensions(640, $original);
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_z.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #3 <br/>');                                    

        $dimensions = calcDimensions(500, $original);
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #4 <br/>');                          

        $dimensions = calcDimensions(320, $original);
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_n.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #5 <br/>');    

        $dimensions = calcDimensions(240, $original);
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_m.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #6 <br/>');        

        $dimensions = calcDimensions(100, $original);
        $thumb->resizeImage($dimensions['width'],$dimensions['height'],imagick::FILTER_LANCZOS,1);
        if (!$thumb->writeImage(dirname(Yii::app()->request->scriptFile).'/uploads/documenten/'.$folder_name.'/'.$file_name.'_t.jpg'))
            return false;

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Resize en schrijf actie #7 <br/>');      

        $thumb->clear();  

        // Yii::app()->user->setFlash('info', Yii::app()->user->getFlash('info', '').'<br>'.date('H:i:s') . '-> Eind <br/>');

        return true;
    }

    public function saveDocument($document_id, $tag_id, $file)
    {
        if($file){
            // Bestandsnamen, bestandslocaties
            $tags = Tag::model()->find('id=?',array($tag_id));
            $folder_name = preg_replace('/[^a-z0-9-_\.]/','', strtolower($tags->name));
            $fileInfo = pathinfo(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            $file_name = $fileInfo['filename'];

            // Map voor documenten
            if(!is_dir(Yii::app()->basePath.'/../uploads/documenten/')){
                mkdir(Yii::app()->basePath.'/../uploads/documenten/');
            }

            // Map voor specifieke document
            if(!is_dir(Yii::app()->basePath.'/../uploads/documenten/'.$folder_name.'/')){
                mkdir(Yii::app()->basePath.'/../uploads/documenten/'.$folder_name.'/');
            }

            // Maak thumbnails aan
            if(!$this->genThumbs($file, $folder_name, $file_name)){
                return false;
            }
                                       
            // Schrijf bestand weg naar uiteidenlijke locatie
            $fileContents = file_get_contents(Yii::app()->basePath.'/../uploads/'.$file['location'].'/'.$file['file']);
            if(! $fileContents || ! file_put_contents(Yii::app()->basePath.'/../uploads/documenten/'.$folder_name.'/'.$file_name.'.pdf', $fileContents)){
                return false;
            }
             echo 'sweet'; 
            // Up into the clouds~
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
            // $postbody = array('data' => $fileContents, 'mimeType' =>'application/pdf');
            // $gso = new Google_StorageObject();
            // $gso->setName($folder_name.'/'.$file_name.'.pdf');
            // $resp = $objects->insert('dcu-pdf', $gso, $postbody);

            // Alle oude documenten op state 0
            $this->updateAll(array('state'=>0),'document_id='.$document_id);

            // Insert het nieuwe doucment
            $attributes['document_id'] = $document_id;
            $attributes['file'] = $file_name;
            $attributes['location'] = $folder_name;
            $attributes['format'] = '.pdf';
            $attributes['state'] = 1;
            $this->setIsNewRecord(true);
            $this->attributes = $attributes;
            if(!$this->insert())
                return false;
        }

        return true;
    }
}