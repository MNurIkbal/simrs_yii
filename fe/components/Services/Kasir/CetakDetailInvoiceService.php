<?php

namespace app\components\Services\Kasir;

use Yii;
use app\components\Traits\ControllerHelperTrait;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoHelpers;

class CetakDetailInvoiceService extends BaseCurrentService
{
    public function execute()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $invoice_id = $request->get('pembayaran_id', null);
        $kelompok = $request->get('kelompok', null);
        $status = $request->get('status', 1);
        $tipe_pasien = $request->get('tipe_pasien', null);
        if ($request->get('invoice_id')) {
            $invoice_id = $request->get('invoice_id', null);
        }
        $jenis_invoice = $request->get('jenis_invoice', 1);
        if (!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if (!is_numeric($invoice_id)) {
            $invoice_id = DocoHelpers::decrypt($invoice_id);
        }
        if (!is_numeric($jenis_invoice)) {
            $jenis_invoice = DocoHelpers::decrypt($jenis_invoice);
        }
        $path = Yii::getAlias("@download") . "/cetak-detail-invoice.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->restKasir->get('tagihan-pasien/detail-invoice', [
            'query' => [
                'id' => $id,
                'invoice_id' => $invoice_id,
                'nama_pegawai' => $userIdentity['nama_pegawai'],
                'jenis_invoice' => $jenis_invoice,
                'kelompok' => $kelompok,
                'status' => $status,
                'tipe_pasien' => $tipe_pasien,
                'tipe' => $request->get('tipe'),
                'invoice_type' => $request->get('invoice_type'),
                'kelompok' => $request->get('kelompok'),
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path, $response);
    }
}
