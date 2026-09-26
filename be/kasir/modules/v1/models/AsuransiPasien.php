<?php

namespace app\modules\v1\models;
use Yii;

/**
 * This is the model class for table "asuransipasien_m".
 *
 * @property int $asuransipasien_id
 * @property int $pasien_id
 * @property int $jenispeserta_id
 * @property int $penjamin_id
 * @property int $carabayar_id
 * @property string $nokartuasuransi
 * @property string $nopeserta
 * @property string $namapemilikasuransi
 * @property string $tglcetakkartuasuransi
 * @property int $kelastanggunganasuransi_id
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $kodefeskesgigi
 * @property string $namafeskesgigi
 * @property string $namaperusahaan
 * @property string $nomorpokokperusahaan
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property string $hubkeluarga
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
 * @property CarabayarM $carabayar
 * @property JenispesertaM $jenispeserta
 * @property KelaspelayananM $kelastanggunganasuransi
 * @property PasienM $pasien
 * @property PenjaminM $penjamin
 */
class AsuransiPasien extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'nokartuasuransi',
        'namapemilikasuransi',
        'nomorpokokperusahaan',
        'namaperusahaan'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asuransipasien_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'penjamin_id', 'carabayar_id', 'nokartuasuransi'], 'required'],
            [['pasien_id', 'jenispeserta_id', 'penjamin_id', 'carabayar_id', 'kelastanggunganasuransi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'jenispeserta_id', 'penjamin_id', 'carabayar_id', 'kelastanggunganasuransi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglcetakkartuasuransi', 'masaberlakukartu','pendaftaran_id', 'tgl_konfirmasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nokartuasuransi', 'nopeserta', 'namapemilikasuransi', 'kodefeskestk1', 'kodefeskesgigi', 'namaperusahaan', 'nomorpokokperusahaan', 'status_konfirmasi'], 'string', 'max' => 50],
            [['nama_feskestk1', 'namafeskesgigi', 'nopassport'], 'string', 'max' => 200],
            [['nokartukeluarga', 'hubkeluarga'], 'string', 'max' => 100],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['jenispeserta_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenispesertaM::className(), 'targetAttribute' => ['jenispeserta_id' => 'jenispeserta_id']],
            // [['kelastanggunganasuransi_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelastanggunganasuransi_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asuransipasien_id' => 'Asuransipasien ID',
            'pasien_id' => 'Pasien ID',
            'jenispeserta_id' => 'Jenispeserta ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_id' => 'Carabayar ID',
            'nokartuasuransi' => 'Nokartuasuransi',
            'nopeserta' => 'Nopeserta',
            'namapemilikasuransi' => 'Namapemilikasuransi',
            'tglcetakkartuasuransi' => 'Tglcetakkartuasuransi',
            'kelastanggunganasuransi_id' => 'Kelastanggunganasuransi ID',
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'kodefeskesgigi' => 'Kodefeskesgigi',
            'namafeskesgigi' => 'Namafeskesgigi',
            'namaperusahaan' => 'Namaperusahaan',
            'nomorpokokperusahaan' => 'Nomorpokokperusahaan',
            'masaberlakukartu' => 'Masaberlakukartu',
            'nokartukeluarga' => 'Nokartukeluarga',
            'nopassport' => 'Nopassport',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'hubkeluarga' => 'Hubkeluarga',
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
    public function getCarabayar()
    {
        return $this->hasOne(CaraBayar::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenispeserta()
    {
        return $this->hasOne(JenisPeserta::className(), ['jenispeserta_id' => 'jenispeserta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelastanggunganasuransi()
    {
        return $this->hasOne(KelasPelayanan::className(), ['kelaspelayanan_id' => 'kelastanggunganasuransi_id']);
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
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamin_id']);
    }
}
