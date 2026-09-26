<?php

namespace app\modules\master\models;
use app\components\DocoBaseModel;
use Yii;

/**
 * This is the model class for table "jadwaldokter_m".
 *
 * @property int $jadwaldokter_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $jadwaldokter_tgl
 * @property string $jadwaldokter_hari
 * @property string $jadwaldokter_waktupelayanan
 * @property string $jadwaldokter_mulai
 * @property string $jadwaldokter_tutup
 * @property int $maximumantrian
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property bool $is_loaddokter
 * @property int $jumlah_loaddokter
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property InstalasiM $instalasi
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 * @property int $kuota_total
 */
class JadwalDokterForm extends DocoBaseModel
{
    // const SCENARIO_IGD = 'IGD';
    // const SCENARIO_N_IGD = 'NIGD';
    public $kuota_online;
    public $pegawai_id;
    public $instalasi_id;
    public $ruangan_id;
    public $maximumantrian;
    public $created_by;
    public $modified_count;
    public $last_modified_by;
    public $deleted_by;
    public $jadwaldokter_tgl;
    public $jadwalbukapoli_id;
    public $created_date;
    public $last_modified_date;
    public $deleted_date;
    public $jadwaldokter_hari;
    public $jadwaldokter_waktupelayanan;
    public $jadwaldokter_mulai;
    public $jadwaldokter_tutup;
    public $additional_data;
    public $jumlah_loaddokter;
    public $is_deleted;
    public $kuota_total;
    public $is_loaddokter;
    public $is_active;
    public $jadwaldokter_id;
    public $kuota_bpjs_total;
    public $kuota_nonbpjs_total;
    public $kuota_bpjs_offline;
    public $kuota_nonbpjs_offline;
    public $kuota_bpjs_online;
    public $kuota_nonbpjs_online;
    public $is_bersedia;
    public $is_skip_jkn;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwaldokter_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'kuota_online', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'kuota_online', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kuota_total'], 'integer'],
            [['instalasi_id','pegawai_id', 'ruangan_id', 'jadwaldokter_tgl', 'jadwaldokter_waktupelayanan','jadwalbukapoli_id','jadwaldokter_mulai', 'jadwaldokter_tutup'], 'required'],
            [['jadwaldokter_tgl','jadwalbukapoli_id', 'created_date', 'last_modified_date', 'deleted_date','jadwaldokter_hari', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'is_bersedia', 'is_skip_jkn'], 'safe'],
            [['additional_data'], 'string'],
            [['jumlah_loaddokter', 'kuota_total'], 'integer'],
            [['is_deleted', 'is_active', 'is_loaddokter', 'is_bersedia', 'is_skip_jkn'], 'boolean'],
            [['jadwaldokter_waktupelayanan'], 'string', 'max' => 50],
            [['maximumantrian','kuota_online','kuota_bpjs_offline','kuota_nonbpjs_offline','kuota_bpjs_online','kuota_nonbpjs_online'],'validateKuota'],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instalasi::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }
    // public function scenarios()
    // {
    //     return [
    //         self::SCENARIO_IGD => ['instalasi_id','pegawai_id', 'ruangan_id', 'jadwaldokter_tgl', 'jadwaldokter_waktupelayanan','jadwaldokter_hari'],
    //         self::SCENARIO_N_IGD => ['instalasi_id','pegawai_id', 'ruangan_id', 'jadwaldokter_tgl', 'jadwaldokter_waktupelayanan', 'jadwalbukapoli_id'],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwaldokter_id' => Yii::t('fe','Jadwaldokter ID'),
            'pegawai_id' => Yii::t('fe','Dokter'),
            'instalasi_id' => Yii::t('fe','Instalasi'),
            'ruangan_id' => Yii::t('fe','Ruangan'),
            'jadwalbukapoli_id' => Yii::t('fe','Jadwal Buka Poli'),
            'jadwaldokter_tgl' => Yii::t('fe','Jadwaldokter Tgl'),
            'jadwaldokter_hari' => Yii::t('fe','Jadwal Dokter Hari'),
            'jadwaldokter_waktupelayanan' => Yii::t('fe','Jadwaldokter Waktupelayanan'),
            'jadwaldokter_mulai' => Yii::t('fe','Jam Mulai'),
            'jadwaldokter_tutup' => Yii::t('fe','Jam Tutup'),
            'maximumantrian' => Yii::t('fe','Antrian Walkin'),
            'kuota_online' => Yii::t('fe','Antrian Reservasi'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Aktif'),
            'is_loaddokter' => Yii::t('fe',''),
            'jumlah_loaddokter' => Yii::t('fe','Lama Pemeriksaan (menit)/Pasien'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'kuota_total' => Yii::t('fe','Total Kuota'),
            'kuota_bpjs_total' => Yii::t('fe', 'Kuota BPJS'),
            'kuota_nonbpjs_total' => Yii::t('fe', 'Kuota Non BPJS'),
            'kuota_bpjs_offline' => Yii::t('fe', 'Kuota BPJS'),
            'kuota_nonbpjs_offline' => Yii::t('fe', 'Kuota Non BPJS'),
            'kuota_bpjs_online' => Yii::t('fe', 'Kuota BPJS'),
            'kuota_nonbpjs_online' => Yii::t('fe', 'Kuota Non BPJS'),
            'is_bersedia' => Yii::t('fe','Bersedia'),
            'is_skip_jkn' => Yii::t('fe','Skip Update Jadwal HFIS'),
        ];
    }

    public function validateKuota($attributes)
    {
        $antrianwalkin = $this->kuota_bpjs_offline + $this->kuota_nonbpjs_offline;
        if($antrianwalkin != $this->maximumantrian){
            $this->addError('kuota_bpjs_offline','Jumlah Antrian Walkin tidak sesuai');
            $this->addError('kuota_nonbpjs_offline','Jumlah Antrian Walkin tidak sesuai');
        }

        $antrianreservasi = $this->kuota_bpjs_online + $this->kuota_nonbpjs_online;
        if($antrianreservasi != $this->kuota_online){
            $this->addError('kuota_bpjs_online','Jumlah Antrian Reservasi tidak sesuai');
            $this->addError('kuota_nonbpjs_online','Jumlah Antrian Reservasi tidak sesuai');
        }

        $totalkuota = $this->maximumantrian + $this->kuota_online;
        if($totalkuota != $this->kuota_total){
            $this->addError('maximumantrian','Total Kuota tidak sesuai');
            $this->addError('kuota_online','Total Kuota tidak sesuai');
        }

    }

}
