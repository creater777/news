<?php

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Новости', 'h1');
$I->seeLink('Регистрация');
$I->click('Регистрация');
$I->see('Регистрация', 'h1');
//$I->canSeeCurrentUrlEquals(Yii::$app->getUrlManager()->createAbsoluteUrl(['access/register']));
