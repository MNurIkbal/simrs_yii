<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard penatajasa
**/

namespace Doco\penatajasa\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/penatajasa';
    protected $supersetKey = 'penatajasa';
}