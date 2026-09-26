<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard dcms
**/

namespace Doco\dcms\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/dcms';
    protected $supersetKey = 'dcms';
}