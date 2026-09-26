<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard pengadaan
**/

namespace Doco\pengadaan\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/pengadaan';
    protected $supersetKey = 'pengadaan';
}