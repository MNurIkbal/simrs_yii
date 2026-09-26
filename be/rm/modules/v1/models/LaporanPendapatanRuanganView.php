<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpendapatanruangan_v".
 *
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $nama_pegawai
 * @property string $kelaspelayanan_nama
 * @property double $jasa_rumahsakit
 * @property double $jasa_layanan
 * @property double $total
 */
class LaporanPendapatanRuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpendapatanruangan_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['jasa_rumahsakit', 'jasa_layanan', 'total'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'carabayar_nama', 'penjamin_nama', 'nama_pegawai', 'kelaspelayanan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'nama_pegawai' => 'Nama Pegawai',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jasa_rumahsakit' => 'Jasa Rumahsakit',
            'jasa_layanan' => 'Jasa Layanan',
            'total' => 'Total',
        ];
    }
}
