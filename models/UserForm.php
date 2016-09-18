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
            [['username', 'email'], 'required'],
            [['active', 'notificationonline', 'notificationemail'], 'integer'],
            [['username', 'email', 'role'], 'string', 'max' => 255],
            [['authkey', 'accessToken'], 'string', 'max' => 255],
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