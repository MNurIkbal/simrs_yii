<?php

namespace app\modules\gizi\models;

use Yii;

class AsuhanGiziForm extends \yii\base\Model
{
    public $asuhangizi_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;

    public $alergi_makanan;
    public $pantangan_makanan;
    public $ketidaksukaan_makanan;
    public $pengalaman_diet;
    public $pengalaman_diet_desc;
    public $catatan;

    public $bb_saatini;
    public $pb_tb;
    public $bb_biasanya;
    public $imt;
    public $status_gizi;
    public $penurunan_bb;
    public $kurun_waktu;
    public $pengukuran_lainnya;

    public $biokimia;
    public $prosedur;

    public $antropi_otot_lengan;
    public $udem;
    public $hilang_lemak_subkutan;
    public $nafsu_makan;
    public $mual;
    public $muntah;
    public $kembung;
    public $konstipasi;
    public $diare;
    public $gangguan_menelan;
    public $gangguan_mengunyah;
    public $gangguan_menghisap;
    public $kulit;
    public $kepala_dan_mata;
    public $gigi_geligi;
    public $tekanan_darah_mm;
    public $tekanan_darah_hg;
    public $tekanan_darah_mmhg;
    public $tekanan_darah_kondisi;
    public $detak_nadi;
    public $denyut_jantung;
    public $pernapasan;
    public $suhu_tubuh;
    public $data_lain;

    public $diagnosa_gizi;

    public $tujuan;
    public $materi;
    public $sasaran;
    public $preskripsi_diet;
    public $jenis_diet;
    public $rute;
    public $edukasi_gizi;
    public $media;
    public $target_intervensi;

    public $rencana_evaluasi;

