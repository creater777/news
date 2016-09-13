<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace tests\codeception\unit\models;

/**
 * Description of EmailTestCase
 *
 * @author Дарья
 */
class EmailTestCase extends \PHPUnit_Framework_TestCase{

    private $mailcatcher;

    public function setUp()
    {
        $this->mailcatcher = new \Guzzle\Http\Client('http://127.0.0.1:1080');

        // clean emails between tests
        $this->cleanMessages();
    }

    // api calls
    public function cleanMessages()
    {
        $this->mailcatcher->delete('/messages')->send();
    }    
}
