<?php
namespace Doco\kasir\controllers;

use Yii;
use app\components\DocoController;
use app\modules\kasir\models\LaporanKasirForm;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class LaporanKasirController extends DocoController
{
    protected $_restKasir;
	protected $_module = '/rm/laporan-kasir/';
    protected $_title;
    protected $allowAction = ['*'];
    protected $type = null;

	public function init()
    {
        parent::init();
        $this->type = empty(Yii::$app->getRequest()->getQueryParam('key')) ? "laporan_kasir" : Yii::$app->getRequest()->getQueryParam('key');
        $this->_title = Yii::t('fe', 'Laporan '.ucfirst(explode("_",$this->type)[1]));
	 	$this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $model = new LaporanKasirForm;
        $listLaporan = $this->guzzleExec($this->_restKasir, [
            'url' => "laporan-kasir/list-konfig-laporan?key_laporan=".$this->type,
            'payload' => [],
        ]);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {

    }

    public function actionShowPopupExcel()
    {
        $request = Yii::$app->request;
        $title = 'Download Excel '. $request->get('jenis_laporan');
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = [
            'range_tanggal' => $request->get('range_tanggal'),
            'jenis_laporan' => $request->get('jenis_laporan'),
            'instalasi' => $request->get('instalasi'),
            'kasir' => $request->get('kasir'),
            'penjamin' => $request->get('penjamin')
        ];
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "laporan-kasir/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = $request->get('jenis_laporan') .' '. $request->get('range_tanggal').'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $this->_restKasir->get('laporan-kasir/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }
}
