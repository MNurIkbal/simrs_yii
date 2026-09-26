<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "laporanrekapjasadokter_v".
 *
 * @property int $tindakankomponen_id
 * @property int $tindakanpelayanan_id
 * @property string $tgl_tindakan
 * @property int $pegawai_id
 * @property int $dokterpenanggungjawab_id
 * @property string $nama_pegawai
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property double $tarif_tindakankomp
 * @property string $status_bayar
 */
class LaporanRekapJasaDokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanrekapjasadokter_v';
    }

    public static function primaryKey()
    {
        return ['pendaftaran_id', 'dokterpenanggungjawab_id', 'tindakanpelayanan_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tindakankomponen_id', 'tindakanpelayanan_id', 'pegawai_id', 'dokterpenanggungjawab_id', 'pendaftaran_id', 'pasien_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['tindakankomponen_id', 'tindakanpelayanan_id', 'pegawai_id', 'pendaftaran_id', 'pasien_id', 'daftartindakan_id'], 'integer'],
            [['tgl_tindakan', 'status_bayar'], 'safe'],
            [['tarif_tindakankomp'], 'number'],
            [['nama_pegawai', 'nama_pasien'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tindakankomponen_id' => 'Tindakan komponen',
            'tindakanpelayanan_id' => 'Tindakan pelayanan',
            'tgl_tindakan' => 'Tanggal tindakan',
            'pegawai_id' => 'Pegawai',
            'dokterpenanggungjawab_id' => 'Dokter',
            'nama_pegawai' => 'Nama pegawai',
            'pendaftaran_id' => 'Pendaftaran',
            'no_pendaftaran' => 'No pendaftaran',
            'pasien_id' => 'Pasien',
            'no_rekam_medik' => 'No rekam medik',
            'nama_pasien' => 'Nama pasien',
            'daftartindakan_id' => 'Daftar tindakan',
            'daftartindakan_nama' => 'Nama daftar tindakan',
            'tarif_tindakankomp' => 'Tarif tindakan komp',
            'status_bayar' => 'Status Billing',
        ];
    }
}
