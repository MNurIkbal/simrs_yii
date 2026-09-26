<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard igd
**/

namespace Doco\igd\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/igd';
    protected $supersetKey = 'igd';
}