<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard fisioterapi
**/

namespace Doco\fisioterapi\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/fisioterapi';
    protected $supersetKey = 'fisioterapi';
}