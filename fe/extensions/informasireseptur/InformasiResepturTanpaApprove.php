<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\informasireseptur;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class InformasiResepturTanpaApprove extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Informasi Reseptur";
    protected $_status_resep =  [
        346 => "Belum Proses",
        347 => "Dalam Proses",
        432 => "Batal Reseptur",
        660 => "Diserahkan",
        348 => "Sudah Bayar",
        349 => "Belum Bayar",
    ];
    protected $_status_racikan =  [
        "Racikan" => "Racikan",
        "Non Racikan" => "Non Racikan",
    ];
    protected $_is_kronis = [
        0 => "Tidak",
        1 => "Ya"
    ];

    const STAT_BAYAR_LUNAS = 348;
    const STAT_BAYAR_BLM_LUNAS = 349;



    protected function processFlow($controller)
    {
        
        $session = Yii::$app->session;
        $title = $this->_title;
        $model = new InformasiForm;
        $carabayar = \Yii::$app->cache->get('carabayar');
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $cekLoket = empty($session->get('active_loket')[$loginpemakai_id])
                ? false
                : $session->get('active_loket')[$loginpemakai_id];

        if ($cekLoket) {
            if (!$carabayar) {
                $response = Yii::$app->docoRest->master->get('cara-bayar/index?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
                \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
                $carabayar = $carabayar_data;
            }

            $status_resep = $this->_status_resep;
            $status_racikan = $this->_status_racikan;
            $is_kronis = $this->_is_kronis;

            $keteranganCetakEtiket = [ 
                [
                    'id' => 1,
                    'kode_warna' => '#FFFFFF',
                    'text' => 'Belum Cetak Etiket'
                ],
                [
                    'id' => 2,
                    'kode_warna' => '#05BBBE',
                    'text' => 'Sudah Cetak Etiket'
                ],
                [
                    'id' => 3,
                    'kode_warna' => '#FFD3D3',
                    'text' => 'Racikan'
                ],
                [
                    'id' => 4,
                    'kode_warna' => '#FFFFFF',
                    'text' => 'Non Racikan'
                ],
                [
                    'id' => 5,
                    'kode_warna' => '#E0B8FF',
                    'text' => 'Resep Kronis'
                ],
                [
                    'id' => 6,
                    'kode_warna' => '#b4f4ff',
                    'text' => 'Retur Obat'
                ],
            ];

            return $controller->render('@app/extensions/informasireseptur/views/index', get_defined_vars());
        } else {
            return Yii::$app->runAction('/apotek/informasi-reseptur/pilih-loket', [
                'jenisantrian_id' => DocoConstants::JA_FAR
            ]);
        }
    }

}