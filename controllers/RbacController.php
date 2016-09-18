<?php
namespace app\controllers;
 
use Yii;
use yii\console\Controller;
use app\models\User;
use app\controllers\UserRule;
 
/**
 * Инициализация доступов
 * Изначально создается пользователи:
 * - admin с паролем admin с правами администратора
 * - moder с паролем moder с правами модератора
 * - user с паролем user с правами пользователя
 * запускается из консоли php yii rbac/init
 * @throws \Exception
 */
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
        
        //Права редактировать профиль
        $userRule = new UserRule();
        $auth->add($userRule);
        $editProfile = $auth->createPermission(User::PERMISSION_EDITPROFILE);
        $editProfile->description = 'Запрет на редактирование чужего профиля';
        $editProfile->ruleName = $userRule->name;
        $auth->add($editProfile);

        //Права редактора
        $editNews = $auth->createPermission(User::PERMISSION_EDITNEWS);
        $editNews->description = 'Редактирование новостей';
        $auth->add($editNews);

        //Права админа
        $userEdit = $auth->createPermission(User::PERMISSION_USEREDIT);
        $userEdit->description = 'Редактирование чужих профилей';
        $auth->add($userEdit);

        //Добавляем роли
        $user = $auth->createRole(User::ROLE_USER);
        $user->description = User::getRoleList()[User::ROLE_USER];
        $auth->add($user);
        $auth->addChild($user,$editProfile);
        $auth->addChild($user,$viewNews);
        
        $moder = $auth->createRole(User::ROLE_MODERATOR);
        $moder->description = User::getRoleList()[User::ROLE_MODERATOR];
        $auth->add($moder);
        $auth->addChild($moder,$user);
        $auth->addChild($moder,$editNews);

        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = User::getRoleList()[User::ROLE_ADMIN];
        $auth->add($admin);
        $auth->addChild($admin,$viewNews);
        $auth->addChild($admin,$editNews);
        $auth->addChild($admin,$userEdit);
        
        //Заведение администратора
        if ($adminUser=User::findByUsername("admin")){
            $adminUser->delete();
        }
        $adminUser = new User();
        $adminUser->username = "admin";
        $adminUser->email = "admin@news.ru";
        $adminUser->setPassword("admin");
        $adminUser->activateUser();
        $adminUser->setRole(User::ROLE_ADMIN);
        if (!$adminUser->insert(false)){
            throw new \Exception("Unable to add user admin");
        }
        
        //Заведение пользователя
        if ($userUser=User::findByUsername("user")){
            $userUser->delete();
        }
        $userUser = new User();
        $userUser->username="user";
        $userUser->email = "user@news.ru";
        $userUser->setPassword("user");
        $userUser->activateUser();
        $userUser->setRole(User::ROLE_USER);
        if (!$userUser->insert(false)){
            throw new \Exception("Unable to add user user");
        }
 
        //Заведение модератора
        if ($moderUser=User::findByUsername("moder")){
            $moderUser->delete();
        }
        $moderUser = new User();
        $moderUser->username = "moder";
        $moderUser->email = "moder@news.ru";
        $moderUser->setPassword("moder");
        $moderUser->activateUser();
        $moderUser->setRole(User::ROLE_MODERATOR);
        if (!$moderUser->insert(false)){
            throw new \Exception("Unable to add user moder");
        }
    }
}