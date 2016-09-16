<?php

namespace app\models;

use Yii;

/**
 * User - модель пользоватея, реализует методы доступа и модель поведения.
 *
 * @property integer $id - идентификатор
 * @property integer $createat - дата создания
 * @property integer $updateat - дата обновления
 * @property string $username  - имя пользователя
 * @property string $password - хеш пароля
 * @property integer $active - признак активности
 * @property string $email - email
 * @property integer $notificationonline - признак оповещения online
 * @property integer $notificationemail - признак оповещения по email
 * @property string $authkey - код авторизации
 * @property integer $authkeyexpired - срок действия кода авторизации
 * @property string $accessToken
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * Роли пользователей
     */
    const ROLE_USER = "user";
    const ROLE_MODERATOR = "moder";
    const ROLE_ADMIN = "admin";
    
    /**
     * Ограничения в правах
     */
    const PERMISSION_VIEWNEWS = "viewNews";
    const PERMISSION_EDITPROFILE = "editProfile";
    const PERMISSION_EDITNEWS = "editNews";
    const PERMISSION_USEREDIT = "userEdit";

    /**
     * Получение списка ролей
     * @return array
     */
    public static function getRoleList(){
        return [
            self::ROLE_USER => 'Пользователь',
            self::ROLE_MODERATOR => 'Модератор',
            self::ROLE_ADMIN => 'Администратор',
        ];
    }

    /**
     * Таблица в БД
     * @return string
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * Поиск по имени пользователя
     * @param  string      $username
     * @return static|null объект User
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Поиск по email
     * @param  string      $email
     * @return static|null объект User
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Поиск по id
     * @param  string      $id
     * @return static|null объект User
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Поиск по AccessToken
     * @param  string      $token
     * @return static|null объект User
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token]);
    }

    /**
     * Поиск по коду авторизации
     * @param  string      $key
     * @return static|null объект User
     */
    public static function findByAuthKey($key)
    {
        return static::findOne(['authkey' => $key]);
    }

    /**
     * Поиск всех активных
     * @return array User
     */
    public static function findAllActual() {
        return static::find()->andWhere('active = 1')->all();
    }
    
    /**
     * Получение идентификатора
     * @return type int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получение кода авторизации
     * @return type string
     */
    public function getAuthKey()
    {
        return $this->authkey;
    }

    /**
     * Проверка кода авторизации
     * @return type boolean - true если верен
     */
    public function validateAuthKey($authKey)
    {
        return $this->authkey === $authKey &&
               ($this->authkeyexpired + $this->updateat) >= time();
    }

    /**
     * Генерация кода авторизации
     * @return type boolean - true если верен
     */
    public function generateAuthKey($expiredTime){
        $this->authkey = hash('md5', $this->username . $this->email, false);
        $this->authkeyexpired = $expiredTime;
        return $this->authkey;
    }

    /**
     * Генерация хеша пароля
     * @param type $password
     * @return type
     */
    private function generatePasswordHash($password){
        return hash('md5', $password . $this->email, false);
    }
    
    /**
     * Проверка пароля
     * @param  string  $password
     * @return boolean - true если верен
     */
    public function validatePassword($password)
    {
        return $this->password === $this->generatePasswordHash($password);
    }
    
    /**
     * Установка пароля с генерацией хеша
     * @param type $password
     */
    public function setPassword($password){
        $this->password = $this->generatePasswordHash($password);
    }
    
    /**
     * Активация пользователя
     */
    public function activateUser(){
        $this->active = 1;
    }

    /**
     * Проверка на активность
     * @return type - true если активен
     */
    public function isActive(){
        return $this->active === 1;
    }

     /**
     * Геттеры и сеттеры виртуального поля роли пользователя.
     * Присваивает пользователю соответствующую роль
     * @param type $role
     */
    public function setRole($role){
        $roleObject = Yii::$app->authManager->getRole($role);
        Yii::$app->authManager->revokeAll($this->getId());
        Yii::$app->authManager->assign($roleObject, $this->getId());
    }
    
    /**
     * Возвращает список ролей пользователя
     * @return type
     */
    public function getRole(){
        $roles = [];
        $appRoles = Yii::$app->authManager->getRolesByUser($this->getId());
        if (!is_array($appRoles) || empty($appRoles)){
            return null;
        }
        foreach ($appRoles as $role){
            $roles[] = $role->name;
        }
        return $roles[0];
    }
    
   /**
     * Перед сохранением пользователя устанавливаем дату создания и обновления
     * генерируем новый код активации
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)){
            return false;
        }
        if ($insert){
            $this->createat = time();
        }
        $this->updateat = time();
        return true;
    }
    
    /**
     * После сохранения рассылаем администраторам оповещение по email при создании.
     * Отправляем пользователю оповещение при смене пароля
     * @param type $insert
     * @param type $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if (Yii::$app->id == 'basic-console'){
            return true;
        }
        if ($insert){
            RegisterForm::sendConfirm($this);
            $admins = Yii::$app->authManager->getUserIdsByRole(self::ROLE_ADMIN);
            foreach ($admins as $id){
                RegisterForm::sendNewUser($this->findIdentity($id));
            }
        }
        if(!$insert && isset($changedAttributes['password'])){
            RegisterForm::sendPswChanged($this);
        }
        return true;
    }
    
    /**
     * После удаления пользователя, удаляем из модели доступа
     */
    public function afterDelete() {
        parent::afterDelete();
        Yii::$app->authManager->revokeAll($this->getId());
    }
}