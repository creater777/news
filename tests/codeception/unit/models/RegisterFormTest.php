<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use app\models\RegisterForm;
use Codeception\Specify;

class RegisterFormTest extends TestCase
{
    use Specify;

    public function testRegisterNoUser()
    {
        $model = new RegisterForm([
            'username' => '',
            'email' => '',
            'verifyCode' => 'testme',
        ]);

        $this->specify('user name and email not be empty', function () use ($model) {
            expect('model should not register user', $model->register())->false();
            expect('error message on user name should be set', $model->errors)->hasKey('username');
            expect('error message on email should be set', $model->errors)->hasKey('email');
        });
    }

    public function testRegisterExistingUser()
    {
        $model = new RegisterForm([
            'username' => 'user',
            'email' => 'user@news.ru',
            'verifyCode' => 'testme',
        ]);

        $this->specify('user should not be able to register with existing username', function () use ($model) {
            expect('model should not register user', $model->register())->false();
            expect('not be set a existing username', $model->errors)->hasKey('username');
        });
    }

    public function testRegisterExistingEmail()
    {
        $model = new RegisterForm([
            'username' => 'user_test',
            'email' => 'user@news.ru',
            'verifyCode' => 'testme',
        ]);

        $this->specify('user should not be able to register with existing email', function () use ($model) {
            expect('model should not register user', $model->register())->false();
            expect('not be set a existing email', $model->errors)->hasKey('email');
        });
    }

    public function testRegisterOk()
    {
        $model = new RegisterForm([
            'username' => 'user_test',
            'email' => 'user_test@news.ru',
            'verifyCode' => 'testme',
        ]);

        $this->specify('user should be able to register', function () use ($model) {
            expect('model should register user', $model->register())->true();
        });
    }
}
