<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * Validation model for update tanggal pulang form.
 *
 * @property int $pendaftaran_id
 * @property int $no_pendaftaran
 * @property int $pasienadmisi_id
 * @property int $pasienpulang_id
 * @property int $bpjs_id
 * @property string $nosep
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $nokartuasuransi
 * @property string $tglpasienpulang
 * @property string $status_pulang_id
 * @property string $status_pulang_nama
 * @property string $status_pulang
 * @property string $no_surat_kematian
 * @property string $tgl_meninggal
 * @property string $no_up_manual
 * @property string $user
 */

class UpdateTanggalPulangForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasienadmisi_id;
    public $pasienpulang_id;
    public $bpjs_id;
    public $nosep;
    public $no_rekam_medik;
    public $nama_pasien;
    public $nokartuasuransi;
    public $tglpasienpulang;
    public $status_pulang_id;
    public $status_pulang_nama;
    public $status_pulang;
    public $no_surat_kematian;
    public $tgl_meninggal;
    public $no_up_manual;
    public $user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    // 'no_pendaftaran',
                    'pasienadmisi_id',
                    // 'pasienpulang_id',
                    'bpjs_id',
                    'nosep',
                    // 'status_pulang_id',
                    // 'no_surat_kematian',
                    // 'tgl_meninggal',
                    'tglpasienpulang',
                    // 'no_up_manual',
                ], 
                'required'
            ],
            [
                [
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienadmisi_id',
                    'pasienpulang_id',
                    'bpjs_id',
                    'nosep',
                    'no_rekam_medik',
                    'nama_pasien',
                    'nokartuasuransi',
                    'tglpasienpulang',
                    'status_pulang_id',
                    'status_pulang_nama',
                    'status_pulang',
                    'no_surat_kematian',
                    'tgl_meninggal',
                    'no_up_manual',
                    'user',
                ], 'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran ID'),
            'no_pendaftaran' => Yii::t('fe', 'No. Pendaftaran'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasien Admisis ID'),
            'pasienpulang_id' => Yii::t('fe', 'Pasien Pulang ID'),
            'bpjs_id' => Yii::t('fe', 'Bpjs ID'),
            'nosep' => Yii::t('fe', 'No. SEP'),
            'no_rekam_medik' => Yii::t('fe', 'No. Rekam Medik'),
            'nama_pasien' => Yii::t('fe', 'Nama Pasien'),
            'nokartuasuransi' => Yii::t('fe', 'No Kartu'),
            'tglpasienpulang' => Yii::t('fe', 'Tanggal Pasien Pulang'),
            'status_pulang_id' => Yii::t('fe', 'Status Pulang ID'),
            'status_pulang_nama' => Yii::t('fe', 'Status Pulang Nama'),
            'status_pulang' => Yii::t('fe', 'Status Pulang'),
            'no_surat_kematian' => Yii::t('fe', 'No Surat Kematian'),
            'tgl_meninggal' => Yii::t('fe', 'Tanggal Meninggal'),
            'no_up_manual' => Yii::t('fe', 'No. LP Manual'),
            'user' => Yii::t('fe', 'User'),
        ];
    }
}