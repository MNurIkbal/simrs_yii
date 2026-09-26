<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard informasi
**/

namespace Doco\informasi\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/informasi';
    protected $supersetKey = 'informasi';
}