<?php

/****
 * * @author: Budi
 * ? @email: budi@sirs.co.id 
 *  ! Powered by Sirs
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\web\Response;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\ExportExcelEngine;

class LaporanDataJurnalController extends DocoController
{
	protected $allowAction = ['*'];
  	protected $_title = 'Laporan Data Jurnal';
  	protected $_module = 'kasir/laporapn-data-jurnal/';
  	protected $_restKasir;
	public function init()
  	{
		parent::init();
	 	$this->_restKasir = Yii::$app->docoRest->kasir;
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
		$title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
		return $this->render('index', get_defined_vars());
	}

	public function actionShowPopupExcel()
    {
        $title = $this->_title;
        $request = Yii::$app->request;

        $yiiRestfulParams = [
            'range_tanggal' => $request->get('range_tanggal'),
        ];

		$filename = $title . ' ' . $yiiRestfulParams['range_tanggal'];

        $randString = (new ExportExcelEngine)->generateRandomString($yiiRestfulParams);

        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        $rest = $this->_restKasir;
        $backendUrl = "laporan-data-jurnal/export-excel";

        return (new ExportExcelEngine)->processSync($randString, $rest, $backendUrl);
    }

    public function actionDownloadExcel($key, $filename = null)
    {
		$filename = is_null($filename) ? $this->_title : $filename;
        return (new ExportExcelEngine)->downloadFile($key, $filename);
    }
}
