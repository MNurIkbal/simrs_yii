<?php

/**
 * setup config application
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace Doco\sysadmin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;


class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/sysadmin/dashboard';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }
    
}