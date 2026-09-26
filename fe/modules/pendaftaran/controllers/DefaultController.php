<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard pendaftaran
**/

namespace Doco\pendaftaran\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/pendaftaran';
    protected $supersetKey = 'pendaftaran';
}