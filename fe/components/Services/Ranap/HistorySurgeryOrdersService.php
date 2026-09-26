<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\Services\Ranap;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class HistorySurgeryOrdersService extends BaseCurrentService
{
    public function execute($noRm)
    {
        try {
            $title         = Yii::t('fe', 'Riwayat Bedah');
            $pendaftaranId = Yii::$app->request->get('id', null);
            $dataPasien    = $this->guzzleExec($this->_restRanap, [
                'url'     => 'riwayat-pasien/patient-global',
                'method'  => 'GET',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => DocoHelpers::decrypt($pendaftaranId),
                    ]
                ]
            ]);
            return [
                'title'      => $title,
                'noRm'       => $noRm,
                'dataPasien' => $dataPasien['data']
            ];
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }
}
