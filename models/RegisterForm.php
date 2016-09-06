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
    public $password;
    public $password2;
    public $error;
    public $verifyCode;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // required fields
            [['username', 'email', 'password', 'password2'], 'required'],
            // password is validated by compareAttribute()
            ['password', 'compare', 'compareAttribute' => 'password2'],
            ['username', 'validateUser'],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['verifyCode', 'captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'password2' => 'Подтверждение',
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
            $user = $this->getUser();
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

        if (!$this->getUser()){
            $user = new User();
            $user->setUserName($this->username);
            $user->setEmail($this->email);
            $user->setPassword($this->password);
            $user->generateAuthKey(Yii::$app->params['authKeyExpired']);
            try{
                if (!$user->insert(false)){
                    $this->addError('error', "Внутренняя ошибка при регистрации пользователя. Обратитесь к администратору.");
                    return false;
                }
                $user->setRole(User::ROLE_USER);
                $this->sendConfirm($user);
            } catch (\Exception $ex) {
                $this->addError($ex, "Ошибка при регистрации пользователя.");
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public static function sendConfirm($user){
        return Yii::$app->mailer->compose('confirm', ['model' => $user])
            ->setTo([$user->email => $user->username])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Подтверждение регистрации на сайте')
            ->send();
    }
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}
