<?php

namespace app\components;

use Yii;

/**
 * Description of NotificationEmail
 *
 * @author SIR
 */
class Email{

    public static function send($event){
        Yii::warning($event);
    }
}
