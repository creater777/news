<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $createat
 * @property string $username
 * @property string $password
 * @property integer $active
 * @property string $email
 * @property string $authkey
 * @property integer $authkeyexpired
 * @property string $accessToken
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_USER = "user";
    const ROLE_MODERATOR = "moder";
    const ROLE_ADMIN = "admin";
    
    const PERMISSION_VIEWNEWS = "viewNews";
    const PERMISSION_EDITNEWS = "editNews";
    const PERMISSION_USEREDIT = "userEdit";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'authkey', 'accessToken'], 'required'],
            [['active'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password', 'authkey', 'accessToken'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createat' => 'Дата создания',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'active' => 'Активный',
            'email' => 'Email',
            'authkey' => 'Код авторизации',
            'authkeyexpired' => 'Срок действия кода авторизации',
            'accessToken' => 'Access Token',
        ];
    }

    public function setUserName($userName) {
        $this -> username = $userName;
    }

    public function setEmail($email) {
        $this -> email = $email;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by email
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token]);
    }

    public static function findByAuthKey($key)
    {
        return static::findOne(['authkey' => $key]);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authkey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authkey === $authKey &&
               $this->authkeyexpired + $this->createat >= time();
    }

    public function setAuthKey($authKey){
        $this->authkey = $authKey;
    }
    /** 
     * Generate authKey
     * 
     * @return type string
     */
    public function generateAuthKey($expiredTime){
        $this->authkey = hash('md5', $this->username . $this->email . (time() + $expiredTime), false);
        $this->authkeyexpired = $expiredTime;
        return $this->authkey;
    }

    /** 
     * Generate password hash
     * 
     * @param type string $password
     * @return type string
     */
    private function generatePasswordHash($password){
        return hash('md5', $password . $this->email, false);
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $this->generatePasswordHash($password);
    }
    
    public function setPassword($password){
        $this->password = $this->generatePasswordHash($password);
    }
    
    public function activateUser(){
        $this->active = 1;
    }

    public function isActive(){
        return $this->active === 1;
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)){
            return false;
        };
        if ($insert){
            $this->createat = time();
        }
        return true;
    }
    
    public function setRole($role){
        $roleObject = Yii::$app->authManager->getRole($role);
        Yii::$app->authManager->assign($roleObject, $this->getId());
    }
}