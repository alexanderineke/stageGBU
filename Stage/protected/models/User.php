<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 */
class User extends CActiveRecord
{
   	// holds the password confirmation word
    public $repeat_password;
 
    //will hold the encrypted password for update actions.
    public $initialPassword;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		if($this->scenario === 'update'){
			return array(
				array('username', 'unique'),
				array('username, email, roles', 'required'),
				array('username, password, repeat_password, email', 'length', 'min'=>5, 'max'=>128),
				array('password', 'compare', 'compareAttribute'=>'repeat_password'),			
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, username, email', 'safe', 'on'=>'search'),
			);
		}else{
			return array(
				array('username', 'unique'),				
				array('username, password, repeat_password, email, roles', 'required'),
				array('username, password, repeat_password, email', 'length', 'min'=>5, 'max'=>128),
				array('password', 'compare', 'compareAttribute'=>'repeat_password'),			
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, username, email', 'safe', 'on'=>'search'),
			);
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user_id' => array(self::HAS_MANY, 'Image', 'user_id'),
			//'document_user_id' => array(self::HAS_MANY, 'Document', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		if($this->scenario === 'update'){
			return array(
				'id' => 'ID',
				'username' => 'Gebruikersnaam',
				'password' => 'Nieuw wachtwoord',
				'repeat_password' => 'Herhaal nieuwe wachtwoord',
				'email' => 'Email',
				'roles' => 'Rechten'
			);
		}else{
			return array(
				'id' => 'ID',
				'username' => 'Gebruikersnaam',
				'password' => 'Wachtwoord',
				'repeat_password' => 'Herhaal wachtwoord',
				'email' => 'Email',
				'roles' => 'Rechten',
			);
		}
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function beforeSave()
    {
        // in this case, we will use the old hashed password.
        if(empty($this->password) && empty($this->repeat_password) && !empty($this->initialPassword))
            $this->password=$this->repeat_password=$this->initialPassword;

        return parent::beforeSave();
    }
 
    public function afterFind()
    {

        //reset the password to null because we don't want the hash to be shown.
        $this->initialPassword = $this->password;
        $this->password = null;
 		
        parent::afterFind();
    }
 
    public function saveModel($data=array())
    {
            //because the hashes needs to match
            if(!empty($data['password']) && !empty($data['repeat_password']))
            {
            	$salt = $this->generateSalt(12);	
                $data['password'] = $this->hashPassword($data['password'], $salt);
                $data['repeat_password'] = $this->hashPassword($data['repeat_password'], $salt);
            }
 
            $this->attributes=$data;
 
            if(!$this->save())
                return false;
 
         return true;
    }

	public function validatePassword($password)
    {
    	$this->password=$this->initialPassword;
        return crypt($password,$this->password)===$this->password;
    }

    protected function generateSalt($cost = 13)
	{
		if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
	        throw new Exception("cost parameter must be between 4 and 31");
	    }
	    $rand = array();
	    for ($i = 0; $i < 8; $i += 1) {
	        $rand[] = pack('S', mt_rand(0, 0xffff));
	    }
	    $rand[] = substr(microtime(), 2, 6);
	    $rand = sha1(implode('', $rand), true);
	    $salt = '$2a$' . str_pad((int) $cost, 2, '0', STR_PAD_RIGHT) . '$';
	    $salt .= strtr(substr(base64_encode($rand), 0, 22), array('+' => '.'));
	    return $salt;
	}
 
    public function hashPassword($password, $salt)
    {
        if(!empty($salt)) return crypt($password, $salt);
        else return crypt($password, $this->generateSalt(12));
    }
}