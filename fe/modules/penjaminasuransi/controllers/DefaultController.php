<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard penjaminasuransi
**/

namespace Doco\penjaminasuransi\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/penjaminasuransi';
    protected $supersetKey = 'penjaminasuransi';
}