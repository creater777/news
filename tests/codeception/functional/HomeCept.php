<?php

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Новости');
$I->seeLink('Регистрация');
$I->click('Регистрация');
$I->see('Регистрация');
