<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property date $createat
 * @property string $username
 * @property string $password
 * @property integer $active
 * @property string $email
 * @property string $authKey
 * @property integer $authExpiredTime
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
            [['username', 'password', 'authKey', 'accessToken'], 'required'],
            [['active'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password', 'authKey', 'accessToken'], 'string', 'max' => 255],
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
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'active' => 'Активный',
            'email' => 'Email',
            'authKey' => 'Код авторизации',
            'accessToken' => 'Access Token',
        ];
    }

    public function setUserName($userName) {
        $this -> username = $userName;
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
        return static::findOne(['access_token' => $token]);
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
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public static function validateAuthKey($authKey)
    {
        $user = static::findOne(['authkey' => $authKey]);
        if (!$user){
            return false;
        }
        return $user->authExpiredTime + $user->createat >= Date();
    }

    /** 
     * Generate authKey
     * 
     * @return type string
     */
    public function generateAuthKey($expiredTime){
        $this->authKey = hash('md5', $this->username . $this->email . (time() + $expiredTime), false);
        $this->authExpiredTime = $expiredTime;
        return $this->authKey;
    }

    /** 
     * Generate password hash
     * 
     * @param type string $password
     * @return type string
     */
    public function getPasswordHash($password){
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
        return $this->password === $this->getPasswordHash($password);
    }
    
    public function setPassword($password){
        $this->password = $this->getPasswordHash($password);
    }
    
    public function activateUser(){
        $this->active = 1;
    }

    public function isActive(){
        return $this->active === 1;
    }

}