<?php
use app\models\User;
use Codeception\Util\HttpCode;

$I = new FunctionalTester($scenario);
$user = User::findByUsername('user');
$moder = User::findByUsername('moder');
$I->wantTo('ensure that access control is works');

$I->amLoggedInAs($user);
$I->amOnPage(Yii::$app->homeUrl);
$I->seeLink('Профиль');
$I->click('Профиль');

//Управление новостями
$I->amOnPage(['site/create']);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['site/update', 'id' => 8]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['site/view', 'id' => 8]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['site/delete', 'id' => 8]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);

//Свой профиль
$I->amOnPage(['users/update', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['users/view', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['users/create']);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['users/delete', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);

//Чужой профиль
$I->amOnPage(['users/update', 'id' => $moder->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['users/view', 'id' => $moder->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
$I->amOnPage(['users/delete', 'id' => $moder->getId()]);
$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
