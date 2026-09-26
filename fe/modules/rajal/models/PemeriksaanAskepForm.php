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

class PemeriksaanAskepForm extends \yii\base\Model
{

    public $nama_dokter;
    public $pegawaiperawat_id;
    public $tglperiksafisik;
    public $keadaanumum;
    public $gcs_eye;
    public $gcs_verbal;
    public $gcs_motorik;

    public $gcs_hasil_metode;
    public $kesadaran;
    public $td_systolic;
    public $td_diastolic;
    public $tekanandarah_kategori;
    public $meanarteripressure;
    public $detaknadi;
    public $denyutjantung;
    public $pernapasan;
    public $kategori_pernapasan;

    public $suhutubuh;
    public $tinggibadan_cm;
    public $beratbadan_kg;
    public $bb_ideal;
    public $imt;
    public $imt_kategori;
    public $kelainanpadabagtubuh;
    public $bodymassindex_id;
    public $gcs_is_kapitis;
    public $gcs_kategori;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'nama_dokter',
                    'pegawaiperawat_id',
                    'tglperiksafisik',
                    'keadaanumum',
                    'gcs_eye',
                    'gcs_verbal',
                    'gcs_motorik',
                    'gcs_hasil_metode',
                    'kesadaran',
                    'td_systolic',
                    'td_diastolic',
                    'tekanandarah_kategori',
                    'meanarteripressure',
                    'detaknadi',
                    'denyutjantung',
                    'pernapasan',
                    'kategori_pernapasan',
                    'suhutubuh',
                    'tinggibadan_cm',
                    'beratbadan_kg',
                    'bb_ideal',
                    'imt',
                    'imt_kategori',
                    'kelainanpadabagtubuh',
                    'bodymassindex_id',
                    'gcs_is_kapitis',
                    'gcs_kategori'
                ],
                'safe'
            ],

            // [['nadi', 'rr', 'berat_badan', 'tinggi_badan'], 'number'],
            // [['keluhan_utama', 'td'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawaiperawat_id' => Yii::t('fe', 'Perawat'),
            'keadaanumum' => Yii::t('fe', 'Keadaan Umum'),
            'kelainanpadabagtubuh' => 'Kelainan pada bagian tubuh',
            'meanarteripressure' => 'Mean Arteri Pressure',
            'td_systolic' => 'Tekanan Darah Systolic',
            'td_diastolic' => 'Tekanan Darah Diastolic',
            'tekanandarah_kategori' => 'Tekanan Darah Kategori',
            'detaknadi' => 'Detak Nadi',
            'suhutubuh' => 'Suhu Tubuh',
            'tinggibadan_cm' => 'Tinggi Badan',
            'beratbadan_kg' => 'Berat Badan',
            'bb_ideal' => 'Berat Badan Ideal',

        ];
    }
}
