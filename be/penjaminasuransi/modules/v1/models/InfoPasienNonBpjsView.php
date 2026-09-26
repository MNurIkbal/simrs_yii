<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasiennonbpjs_v".
 *
 * @property string $tipe
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $tglpasienpulang
 * @property string $no_pendaftaran
 * @property string $ruangan_nama
 * @property string $instalasi_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $pegawai_id
 * @property string $dokter
 * @property int $status_bayar
 * @property double $total_tagihan
 * @property double $total_sdh_bayar
 * @property double $total_sisa_tagihan
 * @property double $total_asuransi
 * @property bool $is_skd
 * @property string $status_skd
 */
class InfoPasienNonBpjsView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasiennonbpjs_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe', 'status_skd'], 'string'],
            [['pasien_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pegawai_id', 'status_bayar'], 'default', 'value' => null],
            [['pasien_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pegawai_id', 'status_bayar'], 'integer'],
            [['tgl_pendaftaran', 'tglpasienpulang'], 'safe'],
            [['total_tagihan', 'total_sdh_bayar', 'total_sisa_tagihan', 'total_asuransi'], 'number'],
            [['is_skd'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'instalasi_nama', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama', 'dokter'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe' => 'Tipe',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tglpasienpulang' => 'Tglpasienpulang',
            'no_pendaftaran' => 'No Pendaftaran',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_nama' => 'Instalasi Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'pegawai_id' => 'Pegawai ID',
            'dokter' => 'Dokter',
            'status_bayar' => 'Status Bayar',
            'total_tagihan' => 'Total Tagihan',
            'total_sdh_bayar' => 'Total Sdh Bayar',
            'total_sisa_tagihan' => 'Total Sisa Tagihan',
            'total_asuransi' => 'Total Asuransi',
            'is_skd' => 'Is Skd',
            'status_skd' => 'Status Skd',
        ];
    }
}
