<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiPemesanan;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\TransaksiPemesananForm;

class ObatAlkesAction extends Action {
    protected $_title = "Pemesanan";
    protected $_ruangan_gdf = 25;
    protected $_instalasi_gdf = DocoConstants::INSTALASI_GUDANG_FARMASI;

    public function run($state = '') {
        $title = $this->_title . ' Obat Alkes';
        $model = new TransaksiPemesananForm;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_gdf = $this->_instalasi_gdf;
        $ruangan_gdf = $this->_ruangan_gdf;

        // Init Awal
        $model->instalasi_tujuan = $instalasi_gdf;

        try {
            Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
            $instalasi = Yii::$app->cache->get('instalasi');

            if ($instalasi == false) {
                $response = Yii::$app->docoRest->master->get('instalasi/get-all-data?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $instalasi_data = ArrayHelper::map($body['response']['data'], 'instalasi_id', 'instalasi_nama');
                Yii::$app->cache->set('instalasi', $instalasi_data, 60);
                $instalasi = $instalasi_data;
            }

            $result = Yii::$app->docoRest->apotek->get('allow/set-cache-konvert-satuan', []);
            $result = json_decode($result->getBody(), true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konvert-satuan', $cacheSatuan, 3600);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $cacheSatuan = [];
        }
        $cache = json_encode($cacheSatuan);

        return $this->controller->render('obat-alkes', get_defined_vars());
    }
}