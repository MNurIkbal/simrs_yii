<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal;

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
        $title = $helper->coalesce(DHtml::getTitleMenu(), Yii::t('fe', 'Laporan Pemeriksaan Fisioterapi'));
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('laporan-pemeriksaan-fisioterapi-rajal/get-attributes');
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            if ($response) {
                $dokterDpjp = ArrayHelper::getValue($response, 'dokter_dpjp', []);
            }
            if (is_array($dokterDpjp)) {
                $listDokter[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($dokterDpjp) > 0) {
                    foreach($dokterDpjp as $value) { 
                        $listDokter[] = [
                            'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ]; 
                    }
                }   
            }
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
        $dataView = [
            'title' => $title,
            'listDokter' => $listDokter
        ];
        return Yii::$app->controller->render('index', compact('dataView'));    
    }
}