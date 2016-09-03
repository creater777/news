<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents index page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class IndexPage extends BasePage
{
    public $route = 'site/index';
}
