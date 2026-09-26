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

class LapDiskonPayerController extends DocoController
{
	protected $allowAction = ['*'];
  	protected $_title = 'Laporan Diskon Payer';
  	protected $_module = 'kasir/lap-diskon-payer/';
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
		$response = $this->getCaraBayar();
		$caraBayar = !empty($response) ? json_encode($response) : [];
		return $this->render('index', get_defined_vars());
	}

	public function actionGetData()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$payload = DocoDatatableHelper::advancedFilterParam();
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'lap-diskon-payer/index',
			'payload' => [
					'query' => $payload,
			]
		]);
		if(!empty($response['data'])) {
			foreach ($response['data'] as $key => $value) {
				$value['billing_total'] = is_null($value['billing_total']) ? 0 : $value['billing_total'];
				$value['remarks'] = is_null($value['remarks']) ? "-" : $value['remarks'];
				$value['diskon_type'] = is_null($value['diskon_type']) ? "-" : $value['diskon_type'];
				$value['discount_total'] = is_null($value['discount_total']) ? 0 : $value['discount_total'];
			}
		}
		$response['recordsTotal'] = isset($response['_meta']) ? $response['_meta']['totalCount'] : 0;
		$response['recordsFiltered'] = isset($response['_meta']) ? $response['_meta']['totalCount'] : 0;
		return $response;
	}

	private function getCaraBayar()
	{
		return $this->guzzleExec($this->_restKasir, [
			'url' => 'lap-diskon-payer/get-cara-bayar',
			'method' => 'get',
		]);
	}

	public function actionGetPenjamin()
	{
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'lap-diskon-payer/get-penjamin',
			'payload' => [
				'query' => [
					'payload' => Yii::$app->request->get('payload', []),
				]
			]
		]);
		return $this->responseJson(200, 'Data berhasil diambil!', $response);
	}

	public function actionExportExcel()
	{
		$payload = DocoDatatableHelper::advancedFilterParam();
		$path = Yii::getAlias("@download") . "/laporan-diskon.xlsx";
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'lap-diskon-payer/export-excel',
			'payload' => [
				'query' => $payload,
				'save_to' => $path,
			]
		]);
		return DocoHelpers::downloadFile($path,true);
	}
}
