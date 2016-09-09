<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
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
            // required fields
            [['username', 'email'], 'required'],
            // password is validated by compareAttribute()
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
            'verifyCode' => '',
        ];
    }
    
    /**
     * Validates.
     * This method serves as the inline validation for username.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
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

    public function validateEmail($attribute, $params){
        $user = User::findByEmail($this->email);
        if ($user){
            $this->addError($attribute, 'Нельзя задать такой адрес электронной почты.');
        }
    }
    /**
     * @return boolean whether the user is registered successfully
     */
    public function register()
    {
        if (!$this->validate()){
            return false;
        }

        $user = new User();
        $user->setUserName($this->username);
        $user->setEmail($this->email);
        $user->generateAuthKey(Yii::$app->params['authKeyExpired']);
        try{
            if (!$user->insert(false)){
                $this->addError('error', "Внутренняя ошибка при регистрации пользователя. Обратитесь к администратору.");
                return false;
            }
            $this->sendConfirm($user);
        } catch (\Exception $ex) {
            $this->addError($ex, "Ошибка при регистрации пользователя.");
            return false;
        }
        return true;
    }

    public static function sendConfirm($user){
        return Yii::$app->mailer->compose('confirm', ['model' => $user])
            ->setTo([$user->email => $user->username])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Подтверждение регистрации на сайте')
            ->send();
    }
}
