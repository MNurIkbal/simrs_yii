<?php

namespace app\modules\antrian\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $nik
 * @property string $nomor_kartu_bpjs
 * @property string $nomor_identitas
 * @property string $nomor_rujukan
 * @property date $nama_dokter
 * @property string $poli_tujuan
 * @property date $jam_praktek
 * @property int $jenis_kunjungan
 *
 */

class AntrianOnsiteForm extends \yii\base\Model
{
    public $jenis_identitas;
    public $nomor_identitas;
    public $nomor_rujukan;
    public $nama_dokter;
    public $dokter_id;
    public $kode_ruangan_bpjs;
    public $poli_tujuan;
    public $jam_praktek;
    public $jenis_kunjungan;
    public $nomor_surat_kontrol;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'jenis_identitas',
                'nomor_identitas',
                'nomor_rujukan',
                'nama_dokter',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            ['nomor_surat_kontrol', 'required', 'when' => function ($model) {
                return $model->jenis_kunjungan == '1098';
            }, 'whenClient' => "function (attribute, value) {
                return $('#frm-jenis-kunjungan').val() == '1098';
            }"],
            [[ 
                'jenis_identitas',
                'nomor_identitas',
                'nomor_rujukan',
                'nama_dokter',
                'dokter_id',
                'kode_ruangan_bpjs',
                'poli_tujuan',
                'jam_praktek',
                'jenis_kunjungan',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis_identitas' => 'Jenis Identitas',
            'nomor_identitas' => 'Nomor Identitas',
            'nomor_rujukan' => 'Nomor Rujukan',
            'nomor_surat_kontrol' => 'Nomor Surat Kontrol',
            'nama_dokter' => 'Nama Dokter',
            'poli_tujuan' => 'Poli Tujuan',
            'jam_praktek' => 'Jam Praktek',
            'jenis_kunjungan' => 'Jenis Kunjungan',
        ];
    }

}