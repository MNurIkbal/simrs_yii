<?php
/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of Sirs
 * Powered by Sirs
 */

namespace app\extensions\rm\InfDaftarPasien;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class InfDaftarPasienMhg extends \app\components\DocoBaseProcessExtension
{
    protected $title = 'Informasi Daftar Pasien';

    protected function processFlow($controller)
    {
        $data = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->rm, [
            'url' => 'inf-daftar-pasien/get-bundle-data',
            'payload' => [
                'query' => []
            ]
        ]);

		return $controller->render('@app/extensions/rm/views/inf-daftar-pasien/indexMhg', [
            'title' => $this->title,
            'statusMonitoringList' => ArrayHelper::map($data['results']['status_riwayat_rm'], 'lookup_id', 'lookup_name'),
            'jenisReservasi' => ArrayHelper::map($data['jenisReservasi']['jenis_reservasi'], 'lookup_id', 'lookup_name'),
        ]);
    }
}