<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokter_v".
 *
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $ruangan_nama
 * @property int $pegawai_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $nama_keluarga
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $alamat_pegawai
 * @property string $agama
 * @property string $golongandarah
 * @property string $alamatemail
 * @property string $notelp_pegawai
 * @property string $nomobile_pegawai
 * @property string $photopegawai
 * @property int $pendidikan_id
 * @property string $pendidikan_nama
 * @property int $pendkualifikasi_id
 * @property string $pendkualifikasi_nama
 * @property string $nomorindukpegawai
 * @property int $pangkat_id
 * @property int $kelompokpegawai_id
 * @property int $jabatan_id
 * @property bool $is_deleted
 * @property string $instalasi_nama
 * @property string $jabatan_nama
 */
class DokterView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokter_v';
    }
}
