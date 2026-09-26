<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "laporanrekapjasadokter_v".
 *
 * @property int $tindakankomponen_id
 * @property int $tindakanpelayanan_id
 * @property string $tgl_tindakan
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property double $tarif_tindakankompkomp
 */
class LaporanRekapJasaDokterForm extends \yii\base\Model
{
    public $tindakankomponen_id;
    public $tindakanpelayanan_id;
    public $tgl_tindakan;
    public $pegawai_id;
    public $nama_pegawai;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasien_id;
    public $no_rekam_medik;
    public $nama_pasien;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $tarif_tindakankompkomp;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanrekapjasadokter_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tindakankomponen_id', 'tindakanpelayanan_id', 'pegawai_id', 'pendaftaran_id', 'pasien_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['tindakankomponen_id', 'tindakanpelayanan_id', 'pegawai_id', 'pendaftaran_id', 'pasien_id', 'daftartindakan_id'], 'integer'],
            [['tgl_tindakan'], 'safe'],
            [['tarif_tindakankompkomp'], 'number'],
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
            'tindakankomponen_id' => Yii::t('fe', 'Tindakan komponen'),
            'tindakanpelayanan_id' => Yii::t('fe', 'Tindakan pelayanan'),
            'tgl_tindakan' => Yii::t('fe', 'Tanggal tindakan'),
            'pegawai_id' => Yii::t('fe', 'Pegawai'),
            'nama_pegawai' => Yii::t('fe', 'Nama pegawai'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'daftartindakan_id' => Yii::t('fe', 'Daftar tindakan'),
            'daftartindakan_nama' => Yii::t('fe', 'Nama daftar tindakan'),
            'tarif_tindakankompkomp' => Yii::t('fe', 'Tarif tindakan komp'),
        ];
    }
}
