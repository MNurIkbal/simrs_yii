<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard ambulan
**/

namespace Doco\ambulan\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/ambulan';
    protected $supersetKey = 'ambulan';
}