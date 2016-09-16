<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use app\models\User;

class UserTest extends TestCase
{
    use Specify;

    public function testFindByUsername(){
        $user = User::findByUsername('user');
        $this->specify('Пользователь должен найтись', function () use ($user) {
            expect('Пользователь должен найтись', isset($user))->true();
            expect('Имя пользователя дожно совпадать', $user->username == 'user')->true();
        });
    }

    public function testFindByUsernameNoExist(){
        $user = User::findByUsername('user_not_exist');
        $this->specify('Пользователь не должен найтись', function () use ($user) {
            expect('Пользователь не должен найтись', !isset($user))->true();
        });
    }

    public function testFindByEmail(){
        $user = User::findByEmail('user@news.ru');
        $this->specify('Пользователь должен найтись', function () use ($user) {
            expect('Пользователь должен найтись', isset($user))->true();
            expect('Имя пользователя дожно совпадать', $user->username == 'user')->true();
        });
    }

    public function testFindByEmailNoExist(){
        $user = User::findByEmail('user_not_exist@news.ru');
        $this->specify('Пользователь не должен найтись', function () use ($user) {
            expect('Пользователь не должен найтись', !isset($user))->true();
        });
    }

    public function testFindIdentity(){
        $user = User::findIdentity(1);
        $this->specify('Пользователь должен найтись', function () use ($user) {
            expect('Пользователь должен найтись', isset($user))->true();
        });
    }

    public function testFindIdentityNoExist(){
        $user = User::findIdentity(100);
        $this->specify('Пользователь не должен найтись', function () use ($user) {
            expect('Пользователь не должен найтись', !isset($user))->true();
        });
    }    


    public function testFindByAuthKey(){
        $u = User::findByUsername('user');
        $u->generateAuthKey(0);
        $u->save(false);
        $user = User::findByAuthKey($u->getAuthKey());
        $this->specify('Пользователь должен найтись', function () use ($user) {
            expect('Пользователь должен найтись', isset($user))->true();
            expect('Имя пользователя дожно совпадать', $user->username == 'user')->true();
        });
    }

    public function testFindByAuthKeyNoExist(){
        $user = User::findByAuthKey('not_exist_authkey');
        $this->specify('Пользователь не должен найтись', function () use ($user) {
            expect('Пользователь не должен найтись', !isset($user))->true();
        });
    }       
    
    public function testFindAllActual(){
        $u = User::findByUsername('user');
        $u->activateUser();
        $u->save(false);
        $users = User::findAllActual();
        $this->specify('Пользователи должен найтись', function () use ($users) {
            expect('Должны найтись', is_array($users))->true();
            expect('Должны найтись больше одного', count($users) > 0)->true();
        });
    }

    public function testGenerate(){
        if ($u = User::findByUsername('generateTest')){
            $u->delete();
        }
        $user = new User();
        $user->username='generateTest';
        $user->email='generateTest@news.ru';
        $authKey = $user->generateAuthKey(5);
        $user->setPassword('pass');
        $user->save(false);
        $u = User::findByUsername('generateTest');
        $this->specify('Проверка генерации пароля и ключа', function () use ($authKey, $u) {
            expect('Должен найтись', isset($u))->true();
            expect('Ключ должен быть валидным', $u->validateAuthKey($authKey))->true();
            expect('Пароль должен быть валидным', $u->validatePassword('pass'))->true();
            expect('Ключ не должен быть валидным', $u->validateAuthKey('not_valid_key'))->false();
            expect('Пароль не должен быть валидным', $u->validatePassword('not_valid_pass'))->false();
            sleep(6);
            expect('Ключ не должен быть валидным', $u->validateAuthKey($authKey))->false();
        });
    }

    public function testRole(){
        $u = User::findByUsername('user');
        $u->role = User::ROLE_USER;
        $u->role = User::ROLE_ADMIN;
        $roles = Yii::$app->authManager->getRolesByUser($u->getId());
        $this->specify('Проверка генерации пароля и ключа', function () use ($roles) {
            expect('Должна быть одна роль', sizeof($roles) == 1)->true();
            expect('Должна быть роль admin', isset($roles[User::ROLE_ADMIN]))->true();
        });
        $u->role = User::ROLE_USER;
        $r = Yii::$app->authManager->getRolesByUser($u->getId());
        expect('Должна быть роль user', isset($r[User::ROLE_USER]))->true();
    }
    
    public function testUserSave(){
        $user = new User();
        $user->username='generateTest';
        $user->email='generateTest@news.ru';
        $user->save(false);
        $user->role = User::ROLE_USER;
        $this->specify('Проверка генерации полей дат', function () use ($user) {
            expect('createat должна установиться', $user->createat != 0)->true();
            expect('updateat должна установиться', $user->updateat != 0)->true();
        });
        $id = $user->getId();
        $user->delete();
        $roles = Yii::$app->authManager->getRolesByUser($id);
        expect('Ролей у пользователя быть не должно', isset($roles[User::ROLE_USER]))->false();
    }
}
