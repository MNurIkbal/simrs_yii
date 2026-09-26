<?php

namespace app\modules\master\models;

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
 * @property string $no_antrian
 * @property string $kunjungan lookup_type='kunjungan'
 * @property string $status_periksa lookup_type='status_periksa'
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
 * @property int $instalasiasal_id
 */
class PasienMasukPenunjangForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienmasukpenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instalasiasal_id'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instalasiasal_id'], 'integer'],
            [['ruangan_id', 'pasien_id', 'no_masukpenunjang', 'tglmasukpenunjang'], 'required'],
            [['tglmasukpenunjang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['no_masukpenunjang'], 'string', 'max' => 20],
            [['no_antrian'], 'string', 'max' => 100],
            [['kunjungan', 'status_periksa'], 'string', 'max' => 50],
            [['no_masukpenunjang'], 'unique'],
            [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskasuspenyakitM::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            [['pasienkirimkeunitlain_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienkirimkeunitlainT::className(), 'targetAttribute' => ['pasienkirimkeunitlain_id' => 'pasienkirimkeunitlain_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            [['ruanganasal_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruanganasal_id' => 'ruangan_id']],
        ];
    }

    /**
     * {@inheritdoc}
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
            'no_antrian' => 'No Antrian',
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
            'instalasiasal_id' => 'Instalasiasal ID',
        ];
    }
}
