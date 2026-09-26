<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class DetailProduksiAction extends Action {
    public function run($id) {
        $title  = \Yii::t('fe', 'Detail Produksi Obat');
        $id_produksi = $id;
        
        $requests = [
            'get_info_produksi' => [
                'url' => 'inf-produksi-obat/get-info-produksi',
                'params' => [
                    'id' => DocoHelpers::decrypt($id_produksi),
                ]
            ],
            'cek_ketersediaan' => [
                'url' => 'inf-produksi-obat/cek-ketersediaan',
                'params' => [
                    'id' => DocoHelpers::decrypt($id),
                ]
            ],
        ];
        $data = DocoHelpers::poolRequest(Yii::$app->docoRest->apotek, DocoHelpers::makeGetRequests($requests));
        $get_info_produksi = $data['get_info_produksi'];
        $cek_ketersediaan = $data['cek_ketersediaan'];
        
        $status = $get_info_produksi['status_produksi'];
        $catatan = isset($get_info_produksi['catatan_bahanbaku']) ? $get_info_produksi['catatan_bahanbaku'] : '-';
        $pemesananProdukid = isset($get_info_produksi['pemesananproduksiobat_id']) ? DocoHelpers::encrypt($get_info_produksi['pemesananproduksiobat_id']) : null;
        $noPemesanan = isset($get_info_produksi['nopemesanan']) ? $get_info_produksi['nopemesanan'] : '-';
        $statusProduksiId = isset($get_info_produksi['status_produksi_id']) ? $get_info_produksi['status_produksi_id'] : 0;

        $detailKetersediaan = [];
        foreach ($cek_ketersediaan['detailKetersediaan'] as $row => $value) {
            $detailKetersediaan[] = $value;
        }
        
        $detailKetersediaan = json_encode($detailKetersediaan);
        $ObatTidakTersedia = isset($cek_ketersediaan['obatTidakTersedia']) && $cek_ketersediaan['obatTidakTersedia'] >= 1 ? true : false;
        
        return $this->controller->render('detail-produksi', get_defined_vars());

    }
}
