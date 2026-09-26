<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopembebasantarif_v".
 *
 * @property int $pembebasantarif_id
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $pegawai_id
 * @property string $nama_dokter
 * @property string $no_pembebasantarif
 * @property double $total_tagihan
 * @property double $total_pembebasantarif
 * @property string $catatan
 * @property string $status_pembebasantarif
 */
class InfoPembebasanTarifView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopembebasantarif_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembebasantarif_id', 'pendaftaran_id', 'pasien_id', 'kelaspelayanan_id', 'pegawai_id'], 'default', 'value' => null],
            [['pembebasantarif_id', 'pendaftaran_id', 'pasien_id', 'kelaspelayanan_id', 'pegawai_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['total_tagihan', 'total_pembebasantarif'], 'number'],
            [['catatan'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'kelaspelayanan_nama', 'nama_dokter'], 'string', 'max' => 50],
            [['no_pembebasantarif'], 'string', 'max' => 255],
            [['status_pembebasantarif'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembebasantarif_id' => 'Pembebasantarif ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'pegawai_id' => 'Pegawai ID',
            'nama_dokter' => 'Nama Dokter',
            'no_pembebasantarif' => 'No Pembebasantarif',
            'total_tagihan' => 'Total Tagihan',
            'total_pembebasantarif' => 'Total Pembebasantarif',
            'catatan' => 'Catatan',
            'status_pembebasantarif' => 'Status Pembebasantarif',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembebasanTarif()
    {
        return $this->hasOne(PembebasanTarif::className(), ['pembebasantarif_id' => 'pembebasantarif_id']);
    }

    public function extraFields()
    {
        return [
            'pasien_m' => function($item){
                return $item->pasien;
            },
            'pendaftaran_m' => function($item){
                return $item->pendaftaran;
            },
            'pembebasantarif_t' => function($item){
                return $item->pembebasanTarif;
            },
        ];
    }
}
