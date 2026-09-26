<?php

/**
 * @Author: Iqbal
 * @Date:   2018-07-27 14:20:59
 */

namespace app\modules\v1\models;

use Yii;

/**
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $dokter_jaga_id
 * @property int $dokter_dpjp_id
 * @property string $nama_pasien
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property string $no_rekam_medik
 * @property string $jenis_kelamin
 * @property string $penjamin_nama
 * @property string $dokter_jaga
 * @property string $dokter_dpjp
 * @property string $status_periksa
 * @property string $status_periksa_id
 * @property date $tgl_pendaftaran
 */
class InfoPasienRi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienri_v';
    }
}
