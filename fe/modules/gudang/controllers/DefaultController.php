<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: default dashboard gudang
**/

namespace Doco\gudang\controllers;

class DefaultController extends \app\components\DashboardController
{
    protected $_title = "Dashboard";
    protected $_module = '/gudang';
    protected $supersetKey = 'gudang'; // bisa dihapus kalo sama seperti nama modul
}