<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-15 14:57:04
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-10 17:22:02
 */

namespace Doco\bedah\models;

class PostOperasiForm extends \yii\base\Model
{
    public $inpostoperasi_id;
    public $pasienmasukpenunjang_id;
    public $is_recovery;
    public $jam_masuk_rec;
    public $jam_keluar_rec;
    public $kembali_ruangan_id;
    public $kesadaran_umum;
    public $kesadaran_umum_lain;
    public $tingkat_kesadaran;
    public $tingkat_kesadaran_lain;
    public $jalan_napas;
    public $jalan_napas_lain;
    public $terapi_oksigen;
    public $terapi_oksigen_lain;
    public $l_mnt;
    public $kulit_datang;
    public $kulit_datang_lain;
    public $kulit_keluar;
    public $kulit_keluar_lain;
    public $sirkulasi_badan;
    public $sirkulasi_badan_lain;
    public $area_luka;
    public $is_skrining_nyeri;
    public $ket_skrining;
    public $skala_nyeri;
    public $lokasi;
    public $metode_nyeri;
    public $resiko_jatuh;
    public $barang_pasien;
    public $is_pasanginfus;
    public $keterangan;
    public $pemberitahu_perawat;
    public $perawat_datang;
    public $last;

    public function rules()
    {
        return [
            [
                ['jam_masuk_rec', 'jam_keluar_rec'], 'required', 'when' => function($model) {
                    return $model->is_recovery;
                }
            ],
            [
                'kembali_ruangan_id', 'required', 'when' => function($model) {
                    return !$model->is_recovery;
                }
            ],
            [
                ['pemberitahu_perawat', 'perawat_datang'], 'required', 'message' => '{attribute} Tidak Boleh Kosong!'
            ],
            [['inpostoperasi_id','pasienmasukpenunjang_id','is_recovery', 'jam_masuk_rec', 'jam_keluar_rec', 'kembali_ruangan_id', 'kesadaran_umum', 'kesadaran_umum_lain', 'tingkat_kesadaran', 'tingkat_kesadaran_lain', 'jalan_napas','jalan_napas_lain','terapi_oksigen','terapi_oksigen_lain','l_mnt','kulit_datang','kulit_datang_lain','kulit_keluar','kulit_keluar_lain','sirkulasi_badan','sirkulasi_badan_lain','area_luka','is_skrining_nyeri','ket_skrining','skala_nyeri','lokasi','metode_nyeri','resiko_jatuh','barang_pasien', 'is_pasanginfus', 'keterangan', 'pemberitahu_perawat','perawat_datang', 'last'], 'safe'],
            [['metode_nyeri', 'skala_nyeri'], 'integer', 'message'=>'{attribute} Harus berupa angka!']
        ];
    }
    public function attributeLabels()
    {
        return [
            'is_recovery' => \Yii::t('fe', 'Masuk recovery room'),
            'jam_masuk_rec' => \Yii::t('fe', 'Masuk pukul'),
            'jam_keluar_rec' => \Yii::t('fe', 'Keluar pukul'),
            'kembali_ruangan_id' => \Yii::t('fe', 'Kembali ke ruangan'),
            'kesadaran_umum' => \Yii::t('fe', 'Kesadaran umum'),
            'kesadaran_umum_lain' => \Yii::t('fe', 'Kesadaran umum lain'),
            'tingkat_kesadaran' => \Yii::t('fe', 'Tingkat kesadaran'),
            'tingkat_kesadaran_lain' => \Yii::t('fe', 'Tingkat kesadaran lain'),
            'jalan_napas' => \Yii::t('fe', 'Jalan nafas'),
            'jalan_napas_lain' => \Yii::t('fe', 'Jalan nafas lain'),
            'terapi_oksigen' => \Yii::t('fe', 'Terapi oksigen'),
            'terapi_oksigen_lain' => \Yii::t('fe', 'Terapi oksigen lain'),
            'l_mnt' => \Yii::t('fe', 'L/mnt'),
            'kulit_datang' => \Yii::t('fe', 'Keadaan kulit ketika datang'),
            'kulit_datang_lain' => \Yii::t('fe', 'Keadaan kulit ketika datang lain'),
            'kulit_keluar' => \Yii::t('fe', 'Keadaan kulit ketika keluar'),
            'kulit_keluar_lain' => \Yii::t('fe', 'Keadaan kulit ketika keluar lain'),
            'sirkulasi_badan' => \Yii::t('fe', 'Sirkulasi anggota badan'),
            'sirkulasi_badan_lain' => \Yii::t('fe', 'Sirkulasi anggota badan lain'),
            'area_luka' => \Yii::t('fe', 'Area luka operasi'),
            'is_skrining_nyeri' => \Yii::t('fe', 'Skrining nyeri'),
            'ket_skrining' => \Yii::t('fe', 'Keterangan'),
            'skala_nyeri' => \Yii::t('fe', 'Skala nyeri'),
            'lokasi' => \Yii::t('fe', 'Lokasi'),
            'metode_nyeri' => \Yii::t('fe', 'Metode'),
            'resiko_jatuh' => \Yii::t('fe', 'Resiko jatuh'),
            'barang_pasien' => \Yii::t('fe', 'Barang pasien yang dikembalikan'),
            'is_pasanginfus' => \Yii::t('fe', 'Pemasangan infus'),
            'keterangan' => \Yii::t('fe', 'Keterangan'),
            'pemberitahu_perawat' => \Yii::t('fe', 'Pemberitahuan perawat'),
            'perawat_datang' => \Yii::t('fe', 'Perawat datang'),

        ];
    }
}