<?php

/**
 * @author: rizqi@docotel.com
 * @description: default dashboard penjamin asuransi
**/

namespace Doco\penjaminasuransi\controllers;

use Yii;
use app\components\DocoController;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/penjaminasuransi/dashboard';
    protected $params;

    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }
}