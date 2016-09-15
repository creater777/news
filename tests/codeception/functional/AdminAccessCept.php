<?php
use app\models\User;
use Codeception\Util\HttpCode;

$I = new FunctionalTester($scenario);
$user = User::findByUsername('user');
$admin = User::findByUsername('admin');
$I->wantTo('ensure that access control is works');

$I->amLoggedInAs($admin);
$I->amOnPage(Yii::$app->homeUrl);
$I->seeLink('Профиль');
$I->click('Профиль');

//Управление новостями
$I->amOnPage(['site/create']);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['site/update', 'id' => 1]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['site/view', 'id' => 1]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['site/delete', 'id' => 1]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['site/view', 'id' => 1]);
$I->seeResponseCodeIs(HttpCode::NOT_FOUND);

//Свой профиль
$I->amOnPage(['users/update', 'id' => $admin->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['users/view', 'id' => $admin->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);

//Чужой профиль
$I->amOnPage(['users/create']);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['users/update', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->amOnPage(['users/view', 'id' => $user->getId()]);
$I->seeResponseCodeIs(HttpCode::OK);
