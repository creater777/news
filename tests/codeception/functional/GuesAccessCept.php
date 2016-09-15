<?php
use app\models\User;
use Codeception\Util\HttpCode;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that access guest is works');
$user = User::findByUsername('user');

//Регистрация
$I->amOnPage(['access/login']);
$I->see('Вход', 'h1');
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['access/index']);
$I->see('Вход', 'h1');
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['access/register']);
$I->see('Регистрация', 'h1');
$I->amOnPage(['access/confirmemail']);
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
$I->amOnPage(['access/confirmemail', 'authKey' => 'missing_key']);
$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
$I->amOnPage(['access/resendemail']);
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
$I->amOnPage(['access/resendemail', 'authKey' => 'missing_key']);
$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
$I->amOnPage(['access/captcha']);
$I->seeResponseCodeIs(HttpCode::OK);;

//Управление новостями
$I->amOnPage(['site/create']);
$I->see('Вход', 'h1');
$I->amOnPage(['site/update', 'id' => 3]);
$I->see('Вход', 'h1');
$I->amOnPage(['site/view', 'id' => 3]);
$I->see('Вход', 'h1');
$I->amOnPage(['site/delete', 'id' => 3]);
$I->see('Вход', 'h1');

//Управление пользователями
$I->amOnPage(['users/create']);
$I->see('Вход', 'h1');
$I->amOnPage(['users/update', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['users/view', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['users/delete', 'id' => $user->getId()]);
$I->see('Вход', 'h1');
