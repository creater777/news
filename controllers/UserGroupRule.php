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
    
    public function execute($user, $item, $params)
    {
        //Получаем массив пользователя из базы
        $user = ArrayHelper::getValue($params, 'user', User::findOne($user));
        if ($user) {
            $role = Yii::$app->authManager->getPermissionsByUser($user->getId())->ruleName;
            if ($item->name === User::ROLE_ADMIN) {
                return $role == User::ROLE_ADMIN;
            } elseif ($item->name === User::ROLE_MODER) {
                return $role == User::ROLE_MODER;
            } elseif ($item->name === User::ROLE_USER) {
                return $role == User::ROLE_USER;
            }
        }
        return false;
    }
}