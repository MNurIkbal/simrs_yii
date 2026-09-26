<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard jenazah
**/

namespace Doco\jenazah\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/jenazah';
    protected $supersetKey = 'jenazah';
}