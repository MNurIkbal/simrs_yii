<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers;
        $title = $helper->coalesce(DHtml::getTitleMenu(), Yii::t('fe', 'Laporan Kunjungan Fisioterapi'));
        $listDokterDpjp = [
            ['id' => 'Semua', 'text' => 'Semua']
        ];
        $listStatusPeriksa = [
            ['id' => 'Semua', 'text' => 'Semua']
        ];
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-kunjungan-fisioterapi-ranap/get-attributes',
            ]);
            $datasDokterDpjp = ArrayHelper::getValue($response, 'dokter_dpjp', []);
            $datasStatusPeriksa = ArrayHelper::getValue($response, 'status_periksa', []);
            foreach ($datasDokterDpjp as $key => $value) {
                $listDokterDpjp[] = [
                    'id' => ArrayHelper::getValue($value, 'pegawai_id'),
                    'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                ];
            }
            foreach ($datasStatusPeriksa as $key => $value) {
                $listStatusPeriksa[] = [
                    'id' => ArrayHelper::getValue($value, 'lookup_id'),
                    'text' => ArrayHelper::getValue($value, 'lookup_value')
                ];
            }
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
        $dataView = [
            'title' => $title,
            'listDokter' => $listDokterDpjp,
            'listStatusPeriksa' => $listStatusPeriksa
        ];
        return Yii::$app->controller->render('index', compact('dataView'));
    }
}
