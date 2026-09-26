<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 18:00:44
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 10:46:07
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "anamnesa_t".
 *    
 * @property int $skrining_pasien_rj_id;
 * @property int $pendaftaran_id;
 * @property int $kesadaran;
 * @property int $pernapasan;
 * @property int $resiko_jatuh;
 * @property int $nyeri_dada;
 * @property int $nyeri;
 * @property int $batuk;
 * @property int $keputusan;
 * @property int $bahasa;
 * @property int $bahasa_daerah;
 * @property int $bahasa_asing;
 * @property int $poli_tujuan;
 * @property int $asal_rujukan;
 * @property int $petugas_loket_pendaftaran;
 * @property int $pasien_keluarga_pasien;
 */

class SkriningRajal extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'skrining_pasien_rj_t';
    }

   /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 'kesadaran', 'pernapasan', 'resiko_jatuh', 'nyeri_dada','nyeri',  'batuk', 
                    'keputusan'
                ], 
                'required'
            ],
            [
                [
                    'bahasa', 'bahasa_daerah', 'bahasa_asing','petugas_loket_pendaftaran', 'pasien_keluarga_pasien', 'poli_tujuan', 'asal_rujukan'
                ], 
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'kesadaran' => 'Kesadaran',
            'pernapasan' => 'Pernapasan',
            'resiko_jatuh' => 'Resiko Jatuh',
            'nyeri_dada' => 'Nyeri Dada',
            'nyeri' => 'Nyeri',
            'batuk' => 'Batuk', 
            'keputusan' => 'Keputusan',
            'bahasa' => 'Bahasa Sehai-hari',
            'bahasa_daerah' => 'Daerah',
            'bahasa_asing' => 'Asing',
            'poli_tujuan' => 'Poli Tujuan',       
            'asal_rujukan' => 'Asal Rujukan',       
        ];
    }
}
