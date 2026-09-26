<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard laboratorium
**/

namespace Doco\laboratorium\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/laboratorium';
    protected $supersetKey = 'laboratorium';
}