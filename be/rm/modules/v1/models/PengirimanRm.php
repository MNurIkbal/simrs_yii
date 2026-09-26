<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pengirimanrm_t".
 *
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $kembalirm_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $dokrekammedis_id
 * @property int $ruangan_id
 * @property int $petugaspengirim_id
 * @property int $ruanganpengirim_id
 * @property int $petugaspenerima_id
 * @property int $ruanganpenerima_id
 * @property string $nourut_keluar
 * @property string $tgl_pengirimanrm
 * @property bool $kelengkapan_dokumen
 * @property bool $print_pengiriman
 * @property string $tgl_terimadokrm
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
 *
 * @property PendaftaranT[] $pendaftaranTs
 * @property DokrekammedisM $dokrekammedis
 * @property KembalirmT $kembalirm
 * @property PasienM $pasien
 * @property PegawaiM $petugaspengirim
 * @property PegawaiM $petugaspenerima
 * @property PeminjamanrmT $peminjamanrm
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 * @property RuanganM $ruanganpengirim
 * @property RuanganM $ruanganpenerima
 */
class PengirimanRm extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pengirimanrm_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peminjamanrm_id', 'kembalirm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'petugaspenerima_id', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['peminjamanrm_id', 'kembalirm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'petugaspenerima_id', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pasien_id', 'dokrekammedis_id', 'ruangan_id', 'ruanganpengirim_id', 'nourut_keluar', 'tgl_pengirimanrm'], 'required'],
            [['tgl_pengirimanrm', 'tgl_terimadokrm', 'created_date', 'last_modified_date', 'deleted_date','pasien_id'], 'safe'],
            [['kelengkapan_dokumen', 'print_pengiriman', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['nourut_keluar'], 'string', 'max' => 5],
            [['dokrekammedis_id'], 'exist', 'skipOnError' => true, 'targetClass' => DokRekamMedis::className(), 'targetAttribute' => ['dokrekammedis_id' => 'dokrekammedis_id']],
            // [['kembalirm_id'], 'exist', 'skipOnError' => true, 'targetClass' => KembalirmT::className(), 'targetAttribute' => ['kembalirm_id' => 'kembalirm_id']],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['petugaspengirim_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['petugaspengirim_id' => 'pegawai_id']],
            // [['petugaspenerima_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['petugaspenerima_id' => 'pegawai_id']],
            [['peminjamanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanRm::className(), 'targetAttribute' => ['peminjamanrm_id' => 'peminjamanrm_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['ruanganpengirim_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruanganpengirim_id' => 'ruangan_id']],
            // [['ruanganpenerima_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruanganpenerima_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'peminjamanrm_id' => 'Peminjamanrm ID',
            'kembalirm_id' => 'Kembalirm ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'ruangan_id' => 'Ruangan ID',
            'petugaspengirim_id' => 'Petugaspengirim ID',
            'ruanganpengirim_id' => 'Ruanganpengirim ID',
            'petugaspenerima_id' => 'Petugaspenerima ID',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'nourut_keluar' => 'Nourut Keluar',
            'tgl_pengirimanrm' => 'Tgl Pengirimanrm',
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'print_pengiriman' => 'Print Pengiriman',
            'tgl_terimadokrm' => 'Tgl Terimadokrm',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['pengirimanrm_id' => 'pengirimanrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedis()
    {
        return $this->hasOne(DokRekamMedis::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKembalirm()
    {
        return $this->hasOne(KembaliRm::className(), ['kembalirm_id' => 'kembalirm_id']);
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
    public function getPetugaspengirim()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'petugaspengirim_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPetugaspenerima()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'petugaspenerima_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamanrm()
    {
        return $this->hasOne(PeminjamanRm::className(), ['peminjamanrm_id' => 'peminjamanrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuanganpengirim()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruanganpengirim_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuanganpenerima()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruanganpenerima_id']);
    }

    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id'])
            ->viaTable('peminjamanrm_t as peminjam', ['peminjamanrm_id' => 'peminjamanrm_id']);
    }

    public function extraFields()
    {
        return [
            'dokrekammedis_m' => function($item){
                return $item->dokrekammedis;
            },
            'peminjamanrm_t' => function($item){
                return $item->peminjamanrm;
            },
            'pasien_m' => function($item){
                return $item->pasien;
            },
            'ruangan_m' => function($item){
                return $item->ruangan;
            },
            'instalasi_m' => function($item){
                return $item->instalasi;
            }
        ];
    }
}
