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
    public $password;
    public $password2;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'password2'], 'required'],
            // password is validated by compareAttribute()
            ['password', 'compare', 'compareAttribute' => 'password2'],
            // password is validated by validatePassword()
            ['username', 'validateUser'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'password2' => 'Подтверждение',
        ];
    }
    
    /**
     * Validates.
     * This method serves as the inline validation for password.
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

    /**
     * Validates.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        return;
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
            $user->username = $this->username;
            $user->password = $this->password; 
            try{
                $user->insert();
            } catch (Exception $ex) {
                $this->addError($ex, "Ошибка при регистрации пользователя");
                return false;
            }
            return true;
        } else {
            return false;
        }
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
