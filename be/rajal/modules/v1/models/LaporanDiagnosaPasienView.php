<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporandiagnosapasien_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama    
 * @property int $pasien_id
 * @property string $no_rekam_medik    
 * @property string $nama_pasien    
 * @property int $diagnosa_id
 * @property string $diagnosa_kode    
 * @property string $diagnosa_nama    
 * @property string $diagnosa_namalainnya    
 * @property string $diagnosa_katakunci    
 * @property datetime $tgl_diagnosa    
 * @property int $pasienmorbiditas_id
 * @property int $ruangan_id
 * @property int $ruangan_nama
 * @property int $instalasi_id
 * @property int $instalasi_nama
 * @property bool $is_deleted
 * @property int $klasifikasidiagnosa_id
 * @property string $klasifikasidiagnosa_nama   
 * @property int $kelompokdiagnosa_id
 * @property string $kelompokdiagnosa_nama
 */
class LaporanDiagnosaPasienView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporandiagnosapasien_v';
    }
}
