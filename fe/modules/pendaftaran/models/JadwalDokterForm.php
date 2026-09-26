<?php

namespace app\modules\pendaftaran\models;

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
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property InstalasiM $instalasi
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 */
class JadwalDokterForm extends \yii\base\Model
{
    public $instalasi_id;
    public $ruangan_id;
    public $dokter_id;

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
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instalasi_id', 'ruangan_id', 'jadwaldokter_tgl', 'jadwaldokter_hari', 'jadwaldokter_waktupelayanan'], 'required'],
            [['jadwaldokter_tgl', 'jadwaldokter_mulai', 'jadwaldokter_tutup', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jadwaldokter_hari'], 'string', 'max' => 20],
            [['jadwaldokter_waktupelayanan'], 'string', 'max' => 50],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instalasi::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jadwaldokter_tgl' => 'Jadwaldokter Tgl',
            'jadwaldokter_hari' => 'Jadwaldokter Hari',
            'jadwaldokter_waktupelayanan' => 'Jadwaldokter Waktupelayanan',
            'jadwaldokter_mulai' => 'Jadwaldokter Mulai',
            'jadwaldokter_tutup' => 'Jadwaldokter Tutup',
            'maximumantrian' => 'Maximumantrian',
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

}
