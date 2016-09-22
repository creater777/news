<?php

namespace app\components\notifications;

use Yii;
use app\models\User;

/**
 * Description of NotificationEmail
 *
 * @author SIR
 */
class Email{

    public static function send($event){
        Yii::warning($event);
    }
    
    public function afterSaveNews($event){
        $users = User::findActual()->andWhere(['notificationemail' => $user->notificationemail])->all();
        foreach($users as $user){
            if (empty($user->email)){
                continue;
            }
            Yii::$app->mailer->compose('newnews', ['model' => $this])
                ->setTo([$user->email => $user->username])
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setSubject('Новая новость')
                ->send();            
        }
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
