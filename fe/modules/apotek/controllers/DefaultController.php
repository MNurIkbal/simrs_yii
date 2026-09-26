<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard apotek
**/

namespace Doco\apotek\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/apotek';
    protected $supersetKey = 'apotek';
}