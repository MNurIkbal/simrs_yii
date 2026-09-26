<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 11:54:56
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 17:00:00
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
use app\modules\rm\models\DefaultFilterForm;
use app\modules\rm\models\PenerimaanBarangForm;

class MutasiBarangController extends DocoController
{
	protected $_title = "Rm :: Informasi Mutasi Barang";
    protected $_module = 'rm/mutasi-barang/';
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

    public function actionIndex()
    {
    	$model = new DefaultFilterForm;
    	$module = 'rm/mutasi-barang/';
    	return $this->render('index', get_defined_vars());
    }
    public function actionPenerimaanBarang()
    {
    	$model = new PenerimaanBarangForm;
    	return $this->render('penerimaan-barang', get_defined_vars());
    }

    public function actionDetail()
    {
    	return $this->renderPartial('detail-mutasi', get_defined_vars());
    }
    public function actionListNomor()
    {
    	return $this->renderPartial('list-nomor', get_defined_vars());
    }
    public function actionListPegawai()
    {
    	return $this->renderPartial('list-pegawai', get_defined_vars());	
    }
}
