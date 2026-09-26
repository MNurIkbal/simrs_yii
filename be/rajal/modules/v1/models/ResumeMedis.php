<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-18 11:34:05
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-18 11:34:14
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "resumemedis_t".
 *
 * @property int $resumemedis_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $pegawai_id
 * @property int $ruanganterakhir_id
 * @property int $pemeriksaanfisik_id
 * @property string $tgl_resume
 * @property string $ikhtisar_singkat
 * @property string $pengobatan_sementara
 * @property int $diagnosaawal_id
 * @property int $diagnosautama_id
 * @property string $saran
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
 */
class ResumeMedis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resumemedis_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resumemedis_id', 'pendaftaran_id', 'pasien_id', 'ruanganterakhir_id'], 'required'],
            [['resumemedis_id', 'pendaftaran_id', 'pasien_id', 'pegawai_id', 'ruanganterakhir_id', 'pemeriksaanfisik_id', 'diagnosaawal_id', 'diagnosautama_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['resumemedis_id', 'pendaftaran_id', 'pasien_id', 'pegawai_id', 'ruanganterakhir_id', 'pemeriksaanfisik_id', 'diagnosaawal_id', 'diagnosautama_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_resume', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['ikhtisar_singkat', 'pengobatan_sementara', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['saran'], 'string', 'max' => 255],
            [['resumemedis_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resumemedis_id' => 'Resumemedis ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'pegawai_id' => 'Pegawai ID',
            'ruanganterakhir_id' => 'Ruanganterakhir ID',
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'tgl_resume' => 'Tgl Resume',
            'ikhtisar_singkat' => 'Ikhtisar Singkat',
            'pengobatan_sementara' => 'Pengobatan Sementara',
            'diagnosaawal_id' => 'Diagnosaawal ID',
            'diagnosautama_id' => 'Diagnosautama ID',
            'saran' => 'Saran',
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