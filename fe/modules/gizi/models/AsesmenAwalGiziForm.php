<?php

namespace app\modules\gizi\models;

use Yii;

class AsesmenAwalGiziForm extends \yii\base\Model
{
    public $asesmenawalgizi_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;

    public $sumberdata;
    public $sumberdata_dari;

    public $bb_biasanya;
    public $bb_saatini;
    public $perubahan_kg;
    public $perubahan_kg_data;
    public $perubahan_persen;
    public $perubahan_persen_data;
    public $perubahan_hasil;
    public $perubahan_hasil_nama;
    public $kategori_bb;

    public $asupanmkn;
    public $kategori_asupanmkn;

    public $gastrointestinal_mual;
    public $gastrointestinal_muntah;
    public $gastrointestinal_diare;
    public $gastrointestinal_anoreksia;
    public $kategori_gastrointestinal;

    public $fungsional;
    public $kategori_fungsional;

    public $diagnosa_medis;
    public $keb_metabolik;
    public $kategori_hubungan;

    public $fisik_lemak;
    public $fisik_otot;
    public $fisik_udem;
    public $fisik_asites;
    public $kategori_fisik;

    public $penilaian_sga;

    public $diet;
    public $pagt;
    public $saran_terapi;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'sumberdata',
                    'bb_biasanya',
                    'bb_saatini',
                    'perubahan_kg',
                    'perubahan_persen',
                    'perubahan_hasil_nama',
                    'kategori_bb',
                    'asupanmkn',
                    'kategori_asupanmkn',
                    'gastrointestinal_mual',
                    'gastrointestinal_muntah',
                    'gastrointestinal_diare',
                    'gastrointestinal_anoreksia',
                    'kategori_gastrointestinal',
                    'fungsional',
                    'kategori_fungsional',
                    'diagnosa_medis',
                    'keb_metabolik',
                    'kategori_hubungan',
                    'kategori_fisik',
                    'penilaian_sga',
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                ['sumberdata_dari'],
                'required',
                'when' => function($model) {
                    return $model->sumberdata == 2;
                }
            ],
            [
                [
                    'asesmenawalgizi_id',
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'fisik_lemak',
                    'fisik_otot',
                    'fisik_udem',
                    'fisik_asites',
                    'diet',
                    'pagt',
                    'saran_terapi',
                    'perubahan_hasil',
                    'sumberdata_dari',
                    'perubahan_persen_data',
                    'perubahan_kg_data',
                ],
                'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asesmenawalgizi_id'=> Yii::t('fe', 'asesmenawalgizi_id'),
            'pendaftaran_id'=> Yii::t('fe', 'pendaftaran_id'),
            'pasienadmisi_id'=> Yii::t('fe', 'pasienadmisi_id'),
            'sumberdata'=> Yii::t('fe', 'Sumber data'),
            'sumberdata_dari'=> Yii::t('fe', 'sumber data dari'),
            'bb_biasanya'=> Yii::t('fe', 'BB biasanya'),
            'bb_saatini'=> Yii::t('fe', 'BB saat ini'),
            'perubahan_kg'=> Yii::t('fe', 'Perubahan'),
            'perubahan_kg_data'=> Yii::t('fe', 'Perubahan'),
            'perubahan_persen'=> Yii::t('fe', 'Perubahan persen'),
            'perubahan_persen_data'=> Yii::t('fe', 'Perubahan persen'),
            'perubahan_hasil_nama'=> Yii::t('fe', 'Hasil perubahan'),
            'kategori_bb'=> Yii::t('fe', 'Kategori'),
            'asupanmkn'=> Yii::t('fe', 'Perubahan asupan makanan'),
            'kategori_asupanmkn'=> Yii::t('fe', 'Kategori'),
            'gastrointestinal_mual'=> Yii::t('fe', 'Mual'),
            'gastrointestinal_muntah'=> Yii::t('fe', 'Muntah'),
            'gastrointestinal_diare'=> Yii::t('fe', 'Diare'),
            'gastrointestinal_anoreksia'=> Yii::t('fe', 'Anoreksia'),
            'kategori_gastrointestinal'=> Yii::t('fe', 'Kategori'),
            'fungsional'=> Yii::t('fe', 'Perubahan kapasitas fungsional'),
            'kategori_fungsional'=> Yii::t('fe', 'Kategori'),
            'diagnosa_medis'=> Yii::t('fe', 'Diagnosa medis'),
            'keb_metabolik'=> Yii::t('fe', 'Hubungan dgn kebutuhan metabolik (stress)'),
            'kategori_hubungan'=> Yii::t('fe', 'Kategori'),
            'fisik_lemak'=> Yii::t('fe', 'Hilang lemak subkutan (trisep, dada)'),
            'fisik_otot'=> Yii::t('fe', 'Hilang massa otot (selangka, scaptula/tulang belikat, tulang rusuk, betis)'),
            'fisik_udem'=> Yii::t('fe', 'Udem'),
            'fisik_asites'=> Yii::t('fe', 'Asites'),
            'kategori_fisik'=> Yii::t('fe', 'Kategori'),
            'penilaian_sga'=> Yii::t('fe', 'Penilaian SGA'),
            'diet'=> Yii::t('fe', 'Diet'),
            'pagt'=> Yii::t('fe', 'PAGT'),
            'saran_terapi'=> Yii::t('fe', 'Saran terapi'),
        ];
    }

}