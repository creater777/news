<?php
namespace app\controllers;
 
use Yii;
use yii\rbac\Rule;
use app\models\User;
 
class UserGroupRule extends Rule
{
//    public $actions;
//    public $allow;
//    public $roles;
//    public function allows($user){
//        return false;
//    }
    
    public $name = 'userRole';
    
    public function execute($user, $item, $params)
    {
        //Получаем массив пользователя из базы
        $user = ArrayHelper::getValue($params, 'user', User::findOne($user));
        if ($user) {
            $role = $user->usergroup;
            if ($item->name === 'admin') {
                return $role == User::ROLE_ADMIN;
            } elseif ($item->name === 'moder') {
                return $role == User::ROLE_MODER;
            } elseif ($item->name === 'user') {
                return $role == User::ROLE_USER;
            }
        }
        return false;
    }
}