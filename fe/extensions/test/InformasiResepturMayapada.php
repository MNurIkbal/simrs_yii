<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\test;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class InformasiResepturMayapada extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Informasi Reseptur Mayapada";
    protected $_status_resep =  [
        "Belum Proses",
        "Dalam Proses",
        "Diserahkan",
        "Batal Reseptur",
    ];

    protected function processFlow($controller)
    {
        $title = $this->_title;
        $model = new InformasiForm;
        $carabayar = \Yii::$app->cache->get('carabayar');

        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $cekLoket = empty(Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id])
                ? false
                : Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id];

        if ($cekLoket) {
            if (!$carabayar) {
                $response = Yii::$app->docoRest->master->get('cara-bayar/index?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
                \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
                $carabayar = $carabayar_data;
            }

            $status_resep = $this->_status_resep;
            return $controller->render('@app/extensions/test/views/informasi-reseptur-mhkn/index.php',get_defined_vars());
            // return $this->controller->render('index', get_defined_vars());
        } else {
            return Yii::$app->runAction('/apotek/informasi-reseptur/pilih-loket', [
                'jenisantrian_id' => DocoConstants::JA_FAR
            ]);
        }
    }

}