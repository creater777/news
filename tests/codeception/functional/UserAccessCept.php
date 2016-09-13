<?php
use app\models\User;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that access control is works');
$user = User::findByUsername('user');
$I->amLoggedInAs($user);
$I->amOnPage(Yii::$app->homeUrl);
$I->seeLink('Профиль');
