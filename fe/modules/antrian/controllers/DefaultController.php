<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard antrian
**/

namespace Doco\antrian\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/antrian';
    protected $supersetKey = 'antrian';
}