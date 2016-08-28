<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $active
 * @property string $email
 * @property integer $usergroupid
 * @property string $authKey
 * @property string $accessToken
 *
 * @property Usersgroup $usergroup
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_USER = 10;
    const ROLE_MODERATOR = 20;
    const ROLE_ADMIN = 30;

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
            [['username', 'password', 'usergroupid', 'authKey', 'accessToken'], 'required'],
            [['active', 'usergroupid'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password', 'authKey', 'accessToken'], 'string', 'max' => 40],
            [['username'], 'unique'],
            [['usergroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Usersgroup::className(), 'targetAttribute' => ['usergroupid' => 'id']],
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
            'usergroupid' => 'Группа',
            'authKey' => 'Код авторизации',
            'accessToken' => 'Access Token',
        ];
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

    /**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
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
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsergroup()
    {
        return $this->hasOne(Usersgroup::className(), ['id' => 'usergroupid']);
    }
    
    /** 
     * Generate password hash
     * 
     * @param type string $password
     * @return type string
     */
    public static function getPasswordHash($password){
        return hash('md5', $password, false); 
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
}