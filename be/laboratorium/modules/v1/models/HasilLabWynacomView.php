<?php

namespace app\modules\v1\models;

use Doco\Traits\Models\ExceptionLabTrait;
use Yii;

/**
 * This is the model class for table "laporanhasillab_v".
 *
* @property $no_pendaftaran
* @property $no_rekam_medik
* @property $nama_pasien
* @property $jeniskelamin_id
* @property $jeniskelamin_nama
* @property $dateofbirth
* @property $umur
* @property $status_keluarga
* @property $kesatuan
* @property $pangkat
* @property $poli_id
* @property $poli_nama
* @property $is_cyto
* @property $dokter_id
* @property $dokter_dokter
* @property $no_masukpenunjang
* @property $tglpenunjang
* @property $status_kepegawaian
* @property $diagnosa_id
* @property $diagnosa_nama
* @property $kelompoktindakan_id
* @property $kelompoktindakan_nama
* @property $test_id
* @property $test_nama
* @property $test_nama_lis
* @property $hasil_saatini
* @property $nilai_rujukan
* @property $tgl_pemeriksaan
* @property $catatan
* @property $test_group
* @property $petugas_pemeriksaan
* @property $test_method
* @property $is_hasil
*/

class HasilLabWynacomView extends \Doco\components\DocoActiveRecord
{
    use ExceptionLabTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanhasillab_v';
    }
}
