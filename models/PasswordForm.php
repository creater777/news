<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * Форма подтверждения регистрации
 * содержит поле ввода пароля
 */
class PasswordForm extends User
{
    public $passwordInner;
    public $error;

    public function rules()
    {
        return [
            [['passwordInner'], 'required'],
            ['passwordInner', 'compare', 'compareAttribute' => 'passwordVisual2'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'passwordInner' => 'Пароль',
            'passwordVisual2' => 'Повторите ввод',
            'error' => '',
        ];
    }

    /**
     * На форме пароль не отображаем
     */
    public function getPasswordVisual2(){
        return $this->passwordInner;
    }
    
    /**
     * Сохранение пароля
     * @return type
     */
    public function savePassword(){
        $this->setPassword($this->passwordInner);
        return $this->save(false);
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

        $user->setPassword($this->passwordInner);
        $user->activateUser();
        try{
            if (!$user->update(false)){
                $this->addError('error', "Внутренняя ошибка. Обратитесь к администратору.");
                return false;
            }
            $user->setRole(User::ROLE_USER);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage() . $ex->getTraceAsString());
            $this->addError('error', "Ошибка при сохранении параметров пользователя.");
            return false;
        }
        return true;
    }
}
