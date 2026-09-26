<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard radiologi
**/

namespace Doco\radiologi\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/radiologi';
    protected $supersetKey = 'radiologi';
}