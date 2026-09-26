<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 14:24:29
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 11:29:18
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rm\models\PemesananObatAlkesForm;
use app\modules\rm\models\FilterPemesananObatForm;

class PemesananObatAlkesController extends DocoController
{
	protected $_title = "Rm :: Pemesanan Obat Alkes";
    protected $_module = 'rm/pemesanan-obat-alkes/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionCreate()
    {
    	$model = new PemesananObatAlkesForm;
        return $this->render('index', get_defined_vars());
    }
    public function actionUpdate()
    {
    	$model = new PemesananObatAlkesForm;
        return $this->render('form', get_defined_vars());	
    }
    public function actionInformasi()
    {
        $module = 'rm/pemesanan-obat-alkes/';
        $model = new FilterPemesananObatForm;
        return $this->render('informasi', get_defined_vars());
    }
    public function actionLaporan()
    {
        $module = 'rm/pemesanan-obat-alkes/';
        $model = new FilterPemesananObatForm;
        return $this->render('laporan', get_defined_vars());
    }
    public function actionDetail()
    {
        return $this->renderPartial('detail-pemesanan', get_defined_vars());
    }

    //popup list
    public function actionListObat(){
    	return $this->renderPartial('list-obat', get_defined_vars());	
    }
    public function actionListPemesanan(){
        return $this->renderPartial('list-pemesanan', get_defined_vars());  
    }
}
