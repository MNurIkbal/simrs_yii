<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard kasir
**/

namespace Doco\kasir\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/kasir';
    protected $supersetKey = 'kasir';
}