    public $label_imt;
    public $label_status_gizi;
    public $label_penurunan_bb;
    public $label_tekanan_darah_mmhg;
    public $label_tekanan_darah_kondisi;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'bb_saatini',
                    'pb_tb',
                    'bb_biasanya',
                    'kurun_waktu',
                    'tekanan_darah_mm',
                    'tekanan_darah_hg',
                    'detak_nadi',
                    'denyut_jantung',
                    'pernapasan',
                    'suhu_tubuh',
                    'diagnosa_gizi',
                    'tujuan',
                    'materi',
                    'sasaran',
                    'preskripsi_diet',
                    'jenis_diet',
                    'rute',
                    'edukasi_gizi',
                    'media',
                    'rencana_evaluasi',
                ], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'asuhangizi_id',
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pengukuran_lainnya',
                    'alergi_makanan',
                    'pantangan_makanan',
                    'ketidaksukaan_makanan',
                    'pengalaman_diet',
                    'pengalaman_diet_desc',
                    'catatan',
                    'biokimia',
                    'prosedur',
                    'antropi_otot_lengan',
                    'udem',
                    'hilang_lemak_subkutan',
                    'nafsu_makan',
                    'mual',
                    'muntah',
                    'kembung',
                    'konstipasi',
                    'diare',
                    'gangguan_menelan',
                    'gangguan_mengunyah',
                    'gangguan_menghisap',
                    'kulit',
                    'kepala_dan_mata',
                    'gigi_geligi',
                    'data_lain',
                    'tekanan_darah_mmhg',
                    'label_tekanan_darah_mmhg',
                    'tekanan_darah_kondisi',
                    'label_tekanan_darah_kondisi',
                    'imt',
                    'label_imt',
                    'status_gizi',
                    'label_status_gizi',
                    'penurunan_bb',
                    'label_penurunan_bb',
                    'target_intervensi',
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
            'alergi_makanan'=>Yii::t('fe', 'Alergi makanan'),
            'pantangan_makanan'=>Yii::t('fe', 'Pantangan makanan'),
            'ketidaksukaan_makanan'=>Yii::t('fe', 'Ketidaksukaan makanan'),
            'pengalaman_diet'=>Yii::t('fe', 'Pengalaman diet'),
            'pengalaman_diet_desc'=>Yii::t('fe', 'Pengalaman diet desc'),
            'catatan'=>Yii::t('fe', 'Catatan'),
            'bb_saatini'=>Yii::t('fe', 'BB saat ini'),
            'pb_tb'=>Yii::t('fe', 'PB / TB'),
            'bb_biasanya'=>Yii::t('fe', 'BB biasanya'),
            'imt'=>Yii::t('fe', 'IMT'),
            'label_imt'=>Yii::t('fe', 'IMT'),
            'penurunan_bb'=>Yii::t('fe', 'penurunan BB'),
            'label_penurunan_bb'=>Yii::t('fe', 'penurunan BB'),
            'kurun_waktu'=>Yii::t('fe', 'Kurun waktu'),
            'status_gizi'=>Yii::t('fe', 'Status gizi'),
            'label_status_gizi'=>Yii::t('fe', 'Status gizi'),
            'pengukuran_lainnya'=>Yii::t('fe', 'Pengukuran lainnya'),
            'biokimia'=>Yii::t('fe', 'Biokimia'),
            'prosedur'=>Yii::t('fe', 'Prosedur'),
            'antropi_otot_lengan'=>Yii::t('fe', 'Antropi otot lengan'),
            'udem'=>Yii::t('fe', 'Udem'),
            'hilang_lemak_subkutan'=>Yii::t('fe', 'Hilang lemak subkutan'),
            'nafsu_makan'=>Yii::t('fe', 'Nafsu makan'),
            'mual'=>Yii::t('fe', 'Mual'),
            'muntah'=>Yii::t('fe', 'Muntah'),
            'kembung'=>Yii::t('fe', 'Kembung'),
            'konstipasi'=>Yii::t('fe', 'Konstipasi'),
            'diare'=>Yii::t('fe', 'Diare'),
            'gangguan_menelan'=>Yii::t('fe', 'Gangguan menelan'),
            'gangguan_mengunyah'=>Yii::t('fe', 'Gangguan mengunyah'),
            'gangguan_menghisap'=>Yii::t('fe', 'Gangguan_menghisap'),
            'kulit'=>Yii::t('fe', 'Kulit'),
            'kepala_dan_mata'=>Yii::t('fe', 'Kepala dan mata'),
            'gigi_geligi'=>Yii::t('fe', 'Gigi geligi'),
            'tekanan_darah_mm'=>Yii::t('fe', 'Mm'),
            'tekanan_darah_hg'=>Yii::t('fe', 'Hg'),
            'tekanan_darah_mmhg'=>Yii::t('fe', 'Tekanan darah mmhg'),
            'label_tekanan_darah_mmhg'=>Yii::t('fe', 'Tekanan darah mmhg'),
            'tekanan_darah_kondisi'=>Yii::t('fe', 'Tekanan darah kondisi'),
            'label_tekanan_darah_kondisi'=>Yii::t('fe', 'Tekanan darah kondisi'),
            'detak_nadi'=>Yii::t('fe', 'Detak nadi'),
            'denyut_jantung'=>Yii::t('fe', 'Denyut jantung'),
            'pernapasan'=>Yii::t('fe', 'Pernapasan'),
            'suhu_tubuh'=>Yii::t('fe', 'Suhu tubuh'),
            'data_lain'=>Yii::t('fe', 'Data lain'),
            'diagnosa_gizi'=>Yii::t('fe', 'Diagnosa gizi'),
            'tujuan'=>Yii::t('fe', 'Tujuan'),
            'materi'=>Yii::t('fe', 'Materi'),
            'sasaran'=>Yii::t('fe', 'Sasaran'),
            'preskripsi_diet'=>Yii::t('fe', 'Preskripsi diet'),
            'jenis_diet'=>Yii::t('fe', 'Jenis diet'),
            'rute'=>Yii::t('fe', 'Rute'),
            'edukasi_gizi'=>Yii::t('fe', 'Edukasi gizi'),
            'media'=>Yii::t('fe', 'Media'),
            'rencana_evaluasi'=>Yii::t('fe', 'Rencana evaluasi'),
            'target_intervensi'=>Yii::t('fe', 'Target intervensi'),
        ];
    }

}