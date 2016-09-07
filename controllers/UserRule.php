<?php
namespace app\controllers;
 
use Yii;
use yii\rbac\Rule;
use app\models\User;
 
class UserRule extends Rule
{
    public $name = 'isMyProfile';
    
    public function execute($user, $item, $params)
    {
        return isset($params['users']) ? $params['users']->getId() == Yii::$app->user->getId() : false;
    }
}