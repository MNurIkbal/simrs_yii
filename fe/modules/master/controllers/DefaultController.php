<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard master
**/

namespace Doco\master\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/master';
    protected $supersetKey = 'master';
}