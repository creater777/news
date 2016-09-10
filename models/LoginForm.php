<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Форма авторизации
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
            ['verifyCode', 'captcha',  'captchaAction' => 'access/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить',
            'error' => '',
            'verifyCode' => 'Введите код с картинки',
        ];
    }
    
    /**
     * Проверка поля пароля
     * @param string $attribute - проверяемое поле
     * @param array $params - дополнительные параметры, не используеться
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Не верное имя пользователя или пароль');
            }
        }
    }

    /**
     * Вход в систему
     * @return boolean - true в случае успешного входа
     */
    public function login()
    {
        if ($this->validate() && $this->getUser()->isActive()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Поиск пользователя 
     * @return User|null - объект User
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
