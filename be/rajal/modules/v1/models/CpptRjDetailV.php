<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cpptrjdetail_v".
 *
 * @property string $jenis
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
 * @property string $tgl_tindakan
 * @property string $instruksi
 */
class CpptRjDetailV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cpptrjdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis', 'subject', 'object', 'a_diag_utama', 'a_diag_penyerta', 'instruksi'], 'string'],
            [['pendaftaran_id', 'ruangan_id', 'pegawai_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'ruangan_id', 'pegawai_id'], 'integer'],
            [['tgl_tindakan'], 'safe'],
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
            'jenis' => 'Jenis',
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
            'tgl_tindakan' => 'Tgl Tindakan',
            'instruksi' => 'Instruksi',
        ];
    }
}
