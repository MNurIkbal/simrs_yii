<?php
namespace Doco\rm\controllers;
/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-17 09:37:56
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-19 13:38:47
 */

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rm\models\FilterStokBarangForm;
class StokBarangController extends DocoController
{

	protected $_title = "Informasi :: Stok Barang";
    protected $_module = 'master/inf-stok-barang/';
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

    public function actionInformasi()
    {     
        // Init           
        $model = new FilterStokBarangForm;
        return $this->render('informasi', get_defined_vars());
    }

    public function actionLaporan()
    {     
        // Init           
        $model = new FilterStokBarangForm;
        return $this->render('laporan', get_defined_vars());
    }
    public function actionListBarang(){
    	return $this->renderPartial('list-barang',get_defined_vars());
    }

}
