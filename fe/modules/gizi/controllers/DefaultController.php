<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard gizi
**/

namespace Doco\gizi\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/gizi';
    protected $supersetKey = 'gizi';
}