<?php
//author: Aris Munandar

namespace app\modules\ranap\models;

use Yii;
use app\components\DocoConstants;

class SuratKeteranganBayiForm extends \yii\base\Model
{
    // kebutuhan order penunjang
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $dokterdpjp_id;
    public $ibu_nama;
    public $ibu_ktp;
    public $ibu_alamat;
    public $ibu_pekerjaan;
    public $ibu_golongandarah;
    public $ayah_nama;
    public $ayah_ktp;
    public $ayah_alamat;
    public $ayah_pekerjaan_id;
    public $ayah_golongandarah_id;
    public $hari_lahir;
    public $tgl_lahir;
    public $jam_lahir;
    public $bb_lahir;
    public $panjang_lahir;
    public $kelahiran;
    public $golongan_darah;
    public $anakke;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return[
            [[
            'pendaftaran_id',
            'pasienadmisi_id',
            'pasien_id',
            'dokterdpjp_id',
            'ibu_nama',
            'ibu_ktp',
            'ibu_alamat',
            'ibu_pekerjaan',
            'ibu_golongandarah',
            'ayah_nama',
            'ayah_ktp',
            'ayah_alamat',
            'ayah_pekerjaan_id',
            'ayah_golongandarah_id',
            //'hari_lahir',
            'tgl_lahir',
            'jam_lahir',
            'bb_lahir',
            'panjang_lahir',
            'kelahiran',
            'golongan_darah',
            'anakke'

            ],'safe'],

            [[
            
                'ayah_nama',
                'ayah_ktp',
                'ayah_alamat',
                'ayah_pekerjaan_id',
                //'hari_lahir',
                'tgl_lahir',
                'jam_lahir',
                'bb_lahir',
                'panjang_lahir',
                'kelahiran',
                'anakke'
            ],
                'required'
            ]
       ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ayah_nama'             => Yii::t('fe', 'Nama Ayah'),
            'ayah_ktp'              => Yii::t('fe', 'No KTP'),
            'ayah_alamat'           => Yii::t('fe', 'Alamat Rumah'),
            'ayah_pekerjaan_id'     => Yii::t('fe', 'Pekerjaan'),
            'ayah_golongandarah_id' => Yii::t('fe', 'Golongan Darah'),
            //'hari_lahir'            => Yii::t('fe', 'Hari Lahir'),
            'tgl_lahir'             => Yii::t('fe', 'Tanggal Lahir'),
            'jam_lahir'             => Yii::t('fe', 'Jam Lahir'),
            'bb_lahir'              => Yii::t('fe', 'Berat Lahir'),
            'panjang_lahir'         => Yii::t('fe', 'Panjang Lahir'),
            'kelahiran'             => Yii::t('fe', 'Kelahiran'),
            'golongan_darah'        => Yii::t('fe', 'Golongan Darah'),
            'pekerjaan_id'          => Yii::t('fe', 'Pekerjaan'),
            'golongan_darah_ayah'   => Yii::t('fe', 'Golongan Darah Ayah'),
            'anakke'                => Yii::t('fe', 'Anak Ke')

        ];
    }
}
