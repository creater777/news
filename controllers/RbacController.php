<?php
namespace app\controllers;
 
use Yii;
use yii\console\Controller;
//use app\controllers\UserGroupRule;
 
class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные

        //Права просмотра новостей
        $viewNews = $auth->createPermission('viewNews');
        $viewNews->description = 'Просмотр новостей';
        $auth->add($viewNews);

        //Права редактора
        $editNews = $auth->createPermission('editNews');
        $editNews->description = 'Редактирование новостей';
        $auth->add($editNews);

        //Права админа
        $userEdit = $auth->createPermission('userEdit');
        $userEdit->description = 'Админ панель';
        $auth->add($userEdit);

        //Добавляем роли
        $user = $auth->createRole('user');
        $user->description = 'Пользователь';
        $auth->add($user);
        
        $moder = $auth->createRole('moder');
        $moder->description = 'Модератор';
        $auth->add($moder);
        $auth->addChild($moder,$editNews);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);
        $auth->addChild($admin,$editNews);
        $auth->addChild($admin,$userEdit);
        
    }
}