<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-29 16:31:12 
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-16 17:35:28
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienmasukpenunjang_t".
 *
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $kelaspelayanan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $ruanganasal_id
 * @property string $no_masukpenunjang
 * @property string $tglmasukpenunjang
 * @property string $no_urutperiksa
 * @property string $kunjungan
 * @property string $status_periksa
 * @property bool $panggil_antrian
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
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property JadwalkunjunganrmT[] $jadwalkunjunganrmTs
 * @property PasienanastesiT[] $pasienanastesiTs
 * @property PasienbatalperiksaT[] $pasienbatalperiksaTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienadmisiT $pasienadmisi
 * @property PasienkirimkeunitlainT $pasienkirimkeunitlain
 * @property PegawaiM $pegawai
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 * @property RuanganM $ruanganasal
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class PasienMasukPenunjang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienmasukpenunjang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kelaspelayanan_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'pasien_id',  'tglmasukpenunjang','kunjungan'], 'required'],
            [['tglmasukpenunjang', 'created_date', 'last_modified_date', 'deleted_date','is_bayar','no_antrian'], 'safe'],
            [['panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['no_masukpenunjang'], 'string', 'max' => 20],
            [['kunjungan', 'status_periksa'], 'string', 'max' => 50],
            // [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskasuspenyakitM::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pasienkirimkeunitlain_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienkirimkeunitlainT::className(), 'targetAttribute' => ['pasienkirimkeunitlain_id' => 'pasienkirimkeunitlain_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['ruanganasal_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruanganasal_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Pegawai ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'ruanganasal_id' => 'Ruanganasal ID',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_urutperiksa' => 'No Urutperiksa',
            'kunjungan' => 'Kunjungan',
            'status_periksa' => 'Status Periksa',
            'panggil_antrian' => 'Panggil Antrian',
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
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(HasilpemeriksaanlabT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalkunjunganrmTs()
    {
        return $this->hasMany(JadwalkunjunganrmT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs()
    {
        return $this->hasMany(PasienanastesiT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienbatalperiksaTs()
    {
        return $this->hasMany(PasienbatalperiksaT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JeniskasuspenyakitM::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisi()
    {
        return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlain()
    {
        return $this->hasOne(PasienkirimkeunitlainT::className(), ['pasienkirimkeunitlain_id' => 'pasienkirimkeunitlain_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
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
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuanganasal()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruanganasal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }
}
