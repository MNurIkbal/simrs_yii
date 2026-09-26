<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "cppt_v".
 *
 * @property int $cppt_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $tgl_cppt
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property string $kelompokpegawai_nama
 * @property string $subject
 * @property string $object
 * @property int $a_diag_utama
 * @property string $diagnosa_nama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property string $instruksi
 * @property bool $is_verifikasi
 * @property string $pegawai_verifikasi
 * @property string $tgl_verifikasi
 */
class CpptView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cppt_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawai_id', 'a_diag_utama', 'a_diag_penyerta'], 'default', 'value' => null],
            [['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawai_id', 'a_diag_utama'], 'integer'],
            [['tgl_cppt', 'tgl_verifikasi'], 'safe'],
            [['subject', 'object', 'planning', 'instruksi', 'pegawai_instruksi'], 'string'],
            [['is_verifikasi'], 'boolean'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'nama_pegawai', 'pegawai_verifikasi'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur'], 'string', 'max' => 255],
            [['kelompokpegawai_nama'], 'string', 'max' => 30],
            [['diagnosa_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cppt_id' => 'Cppt ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'tgl_cppt' => 'Tgl Cppt',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'kelompokpegawai_nama' => 'Kelompokpegawai Nama',
            'subject' => 'Subject',
            'object' => 'Object',
            'a_diag_utama' => 'A Diag Utama',
            'diagnosa_nama' => 'Diagnosa Nama',
            'a_diag_penyerta' => 'A Diag Penyerta',
            'planning' => 'Planning',
            'instruksi' => 'Instruksi',
            'is_verifikasi' => 'Is Verifikasi',
            'pegawai_verifikasi' => 'Pegawai Verifikasi',
            'tgl_verifikasi' => 'Tgl Verifikasi',
            'pegawai_instruksi' => 'Pegawai Instruksi',
        ];
    }

    /**
     * This function will return this class and add clause for unfinished soap
     * 
     * @param String $pegawai_id this for specific pegawai
     * @return Class
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function unfinishedSoap($pegawai_id)
    {
        return self::find()->andWhere(['pegawai_id' => $pegawai_id])
            ->andWhere([
                'or',
                ['is', 'subject', null],
                ['is', 'object', null],
                ['is', 'a_diag_utama', null],
                ['is', 'planning', null]
            ])
            ->andWhere([
                'is_deleted' => false,
                'is_active' => true,
                'instruksi' => null
            ]);
    }
}
