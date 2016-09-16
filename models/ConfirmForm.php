<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Форма подтверждения регистрации
 * содержит поле ввода пароля
 */
class ConfirmForm extends Model
{
    public $password;
    public $password2;
    public $error;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // required fields
            [['password', 'password2'], 'required'],
            // password is validated by compareAttribute()
            ['password', 'compare', 'compareAttribute' => 'password2'],
            ['verifyCode', 'captcha',  'captchaAction' => 'access/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password2' => 'Подтверждение',
            'error' => '',
            'verifyCode' => '',
        ];
    }
    
    /**
     * Подтверждение регистрации,
     * запись пароля и активация пользователя
     * @return boolean whether the user is registered successfully
     */
    public function activate($user)
    {
        if (!$this->validate() || !isset($user) || !($user instanceof app\models\User)){
            return false;
        }

        $user->setPassword($this->password);
        $user->activateUser();
        try{
            if (!$user->update(false)){
                $this->addError('error', "Внутренняя ошибка при регистрации пользователя. Обратитесь к администратору.");
                return false;
            }
            $user->setRole(User::ROLE_USER);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage() . $ex->getTraceAsString());
            $this->addError('error', "Ошибка при регистрации пользователя.");
            return false;
        }
        return true;
    }
}
