<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard bankdarah
**/

namespace Doco\bankdarah\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/bankdarah';
    protected $supersetKey = 'bankdarah';
}