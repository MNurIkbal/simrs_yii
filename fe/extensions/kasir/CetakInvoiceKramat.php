<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;
use app\components\DocoHelpers;

class CetakInvoiceKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      $request = Yii::$app->request;
		$id = $request->get('id');
		$invoice_id = $request->get('invoice_id');
		if(!is_numeric($id)) {
		   $id = DocoHelpers::decrypt($id);
		}
		if(!is_numeric($invoice_id)) {
		   $invoice_id = DocoHelpers::decrypt($invoice_id);
		}
		$kelompok = $request->get('kelompok', null);
		$jenis_invoice = $request->get('jenis_invoice', 1);
		$path = Yii::getAlias("@download")."/cetak-invoice.pdf";
		$userIdentity = Yii::$app->session->get('user_identity');
		$restKasir = Yii::$app->docoRest->kasir;
		$response = $restKasir->get('tagihan-pasien/invoice', [
			'query' => [
				'id' => $id,
				'invoice_id' => $invoice_id,
				'nama_pegawai' => $userIdentity['nama_pegawai'],
				'kelompok' => $kelompok,
				'jenis_invoice' => $jenis_invoice
			],
			'save_to' => $path
		]);
		$body = json_decode($response->getBody(), true);
		return DocoHelpers::previewPdf($path);
	}
}
