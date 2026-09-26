<?php

namespace Doco\models\kasir;

use Yii;

/**
 * This is the model class for table "cetakkwitansibkm_v".
 *
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $instalasi_id
 * @property string $no_kwitansi
 * @property string $no_bkm
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $tgl_pembayaran
 * @property string $tglpulang_pendaftaran
 * @property string $tglpulang_ranap
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property double $total_terbayar
 * @property string $kasir
 */
class CetakKwitansiBkm extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cetakkwitansibkm_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'instalasi_id'], 'default', 'value' => null],
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'instalasi_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pembayaran', 'tglpulang_pendaftaran', 'tglpulang_ranap'], 'safe'],
            [['total_terbayar'], 'number'],
            [['no_kwitansi', 'no_bkm', 'nama_pasien', 'kasir'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'no_kwitansi' => 'No Kwitansi',
            'no_bkm' => 'No Bkm',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tgl_pembayaran' => 'Tgl Pembayaran',
            'tglpulang_pendaftaran' => 'Tglpulang Pendaftaran',
            'tglpulang_ranap' => 'Tglpulang Ranap',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'total_terbayar' => 'Total Terbayar',
            'kasir' => 'Kasir',
        ];
    }
}
