<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use app\models\User;
use app\models\ConfirmForm;
use Codeception\Specify;

class ConfirmFormTest extends TestCase
{
    use Specify;

    private $_user;
    
    protected function setUp() {
        parent::setUp();
        $this->_user = User::findByUsername('user');
        $this->_user->active = 0;
        Yii::$app->user->logout();
    }

    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testWrongReplay()
    {
        $model = new ConfirmForm([
            'password' => 'user',
            'password2' => 'wrong_reply',
            'verifyCode' => 'testme',
        ]);
        $this->_user->active = 0;
        $this->specify('Пользователь не должен активироваться', function () use ($model) {
            expect('Активация не должна пройти', $model->activate($this->_user))->false();
            expect('Пользователь должен остаться не активным', $this->_user->isActive())->false();
            expect('Авторизация не должна пройти', Yii::$app->user->getIsGuest())->true();
        });
    }

    public function testTimeExpired()
    {
        $model = new ConfirmForm([
            'password' => 'user',
            'password2' => 'user',
            'verifyCode' => 'testme',
        ]);
        $this->_user->updateat = $this->_user->updateat - Yii::$app->params['authKeyExpired'];
        $this->_user->active = 0;
        $this->specify('Пользователь не должен активироваться', function () use ($model) {
            expect('Активация не должна пройти', $model->activate($this->_user))->false();
            expect('Пользователь должен остаться не активным', $this->_user->isActive())->false();
            expect('Авторизация не должна пройти', Yii::$app->user->getIsGuest())->true();
        });
    }
    
    public function testNoExistUser()
    {
        $model = new ConfirmForm([
            'password' => 'user',
            'password2' => 'user',
            'verifyCode' => 'testme',
        ]);
        $this->specify('Пользователь не должен активироваться', function () use ($model) {
            expect('Активация не должна пройти', $model->activate(User::findByAuthKey('no_exist_authkey')))->false();
            expect('Авторизация не должна пройти', Yii::$app->user->getIsGuest())->true();
        });
    }
    
    public function testConfirmOK()
    {
        $model = new ConfirmForm([
            'password' => 'user',
            'password2' => 'user',
            'verifyCode' => 'testme',
        ]);
        $this->_user->active = 0;
        $this->_user->generateAuthKey(Yii::$app->params['authKeyExpired']);
        $this->specify('Пользователь должен активироваться', function () use ($model) {
            expect('Активация должна пройти', $model->activate($this->_user))->true();
            expect('Пользователь должен стать активным', $this->_user->isActive())->true();
            expect('Должна пройти авторизация', Yii::$app->user->getIsGuest())->false();
        });
    }    
}
