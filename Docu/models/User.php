<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface{

    public $repeat_password;
    //will hold the encrypted password for update actions.
    public $initialPassword;

    public static function tableName() {
        return '{{%user}}';
    }

    public function rules() {
        if ($this->scenario === 'update') {
            return [
                [['username'], 'unique'],
                [['username', 'email', 'roles'], 'required'],
                [['username', 'password', 'repeat_password', 'email'], 'string', 'min' => 5, 'max' => 128],
                ['repeat_password', 'compare', 'compareAttribute'=>'password', 'skipOnEmpty' => false, 'message'=>"Passwords don't match"],
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                //[['id', 'username', 'email'], 'safe', 'on' => 'search']
            ];
        } else {
            return [
                [['username'], 'unique'],
                [['username', 'password', 'repeat_password', 'email', 'roles'], 'required'],
                [['username', 'password', 'repeat_password', 'email'], 'string', 'min' => 5, 'max' => 128],
                ['repeat_password', 'compare', 'compareAttribute'=>'password', 'skipOnEmpty' => false, 'message'=>"Passwords don't match"],
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
               // [['id', 'username', 'email'], 'safe', 'on' => 'search']
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        if ($this->scenario === 'update') {
            return [
                'id' => 'ID',
                'username' => 'Gebruikersnaam',
                'password' => 'Nieuw wachtwoord',
                'repeat_password' => 'Herhaal nieuwe wachtwoord',
                'email' => 'Email',
                'roles' => 'Rechten'
            ];
        } else {
            return [
                'id' => 'ID',
                'username' => 'Gebruikersnaam',
                'password' => 'Wachtwoord',
                'repeat_password' => 'Herhaal wachtwoord',
                'email' => 'Email',
                'roles' => 'Rechten',
            ];
        }
    }

    public function search($params) {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query
                ->andFilterWhere([['like', 'id', $this->id],
                    ['like', 'username', $this->username],
                    ['like', 'email', $this->email]]);
        return $dataProvider;
    }

   public function beforeSave($insert) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
              return true;
    }  

    public function afterFind() {
        //reset the password to null because we don't want the hash to be shown.
        $this->initialPassword = $this->password;
        $this->password = null;

        parent::afterFind();
    }

    public function saveModel($data = []) {
        //because the hashes needs to match
         $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
       
        if (!empty($data['password']) && !empty($data['repeat_password'])) {
            $salt = $this->generateSalt(12);
            $data['password'] = $this->hashPassword($data['password'], $salt);
            $data['repeat_password'] = $this->hashPassword($data['repeat_password'], $salt);
        }

        $this->attributes = $data;

        if (!$this->save()) {
            return false;
        }

        return true;
    }

    protected function generateSalt($cost = 13) {
        if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
            throw new Exception("cost parameter must be between 4 and 31");
        }
        $rand = [];
        for ($i = 0; $i < 8; $i += 1) {
            $rand[] = pack('S', mt_rand(0, 0xffff));
        }
        $rand[] = substr(microtime(), 2, 6);
        $rand = sha1(implode('', $rand), true);
        $salt = '$2a$' . str_pad((int) $cost, 2, '0', STR_PAD_RIGHT) . '$';
        $salt .= strtr(substr(base64_encode($rand), 0, 22), ['+' => '.']);
        return $salt;
    }
    
     public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    
    public static function findIdentityByAccessToken($token, $type = null)
    {
          return static::findOne(['access_token' => $token]);
    }
    
    public static function findByUsername($username) {
     return static::findOne(['username' => $username]);
    }
    
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    public function getAuthKey()
    {
        return;// $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public function validatePassword($password)
    {
      return Yii::$app->getSecurity()->validatePassword($password, $this->initialPassword);
    }
    
    public function setPassword($password)
    {
     $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        return;// $this->auth_key = Security::generateRandomKey();
    }
    public function getDocuments() {
        return $this->hasMany(Document::className(), ['user_id' => 'id']);
    }

    public function getImages() {
        return $this->hasMany(Image::className(), ['user_id' => 'id']);
    }

}
