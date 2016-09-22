<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Форма регистрации
 */
class RegisterForm extends User
{
    public $error;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['username', 'validateUser'],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['verifyCode', 'captcha',  'captchaAction' => 'access/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'error' => '',
            'verifyCode' => 'Введите код с картинки',
        ];
    }
    
    /**
     * Проверка поля "имя пользователя",
     * поиск в базе и запрет ввода существующего.
     * Выводит сообщение радом с полем в случае неправильного ввода
     * @param string $attribute - поле
     * @param array $params
     */
    public function validateUser($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findByUsername($this->username);
            if ($user){
                $this->addError($attribute, 'Нельзя задать такое имя.');
            }
        }
    }

    /**
     * Проверка поля "email",
     * поиск в базе и запрет ввода существующего.
     * Выводит сообщение радом с полем в случае неправильного ввода
     * @param type $attribute
     * @param type $params
     */
    public function validateEmail($attribute, $params){
        $user = User::findByEmail($this->email);
        if ($user){
            $this->addError($attribute, 'Нельзя задать такой адрес электронной почты.');
        }
    }
    /**
     * Создание новой записи в таблице пользователей
     * @return boolean - true в случае успеха
     */
    public function register()
    {
        if (!$this->validate()){
            return false;
        }

        $this->generateAuthKey(Yii::$app->params['authKeyExpired']);
        try{
            if (!$this->insert(false)){
                $this->addError('error', "Внутренняя ошибка при регистрации пользователя. Обратитесь к администратору.");
                return false;
            }
        } catch (\Exception $ex) {
            Yii::error("Ошибка при регистрации пользователя." . $ex->getMessage());
            $this->addError('error', "Ошибка при регистрации пользователя.");
            return false;
        }
        return true;
    }

}
