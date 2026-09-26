<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-19 15:59:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 10:35:21
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\kasir\models\PelayananPasienForm;

class PelayananPasienController extends DocoController
{
	protected $_title = "Pasien Pelayanan";
    protected $_module = 'kasir/tra-pelayanan-pasien/';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
    	$model = new PelayananPasienForm;
        return $this->render('index', get_defined_vars());
    }
}