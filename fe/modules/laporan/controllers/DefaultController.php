<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard laporan
**/

namespace Doco\laporan\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/laporan';
    protected $supersetKey = 'laporan';
}