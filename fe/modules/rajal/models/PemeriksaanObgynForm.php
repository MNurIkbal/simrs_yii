<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 09:59:09
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-26 11:57:12
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class PemeriksaanObgynForm extends \yii\base\Model
{
    public $ruangan_id;
    public $konsulpoli_id;
    public $pemeriksaan_spesialis;
    public $pemeriksaanfisik_id;
    public $pendaftaran_id;
    public $pegawaiperawat_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $tglperiksafisik;

    public $obstetric_luar_tfu;
    public $obstetric_luar_situs;
    public $obstetric_luar_punggung;
    public $obstetric_luar_bagterendah;
    public $obstetric_luar_perlimaan;
    public $obstetric_luar_his;
    public $obstetric_luar_denyutjanin;
    public $obstetric_luar_beratjanin;
    public $obstetric_inspekulo;
    public $obstetric_dalam_vulva;
    public $obstetric_dalam_portio;
    public $obstetric_dalam_pembukaan;
    public $obstetric_dalam_ketuban;
    public $obstetric_dalam_bagterendah;
    public $obstetric_dalam_ubunkecil;
    public $obstetric_dalam_penurunan;
    public $obstetric_dalam_kesanpanggul;
    public $obstetric_dalam_pelepasan;

    public $gynekology_luar_tfu;
    public $gynekology_luar_mtnt;
    public $gynekology_luar_fluksus;
    public $gynekology_inspekulo;
    public $gynekology_dalam_vulva;
    public $gynekology_dalam_portio;
    public $gynekology_dalam_oue;
    public $gynekology_dalam_uterus;
    public $gynekology_dalam_adnexa;
    public $gynekology_dalam_pelepasan;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [['pegawaiperawat_id', 'ruangan_id', 'konsulpoli_id', 'pemeriksaan_spesialis', 'tglperiksafisik',
                'obstetric_luar_tfu',
                'obstetric_luar_situs',
                'obstetric_luar_punggung',
                'obstetric_luar_bagterendah',
                'obstetric_luar_perlimaan',
                'obstetric_luar_his',
                'obstetric_luar_denyutjanin',
                'obstetric_luar_beratjanin',
                'obstetric_inspekulo',
                'obstetric_dalam_vulva',
                'obstetric_dalam_portio',
                'obstetric_dalam_pembukaan',
                'obstetric_dalam_ketuban',
                'obstetric_dalam_bagterendah',
                'obstetric_dalam_ubunkecil',
                'obstetric_dalam_penurunan',
                'obstetric_dalam_kesanpanggul',
                'obstetric_dalam_pelepasan',

                'gynekology_luar_tfu',
                'gynekology_luar_mtnt',
                'gynekology_luar_fluksus',
                'gynekology_inspekulo',
                'gynekology_dalam_vulva',
                'gynekology_dalam_portio',
                'gynekology_dalam_oue',
                'gynekology_dalam_uterus',
                'gynekology_dalam_adnexa',
                'gynekology_dalam_pelepasan'],
            'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'obstetric_luar_tfu' => Yii::t('fe', 'TFU'),
            'obstetric_luar_situs' => Yii::t('fe', 'Situs'),
            'obstetric_luar_punggung' => Yii::t('fe', 'Punggung'),
            'obstetric_luar_bagterendah' => Yii::t('fe', 'Bagian Terendah'),
            'obstetric_luar_perlimaan' => Yii::t('fe', 'Perlimaan'),
            'obstetric_luar_his' => Yii::t('fe', 'HIS'),
            'obstetric_luar_denyutjanin' => Yii::t('fe', 'Denyut Jantung Janin'),
            'obstetric_luar_beratjanin' => Yii::t('fe', 'Taksiran Berat Janin'),
            'obstetric_inspekulo' => Yii::t('fe', 'Inspekulo'),
            'obstetric_dalam_vulva' => Yii::t('fe', 'Vulva / Vagina'),
            'obstetric_dalam_portio' => Yii::t('fe', 'Portio'),
            'obstetric_dalam_pembukaan' => Yii::t('fe', 'Pembukaan'),
            'obstetric_dalam_ketuban' => Yii::t('fe', 'Ketuban'),
            'obstetric_dalam_bagterendah' => Yii::t('fe', 'Bagian Terendah'),
            'obstetric_dalam_ubunkecil' => Yii::t('fe', 'Ubun-ubun kecil'),
            'obstetric_dalam_penurunan' => Yii::t('fe', 'Penurunan'),
            'obstetric_dalam_kesanpanggul' => Yii::t('fe', 'Kesan Panggul'),
            'obstetric_dalam_pelepasan' => Yii::t('fe', 'Pelepasan'),

            'gynekology_luar_tfu' => Yii::t('fe', 'TFU'),
            'gynekology_luar_mtnt' => Yii::t('fe', 'MT/NT'),
            'gynekology_luar_fluksus' => Yii::t('fe', 'Fluksus'),
            'gynekology_inspekulo' => Yii::t('fe', 'Inspekulo'),
            'gynekology_dalam_vulva' => Yii::t('fe', 'Vulva / Vagina'),
            'gynekology_dalam_portio' => Yii::t('fe', 'Portio'),
            'gynekology_dalam_oue' => Yii::t('fe', 'OUE/OUI'),
            'gynekology_dalam_uterus' => Yii::t('fe', 'Uterus'),
            'gynekology_dalam_adnexa' => Yii::t('fe', 'Adnexa / Cavum Dauglasi'),
            'gynekology_dalam_pelepasan' => Yii::t('fe', 'Pelepasan'),
        ];
    }
}
