<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\Response;
use app\components\DocoConstants;

class CetakDetailInvoiceKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      $title = 'Jenis Detail Invoice';
		$request = Yii::$app->request;
		$get = $request->get();
		$model = new \yii\base\DynamicModel(['jenis_invoice', 'id', 'invoice_id', 'penjamin_id', 'nama_pegawai', 'pembayaranpelayanan_id']);
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$model
			->addRule(['jenis_invoice', 'id', 'invoice_id', 'penjamin_id', 'pembayaranpelayanan_id'], 'integer')
			->addRule(['jenis_invoice'], 'required');
		
		$model->attributes = $get;
		$model->jenis_invoice = 1;
		$pathJs = '../js/invoice.js';
		if($model->load($request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$pembayaran_id = isset($get['pembayaran_id']) ? $get['pembayaran_id'] : null;
			if(!is_numeric($pembayaran_id)) {
					$pembayaran_id = DocoHelpers::decrypt($pembayaran_id);
			}
			if(!is_numeric($model->id)) {
					$model->id = DocoHelpers::decrypt($model->id);
			}
			if(!empty($model->penjamin_id) && !is_numeric($model->penjamin_id)) {
					$model->penjamin_id = null;
			}
			$userIdentity = Yii::$app->session->get('user_identity');
			$model->id = $model->id;
			$model->invoice_id = $pembayaran_id;
			$model->nama_pegawai = $userIdentity['nama_pegawai'];
			if(!$model->validate()) {
				$errors = DocoHelpers::parseError($model->errors, $formName);
				return DocoHelpers::responseTemplate(422, 'Error', $errors);
			}
			$restKasir = Yii::$app->docoRest->kasir;
			return $controller->guzzleExec($restKasir, [
				'url' => 'tagihan-pasien/cetak-detail-invoice',
				'type' => 'POST',
				'payload' => [
					'form_params' => $model->attributes,
				]
			]);
		}
		else {
			$jenis_invoice = [1 => 'Lengkap', 2 => 'Pasien', 3 => 'Penjamin'];
			$groupUmum = DocoConstants::GROUP_UMUM;
			$pembayaran_id = $request->get('pembayaran_id', null);
			$pendaftaran_id = $request->get('id', null);
			$penjamin_id = $request->get('penjamin_id', null);
			$groupcarabayar_id = $request->get('groupcarabayar_id', null);
			if(!empty($pembayaran_id) && !is_numeric($pembayaran_id)) {
				$pembayaran_id = DocoHelpers::decrypt($pembayaran_id);
			}
			if(!empty($pendaftaran_id) && !is_numeric($pendaftaran_id)) {
				$pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
			}
			if(!empty($penjamin_id) && !is_numeric($penjamin_id)) {
				$penjamin_id = DocoHelpers::decrypt($penjamin_id);
			}
			if(!empty($groupcarabayar_id) && !is_numeric($groupcarabayar_id)) {
				$groupcarabayar_id = DocoHelpers::decrypt($groupcarabayar_id);
			}
			$listPenjamin = $this->getPenjamin($controller, $pembayaran_id);
			$listPenjamin = !empty($listPenjamin) ? $listPenjamin : [];
			if(empty($groupcarabayar_id)) {
				$groupcarabayar_id = isset($listPenjamin[0]['groupcarabayar_id']) ? $listPenjamin[0]['groupcarabayar_id'] : null;
			}
			if(!empty($listPenjamin)) {
				$listPenjamin = ArrayHelper::map($listPenjamin, 'penjamin_id', 'penjamin_nama');
			}
			$is_bgprocess = $request->get('is_bgprocess', true);
			$is_invoice = $request->get('is_invoice', false);
			if($is_invoice){
				$title = 'Jenis Invoice';
			}

			$userIdentity = Yii::$app->session->get('user_identity');
			$pegawaiId = ArrayHelper::getValue($userIdentity, 'id_pegawai');
			return $controller->renderAjax('partial/_form_detail_invoice', get_defined_vars());
		}
	}

	private function getPenjamin($controller, $pembayaran_id)
	{
		$restKasir = Yii::$app->docoRest->kasir;
		return $controller->guzzleExec($restKasir, [
			'url' => 'inf-pasien-sudah-bayar/get-penjamin',
			'payload' => [
				'query' => [
					'pembayaran_id' => $pembayaran_id
				]
			]
		]);
	}
}
