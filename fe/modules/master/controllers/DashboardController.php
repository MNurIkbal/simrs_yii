<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/master/dashboard';

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

    public function actionLayarKetersediaanKamar()
    {
        $state = false;
        $request = Yii::$app->request;
        $judulLayarAntrian = Yii::t('fe', 'Ketersediaan Kamar');
        $jenisLayarAntrian = Yii::t('fe', 'Ketersediaan Kamar');

        $getRequest = $this->guzzleExec($this->_restMaster,
        [
            'url' => 'dashboard-kamar/data-layar-ketersediaan-kamar',
        ]);
        
        $listData = ArrayHelper::getValue($getRequest, 'dataKamar');
        $konfig_layar = ArrayHelper::getValue($getRequest, 'konfig-layar');
        return $this->render('layarketersediaankamar', compact('listData', 'judulLayarAntrian', 'jenisLayarAntrian', 'konfig_layar'));
    }
}