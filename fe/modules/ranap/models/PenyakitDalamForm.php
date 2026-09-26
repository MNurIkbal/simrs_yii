<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class PenyakitDalamForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $formasesmen_code;
    public $pemeriksaan_spesialis;
    public $is_dokumen_eklaim;
    public $asesmenmedis_id;

    public $isRujukan;
    public $rujukanDari;
    public $datangTanpaRujukan;
    public $keteranganDatangSendiri;
    public $keteranganDiantar;
    public $keteranganRs;
    public $keteranganPuskesmas;
    public $keteranganDokter;
    public $keteranganLainnya;
    public $diagnosaRujukan;
    public $isRiwayatAlergi;
    public $keteranganRiwayatAlergi;
    public $dokterPemeriksa;
    public $keluhanUtama;
    public $riwayatPenyakitSekarang;
    public $riwayatPenyakitDahulu;
    public $riwayatPenyakitDahuluLainnya;
    public $isPernahDirawat;
    public $isPernahDirawatKapan;
    public $isPernahDirawatDimana;
    public $isPernahDirawatDiagnosa;
    public $namaObat;
    public $dosis;
    public $waktuPenggunaan;
    public $riwayatPenyakitKeluarga;
    public $riwayatPenyakitKeluargaLainnya;
    public $riwayatPenyakitSosial;
    public $riwayatPenyakitSosialLainnya;
    public $isNyeri;
    public $lokasiNyeri;
    public $intensitasNyeri;
    public $jenisNyeri;
    public $metodeNyeri;
    public $skorNyeri;
    public $keadaanUmum;
    public $gizi;
    public $gcsE;
    public $gcsV;
    public $gcsM;
    public $isTindakanResusitasi;
    public $beratBadan;
    public $tinggiBadan;
    public $tensi;
    public $nadi;
    public $respirasi;
    public $suhu;
    public $rektal;
    public $anemis;
    public $pupil;
    public $ikterus;
    public $diameter;
    public $udemPalpabrae;
    public $tonsil;
    public $lidah;
    public $faring;
    public $bibir;
    public $jvp;
    public $pembesaranKelenjar;
    public $pembesaranKelenjarLainnya;
    public $kakuDuduk;
    public $kakuDudukLainnya;
    public $thorax;
    public $thoraxLainnya;
    public $corS1S2;
    public $corIsReguler;
    public $corMurmur;
    public $corLainLain;
    public $suaraNafas;
    public $rinchi;
    public $rinchiLainnya;
    public $wheezing;
    public $wheezingLainnya;
    public $distended;
    public $meteorismus;
    public $peristaltik;
    public $asites;
    public $nyeriTekanan;
    public $nyeriTekananLokasi;
    public $hepar;
    public $lien;
    public $extermitas;
    public $udem;
    public $udemLainnya;
    public $lainLain;
    public $diagnosaKerja;
    public $laboratorium;
    public $ekg;
    public $xray;
    public $terapiTindakan;
    public $rencanaKerja;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [[
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'keluhan_utama',
                'riwayat_penyakit_sekarang',
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',

                'isRujukan',
                'rujukanDari',
                'datangTanpaRujukan',
                'keteranganDatangSendiri',
                'keteranganDiantar',
                'keteranganRs',
                'keteranganPuskesmas',
                'keteranganDokter',
                'keteranganLainnya',
                'diagnosaRujukan',
                'isRiwayatAlergi',
                'keteranganRiwayatAlergi',
                'dokterPemeriksa',
                'keluhanUtama',
                'riwayatPenyakitSekarang',
                'riwayatPenyakitDahulu',
                'riwayatPenyakitDahuluLainnya',
                'isPernahDirawat',
                'isPernahDirawatKapan',
                'isPernahDirawatDimana',
                'isPernahDirawatDiagnosa',
                'namaObat',
                'dosis',
                'waktuPenggunaan',
                'riwayatPenyakitKeluarga',
                'riwayatPenyakitKeluargaLainnya',
                'riwayatPenyakitSosial',
                'riwayatPenyakitSosialLainnya',
                'isNyeri',
                'lokasiNyeri',
                'intensitasNyeri',
                'jenisNyeri',
                'metodeNyeri',
                'skorNyeri',
                'keadaanUmum',
                'gizi',
                'gcsE',
                'gcsV',
                'gcsM',
                'isTindakanResusitasi',
                'beratBadan',
                'tinggiBadan',
                'tensi',
                'nadi',
                'respirasi',
                'suhu',
                'rektal',
                'anemis',
                'pupil',
                'ikterus',
                'diameter',
                'udemPalpabrae',
                'tonsil',
                'lidah',
                'faring',
                'bibir',
                'jvp',
                'pembesaranKelenjar',
                'pembesaranKelenjarLainnya',
                'kakuDuduk',
                'kakuDudukLainnya',
                'thorax',
                'thoraxLainnya',
                'corS1S2',
                'corIsReguler',
                'corMurmur',
                'corLainLain',
                'suaraNafas',
                'rinchi',
                'rinchiLainnya',
                'wheezing',
                'wheezingLainnya',
                'distended',
                'meteorismus',
                'peristaltik',
                'asites',
                'nyeriTekanan',
                'nyeriTekananLokasi',
                'hepar',
                'lien',
                'extermitas',
                'udem',
                'udemLainnya',
                'lainLain',
                'diagnosaKerja',
                'laboratorium',
                'ekg',
                'xray',
                'terapiTindakan',
                'rencanaKerja',
                
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'isRujukan' => Yii::t('fe', 'Rujukan'),
        ];
    }
}
