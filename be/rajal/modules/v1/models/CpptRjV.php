<?php

namespace app\modules\v1\models;


use Yii;

/**
 * This is the model class for table "cpptrj_v".
 *
 * @property int $pendaftaran_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property string $kelompokpegawai_nama
 * @property string $subject
 * @property string $object
 * @property string $a_diag_utama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property string $catatan_dokter
 * @property string $tgl_soaprj
 * @property int $td_diastolic
 * @property int $td_systolic
 * @property int $pernapasan
 * @property double $beratbadan_kg
 * @property double $tinggibadan_cm
 * @property double $imt
 * @property int $detaknadi
 * @property double $suhutubuh
 * @property bool $is_nyeri
 * @property int $skala_nyeri
 * @property bool $is_resikojatuh
 * @property int $soaprj_id
 */
class CpptRjV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cpptrj_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'ruangan_id', 'pegawai_id', 'td_diastolic', 'td_systolic', 'pernapasan', 'detaknadi', 'skala_nyeri', 'soaprj_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'ruangan_id', 'pegawai_id', 'td_diastolic', 'td_systolic', 'pernapasan', 'detaknadi', 'skala_nyeri', 'soaprj_id'], 'integer'],
            [['subject', 'object', 'a_diag_utama', 'a_diag_penyerta', 'planning', 'catatan_dokter'], 'string'],
            [['tgl_soaprj'], 'safe'],
            [['beratbadan_kg', 'tinggibadan_cm', 'imt', 'suhutubuh'], 'number'],
            [['is_nyeri', 'is_resikojatuh'], 'boolean'],
            [['ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
            [['kelompokpegawai_nama'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'kelompokpegawai_nama' => 'Kelompokpegawai Nama',
            'subject' => 'Subject',
            'object' => 'Object',
            'a_diag_utama' => 'A Diag Utama',
            'a_diag_penyerta' => 'A Diag Penyerta',
            'planning' => 'Planning',
            'catatan_dokter' => 'Catatan Dokter',
            'tgl_soaprj' => 'Tgl Soaprj',
            'td_diastolic' => 'Td Diastolic',
            'td_systolic' => 'Td Systolic',
            'pernapasan' => 'Pernapasan',
            'beratbadan_kg' => 'Beratbadan Kg',
            'tinggibadan_cm' => 'Tinggibadan Cm',
            'imt' => 'Imt',
            'detaknadi' => 'Detaknadi',
            'suhutubuh' => 'Suhutubuh',
            'is_nyeri' => 'Is Nyeri',
            'skala_nyeri' => 'Skala Nyeri',
            'is_resikojatuh' => 'Is Resikojatuh',
            'soaprj_id' => 'Soaprj ID',
        ];
    }
}
