<?php
namespace app\controllers;
 
use Yii;
use yii\console\Controller;
use app\models\User;
 
class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные

        //Права просмотра новостей
        $viewNews = $auth->createPermission(User::PERMISSION_VIEWNEWS);
        $viewNews->description = 'Просмотр новостей';
        $auth->add($viewNews);

        //Права редактора
        $editNews = $auth->createPermission(User::PERMISSION_EDITNEWS);
        $editNews->description = 'Редактирование новостей';
        $auth->add($editNews);

        //Права админа
        $userEdit = $auth->createPermission(User::PERMISSION_USEREDIT);
        $userEdit->description = 'Админ панель';
        $auth->add($userEdit);

        //Добавляем роли
        $user = $auth->createRole(User::ROLE_USER);
        $user->description = 'Пользователь';
        $auth->add($user);
        $auth->addChild($user,$viewNews);
        
        $moder = $auth->createRole(User::ROLE_MODERATOR);
        $moder->description = 'Модератор';
        $auth->add($moder);
        $auth->addChild($moder,$user);
        $auth->addChild($moder,$editNews);

        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = 'Администратор';
        $auth->add($admin);
        $auth->addChild($admin,$moder);
        $auth->addChild($admin,$userEdit);
        
        //Заведение администратора
        if ($adminUser=User::findByUsername("admin")){
            $adminUser->delete();
        }
        $adminUser = new User();
        $adminUser->setUserName("admin");
        $adminUser->setPassword("admin");
        $adminUser->activateUser();
        $adminUser->insert(false);
        Yii::$app->authManager->assign($admin, $adminUser->getId());
        
        //Заведение пользователя
        if ($userUser=User::findByUsername("user")){
            $userUser->delete();
        }
        $userUser = new User();
        $userUser->setUserName("user");
        $userUser->setPassword("user");
        $userUser->activateUser();
        $userUser->insert(false);
        Yii::$app->authManager->assign($user, $userUser->getId());
        
    }
}