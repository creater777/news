<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * UserForm - модель формы пользоватея, 
 * реализует методы доступа и модель поведения формы.
 */
class UserForm extends User
{
    private $passwordInner = '';
    
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['active', 'notificationonline', 'notificationemail'], 'integer'],
            [['username', 'email', 'role'], 'string', 'max' => 255],
            [['password', 'authkey', 'accessToken'], 'string', 'max' => 255],
            ['passwordVisual', 'compare', 'compareAttribute' => 'passwordVisual2'],
            [['username'], 'unique'],
            ['email', 'validateEmail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createat' => 'Дата создания',
            'dateCreateInner' => 'Дата создания',
            'username' => 'Имя пользователя',
            'passwordVisual' => 'Пароль',
            'passwordVisual2' => 'Повторите ввод',
            'active' => 'Активный',
            'notificationonline' => 'Включить оповещение на сайте',
            'notificationemail' => 'Оповещать о новых новостях по email',
            'email' => 'Email',
            'role' => 'Роль пользователя',
            'authkey' => 'Код авторизации',
            'authkeyexpired' => 'Срок действия кода авторизации',
            'accessToken' => 'Access Token',
        ];
    }

    /**
     * На форме пароль не отображаем
     */
    public function getPasswordVisual2(){
        return $this->passwordInner;
    }
    
    /**
     * На форме пароль не отображаем
     */
    public function getPasswordVisual(){
        return $this->passwordInner;
    }

    /**
     * Установка пароля
     */
    public function setPasswordVisual($password){
        $this->passwordInner = $password;
    }

    /**
     * Проверка поля email
     * @param type $attribute
     * @param type $params
     */
    public function validateEmail($attribute, $params){
        $user = User::findByEmail($this->email);
        if ($user && $this->isNewRecord){
            $this->addError($attribute, 'Нельзя задать такой адрес электронной почты.');
        }
    }

    /**
     * Виртуальное поле для отображения даты создания
     * @param type $value
     */
    public function setDateCreateInner($value){
        $this->createat = $value ? strtotime($value) : null;
    }

    /**
     * Виртуальное поле для отображения даты создания
     */
    public function getDateCreateInner(){
        return $this->createat ? date("d.m.Y", $this->createat) : '';
    }

    /**
     * На визуальной форме изменять роль пользователя может только администратор
     * @param type $role - роль пользователя
     * @return type
     */
    public function setRole($role) {
        if (!Yii::$app->user->can(self::PERMISSION_USEREDIT)){
            return;
        }        
        parent::setRole($role);
    }
    
    /**
     * Перед сохранением, генерация нового хеша пароля и кода активации
     * @param type $insert
     */
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)){
            return false;
        }
        $this->generateAuthKey(Yii::$app->params['authKeyExpired']);
        if (!empty($this->passwordInner)){
            $this->setPassword($this->passwordInner);
        }
        return true;
    }
}