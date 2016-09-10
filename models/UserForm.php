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
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['active'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password', 'authkey', 'accessToken'], 'string', 'max' => 255],
            ['password', 'compare', 'compareAttribute' => 'password2'],
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
            'password' => 'Пароль',
            'password2' => 'Повторите ввод',
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
     * Значение поля подтверждения пароля равно текущему паролю
     */
    public function getPassword2(){
        return $this->password;
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
     * @param type $value
     */
    public function getDateCreateInner(){
        return $this->createat ? date("d.m.Y", $this->createat) : '';
    }
}