<?php

namespace app\modules\v1\models;

use Yii;

class Triase extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'triase_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'dokter_id',
                    'perawat_id',
                    'tgl_triase',
                    'keluhan_utama',
                    'tekanan_darah',
                    'nadi',
                    'nafas',
                    'suhu',
                    'saturasi_oksigen',
                    'alergi',
                    'alergi_obat',
                    'alergi_lainnya',
                    'trauma',
                    'jalan_nafas',
                    'pernafasan',
                    'sirkulasi',
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'is_kapitis',
                    'hasil_gcs',
                    'waktu_respon',
                    'hasil_triase',
                    'kamartempattidur_id',
                    'disability',
                    'tekanan_darah_sistolik',
                    'tekanan_darah_diastolik',
                    'is_doa',
                    'observation_site'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Function to handle patient-to-bed relation by checking the bed status 
     * 
     * @param String $pendaftaran_id
     * @return Boolean
     */
    public function updateTempatTidur($pendaftaran_id)
    {
        $triaseData = self::find()->select([
            'triase_id',
            'pendaftaran_id',
            'kamartempattidur_id'
        ])->where(compact('pendaftaran_id'))->asArray()->one();

        if (!empty($triaseData) && isset($triaseData['kamartempattidur_id']) && !empty($triaseData['kamartempattidur_id'])) {
            $statusKamarTempatTidur = \app\modules\v1\models\KamarTempatTidur::find()->select([
                'status_isi'
            ])->where([
                'kamartempattidur_id' => $triaseData['kamartempattidur_id']
            ])->scalar();
            if (!$statusKamarTempatTidur) {
                // Jika tempat tidur kosong, assign pasien ke tempat tidur sebelumnya
                \app\modules\v1\models\KamarTempatTidur::updateAll([
                    'status_isi' => true
                ], 'kamartempattidur_id = :kamartempattidur_id', [
                    ':kamartempattidur_id' => $triaseData['kamartempattidur_id']
                ]);

                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'ketersediaan-bed-'.Yii::$app->params['mode'],            
                    'message' => json_encode(['kamartempattidur_id'=>$triaseData['kamartempattidur_id'], 'status_isi'=>true]),
                ]);
            } else {
                // Jika sudah terisi, nilai kamartempattidur_id dikosongkan
                self::updateAll([
                    'kamartempattidur_id' => null
                ], 'triase_id = :triase_id', [
                    ':triase_id' => $triaseData['triase_id']
                ]);
            }
        }

        return true;
    }
}
