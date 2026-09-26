<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\gudang;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoDistribusiObatAlkesView;
use app\modules\v1\models\DetailPemesananObatAlkes;

class CetakDetailPemesananObatDotmatrix extends \Doco\processes\CetakDetailPemesananObatProcess
{
	protected function processFlow()
    {
    	$id = Yii::$app->request->get('id');
    	$id = DocoHelpers::decrypt($id);
        $header = InfoDistribusiObatAlkesView::find()
            ->where(["pesanobatalkes_id" => $id])->asArray()->one();
        $detail = DetailPemesananObatAlkes::find()
            ->where(["pesanobatalkes_id" => $id])->orderBy(['obatalkes_nama'=>SORT_ASC])->asArray()->all();

        $ruangan = $header['ruangan_tujuan'];

        $print = new DocoPrint();
        $print->attributes = [
            "#ruangan#" => $ruangan,
            "#tanggal_pemesanan#" => date("d-M-Y", strtotime($header['tglpemesanan'])),
            "#tanggal_kirim#" => empty($header['tglmutasioa']) ? "-" :
                date("d-M-Y", strtotime($header['tglmutasioa'])),
            "#status_distribusi#" => $header['status_distribusi'],
            "#instalasi_ruangan_pemesan#" => $header['instalasi_pemesan']." - ".$header['ruangan_pemesan'],
            "#instalasi_ruangan_asal#" => $header['instalasi_tujuan']." - ".$header['ruangan_tujuan'],
            "#no_pemesanan#" => $header['nopemesanan'],
            "#no_pengiriman#" => empty($header['nomutasioa']) ? "-" : $header['nomutasioa'],
            "#pemesan#" => $header['pemesan'],
            "#pengirim#" => empty($header['pengirim']) ? "-" : $header['pengirim'],
            "#tabel_detail_obat#" => Yii::$app->controller->renderPartial('detail_dotmatrix', [
                'detail' => $detail
            ]),
        ];

        $print->Output();
    }
}