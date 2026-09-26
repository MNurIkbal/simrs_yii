<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "buatjanjipoli_t".
 *
 * @property int $buatjanjipoli_id
 * @property int $pendaftaran_id
 * @property int $pegawai_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property string $tgl_buatjanji
 * @property string $hari_jadwal lookup_type='hari'
 * @property string $tgl_jadwal
 * @property bool $by_phone
 * @property string $keterangan_buatjanji
 * @property string $antrian_id
 * @property string $no_buatjanji
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $pendaftaranasal_id
 * @property string $status_janjipoli lookup_type='status_janjipoli'
 * @property bool $is_rencanakontrol
 * @property int $carabayar_id
 * @property int $penjamin_id
 *
 * @property PasienM $pasien
 * @property PegawaiM $pegawai
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 */
class TraReservasiPoliklinikForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buatjanjipoli_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pendaftaranasal_id', 'carabayar_id', 'penjamin_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pendaftaranasal_id', 'carabayar_id', 'penjamin_id'], 'integer'],
            [['pegawai_id', 'ruangan_id', 'pasien_id', 'tgl_buatjanji', 'hari_jadwal', 'tgl_jadwal', 'antrian_id', 'status_janjipoli', 'carabayar_id', 'penjamin_id'], 'required'],
            [['tgl_buatjanji', 'tgl_jadwal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['by_phone', 'is_deleted', 'is_active', 'is_rencanakontrol'], 'boolean'],
            [['keterangan_buatjanji', 'additional_data'], 'string'],
            [['hari_jadwal'], 'string', 'max' => 20],
            [['antrian_id', 'status_janjipoli'], 'string', 'max' => 32],
            [['no_buatjanji'], 'string', 'max' => 100],
            [['no_buatjanji'], 'unique'],
            /*[['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pegawai_id' => 'Pegawai',
            'ruangan_id' => 'Ruangan',
            'pasien_id' => 'Pasien ID',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'hari_jadwal' => 'Hari Jadwal',
            'tgl_jadwal' => 'Tanggal Reservasi',
            'by_phone' => 'By Phone',
            'keterangan_buatjanji' => 'Keterangan',
            'antrian_id' => 'Antrian ID',
            'no_buatjanji' => 'No Buatjanji',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'pendaftaranasal_id' => 'Pendaftaranasal ID',
            'status_janjipoli' => 'Status Janjipoli',
            'is_rencanakontrol' => 'Is Rencanakontrol',
            'carabayar_id' => 'Carabayar',
            'penjamin_id' => 'Penjamin',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(PasienForm::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiForm::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranForm::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(RuanganForm::className(), ['ruangan_id' => 'ruangan_id']);
    }
}
