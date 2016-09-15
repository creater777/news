<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Форма регистрации
 */
class RegisterForm extends Model
{
    public $username;
    public $email;
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

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->generateAuthKey(Yii::$app->params['authKeyExpired']);
        try{
            if (!$user->insert(false)){
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

    /**
     * Формирование и отправка письма с ссылкой подтвержения регистрации
     * @param type $user - объект User
     * @return type
     */
    public static function sendConfirm($user){
        return Yii::$app->mailer->compose('confirm', ['model' => $user])
            ->setTo([$user->email => $user->username])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Подтверждение регистрации на сайте')
            ->send();
    }
    
    /**
     * Формирование и отправка письма с оповещением о регистрации нового пользователя
     * @param type $user - объект User
     * @return type
     */
    public static function sendNewUser($user){
        return Yii::$app->mailer->compose('newuser', ['model' => $user])
            ->setTo([$user->email => $user->username])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Зарегистрирован пользователь ' . $user->username)
            ->send();

    }
    
    /**
     * Формирование и отправка письма с оповещением о смене пароля
     * @param type $user - объект User
     * @return type
     */
    public static function sendPswChanged($user){
        return Yii::$app->mailer->compose('pswchanged', ['model' => $user])
            ->setTo([$user->email => $user->username])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Изменен пароль' . $user->username)
            ->send();
    }
}
