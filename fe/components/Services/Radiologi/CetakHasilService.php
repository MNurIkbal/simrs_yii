<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\Services\Radiologi;

use Yii;
use app\components\DocoHelpers;

class CetakHasilService extends BaseCurrentService
{
    /**
     * @method Cetak Hasil Radiologi
     * @param String $path
     * @return Object
     */
    public function execute($path)
    {
        try {
            $daftartindakan_id = Yii::$app->request->get('id');
            $tindakanpelayanan_id = Yii::$app->request->get('tindakan_id');
            $penunjang_id = Yii::$app->request->get('penunjang_id');
            $activeWorkspace = Yii::$app->session->get('active_workspace');
            $modulAlias = $activeWorkspace['modul_alias'];
            $response = $this->_restRad->get('input-hasil/cetak-hasil-pdf', [
                'save_to' => $path,
                'query' => [
                    'modul'                   => $modulAlias,
                    'daftartindakan_id'       => DocoHelpers::decrypt($daftartindakan_id),
                    'tindakanpelayanan_id'    => DocoHelpers::decrypt($tindakanpelayanan_id),
                    'pasienmasukpenunjang_id' => DocoHelpers::decrypt($penunjang_id),
                    'hasilpemeriksaanrad_id'  => !empty($hasilpemeriksaanrad_id) ? DocoHelpers::decrypt($hasilpemeriksaanrad_id) : null,
                ],
            ]);
            return $response;
        } catch (\Throwable $th) {
            Yii::error($th, 'Cetak Hasil Radiologi');
            return ['error' => $th->getMessage()];
        }
    }
}
