<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl3_1_kegiatanrawatinap_v".
 *
 * @property string $kolom
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $p_awal_tahun
 * @property string $p_masuk
 * @property string $p_keluar_hidup
 * @property string $p_keluar_mati_lbh_48
 * @property string $p_keluar_mati_krg_48
 * @property string $lama_rawat
 * @property string $p_akhir_tahun
 * @property string $hari_perawatan
 */
class RLKegiatanRawatInap extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl3_1_kegiatanrawatinap_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kolom', 'jeniskasuspenyakit_nama', 'kelaspelayanan_nama', 'p_awal_tahun', 'p_masuk', 'p_keluar_hidup', 'p_keluar_mati_lbh_48', 'p_keluar_mati_krg_48', 'lama_rawat', 'p_akhir_tahun', 'hari_perawatan'], 'string'],
            [['jeniskasuspenyakit_id', 'kelaspelayanan_id'], 'default', 'value' => null],
            [['jeniskasuspenyakit_id', 'kelaspelayanan_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kolom' => 'Kolom',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'p_awal_tahun' => 'P Awal Tahun',
            'p_masuk' => 'P Masuk',
            'p_keluar_hidup' => 'P Keluar Hidup',
            'p_keluar_mati_lbh_48' => 'P Keluar Mati Lbh 48',
            'p_keluar_mati_krg_48' => 'P Keluar Mati Krg 48',
            'lama_rawat' => 'Lama Rawat',
            'p_akhir_tahun' => 'P Akhir Tahun',
            'hari_perawatan' => 'Hari Perawatan',
        ];
    }
}
