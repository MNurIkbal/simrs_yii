<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/fisioterapi/dashboard';
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