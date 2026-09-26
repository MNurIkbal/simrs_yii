<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KesehatanAnakForm extends Model
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

    public $suhu;
    public $nadi;
    public $pucat;
    public $sianosis;
    public $tonus;
    public $tugor;
    public $edema;
    public $ikterus;
    public $kepala;
    public $muka;
    public $rambut;
    public $ubunBesar;
    public $telinga;
    public $mata;
    public $hidung;
    public $bibir;
    public $lidah;
    public $selMulut;
    public $leher;
    public $bentukDada;
    public $jantung;
    public $ictus;
    public $batasKiri;
    public $batasKanan;
    public $batasAtas;
    public $irama;
    public $soufle;
    public $thrill;
    public $paruParu;
    public $pp;
    public $pr;
    public $pk;
    public $pd;
    public $kulit;
    public $gigiAtas;
    public $gigiBawah;
    public $gigiKiri;
    public $gigiKanan;
    public $caries;
    public $tenggorokan;
    public $tonsil;
    public $perut;
    public $limpa;
    public $hati;
    public $konsistensi;
    public $permikaan;
    public $kelLimfa;
    public $anggotaGerak;
    public $tasbeh;
    public $colVertebralis;
    public $pinggir;
    public $nyeriTekan;
    public $bprTprAtas;
    public $bprTprBawah;
    public $bprTprKiri;
    public $bprTprKanan;
    public $kprAprAtas;
    public $kprAprBawah;
    public $kprAprKiri;
    public $kprAprKanan;

    // lingkar kepala
    public $lingkarKepalaTanggal;
    public $lingkarKepalaUkuran;

    // lingkar dada
    public $lingkarDadaTanggal;
    public $lingkarDadaUkuran;

    // lingkar perut
    public $lingkarPerutTanggal;
    public $lingkarPerutUkuran;

    public $pemeriksaanFisik;
    public $laboratorium;
    public $diagnosaKerja;
    public $nama;
    public $alamat;
    public $noTelepon;
    public $jenisKelamin;
    public $diagnosaMedis;
    public $dikirim;
    public $bangsa;
    public $agama;
    public $tanggalLahir;
    public $beratBadan;
    public $pbtb;
    public $cukupBulan;
    public $dirumahrsrb;
    public $susahBiasa;
    public $ditolongOleh;
    public $anakKe;
    public $dari;
    public $keguguran;
    public $sex;
    public $umur;
    public $sehatSakit;
    public $karena;
    public $riwayatPenyakit;
    public $lamaPenyakit;
    public $berbalik;
    public $gigiPertama;
    public $duduk;
    public $berdiri;
    public $jalanSendiri;
    public $bicara;
    public $makan;
    public $asi;
    public $sampaiUmur;
    public $masukBerapaKali;
    public $tanggalPeriksa;
    public $jamPeriksa;
    public $tanggalMeninggal;
    public $jamMeninggal;
    public $tanggalPulang;
    public $jamPulang;
    public $kondisi;
    public $dirawatBulan;
    public $dirawatHari;
    public $dirawatJam;
    public $pindahKe;
    public $bcg1;
    public $bcg2;
    public $bcg3;
    public $bcg4;
    public $bcg5;
    public $polio1;
    public $polio2;
    public $polio3;
    public $polio4;
    public $polio5;
    public $dpt1;
    public $dpt2;
    public $dpt3;
    public $dpt4;
    public $dpt5;
    public $campak1;
    public $campak2;
    public $campak3;
    public $campak4;
    public $campak5;
    public $hepatitisb1;
    public $hepatitisb2;
    public $hepatitisb3;
    public $hepatitisb4;
    public $hepatitisb5;
    public $hepatitisa1;
    public $hepatitisa2;
    public $hepatitisa3;
    public $hepatitisa4;
    public $hepatitisa5;
    public $typhoid1;
    public $typhoid2;
    public $typhoid3;
    public $typhoid4;
    public $typhoid5;
    public $mmr1;
    public $mmr2;
    public $mmr3;
    public $mmr4;
    public $mmr5;
    public $diagnosaKode;
    public $diagnosaNama;
    public $ringkasanPenyakit;
    public $penyakitDiderita;
    public $namaAyah;
    public $namaIbu;
    public $umurAyah;
    public $umurIbu;
    public $pekerjaanAyah;
    public $pekerjaanIbu;
    public $kesehatanAyah;
    public $kesehatanIbu;
    public $kesehatanKeluargaLain;
    public $ppKanan;
    public $prKanan;
    public $pkKanan;
    public $pdKanan;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'formasesmen_code',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'suhu',
                    'nadi',
                    'pucat',
                    'sianosis',
                    'tonus',
                    'tugor',
                    'edema',
                    'ikterus',
                    'kepala',
                    'muka',
                    'rambut',
                    'ubunBesar',
                    'telinga',
                    'mata',
                    'hidung',
                    'bibir',
                    'lidah',
                    'selMulut',
                    'leher',
                    'bentukDada',
                    'jantung',
                    'batasKiri',
                    'batasKanan',
                    'batasAtas',
                    'irama',
                    'soufle',
                    'thrill',
                    'paruParu',
                    'pp',
                    'pr',
                    'pk',
                    'pd',
                    'kulit',
                    'gigiAtas',
                    'gigiBawah',
                    'gigiKiri',
                    'gigiKanan',
                    'caries',
                    'tenggorokan',
                    'tonsil',
                    'perut',
                    'limpa',
                    'hati',
                    'konsistensi',
                    'permikaan',
                    'kelLimfa',
                    'anggotaGerak',
                    'tasbeh',
                    'colVertebralis',
                    'pinggir',
                    'nyeriTekan',
                    'bprTprAtas',
                    'bprTprBawah',
                    'bprTprKiri',
                    'bprTprKanan',
                    'kprAprAtas',
                    'kprAprBawah',
                    'kprAprKiri',
                    'kprAprKanan',

                    // lingkar kepala
                    'lingkarKepalaTanggal',
                    'lingkarKepalaUkuran',

                    // lingkar dada
                    'lingkarDadaTanggal',
                    'lingkarDadaUkuran',

                    // lingkar perut
                    'lingkarPerutTanggal',
                    'lingkarPerutUkuran',

                    'pemeriksaanFisik',
                    'laboratorium',
                    'diagnosaKerja',
                    'nama',
                    'alamat',
                    'noTelepon',
                    'jenisKelamin',
                    'diagnosaMedis',
                    'dikirim',
                    'bangsa',
                    'agama',
                    'tanggalLahir',
                    'beratBadan',
                    'pbtb',
                    'cukupBulan',
                    'dirumahrsrb',
                    'susahBiasa',
                    'ditolongOleh',
                    'anakKe',
                    'dari',
                    'keguguran',
                    'sex',
                    'umur',
                    'sehatSakit',
                    'karena',
                    'riwayatPenyakit',
                    'lamaPenyakit',
                    'berbalik',
                    'gigiPertama',
                    'duduk',
                    'berdiri',
                    'jalanSendiri',
                    'bicara',
                    'makan',
                    'asi',
                    'sampaiUmur',
                    'masukBerapaKali',
                    'tanggalPeriksa',
                    'jamPeriksa',
                    'tanggalMeninggal',
                    'jamMeninggal',
                    'tanggalPulang',
                    'jamPulang',
                    'kondisi',
                    'dirawatBulan',
                    'dirawatHari',
                    'dirawatJam',
                    'pindahKe',
                    'bcg1',
                    'bcg2',
                    'bcg3',
                    'bcg4',
                    'bcg5',
                    'polio1',
                    'polio2',
                    'polio3',
                    'polio4',
                    'polio5',
                    'dpt1',
                    'dpt2',
                    'dpt3',
                    'dpt4',
                    'dpt5',
                    'campak1',
                    'campak2',
                    'campak3',
                    'campak4',
                    'campak5',
                    'hepatitisb1',
                    'hepatitisb2',
                    'hepatitisb3',
                    'hepatitisb4',
                    'hepatitisb5',
                    'hepatitisa1',
                    'hepatitisa2',
                    'hepatitisa3',
                    'hepatitisa4',
                    'hepatitisa5',
                    'typhoid1',
                    'typhoid2',
                    'typhoid3',
                    'typhoid4',
                    'typhoid5',
                    'mmr1',
                    'mmr2',
                    'mmr3',
                    'mmr4',
                    'mmr5',
                    'diagnosaKode',
                    'diagnosaNama',
                    'ringkasanPenyakit',
                    'penyakitDiderita',
                    'namaAyah',
                    'namaIbu',
                    'umurAyah',
                    'umurIbu',
                    'pekerjaanAyah',
                    'pekerjaanIbu',
                    'kesehatanAyah',
                    'kesehatanIbu',
                    'kesehatanKeluargaLain',
                    'ppKanan',
                    'prKanan',
                    'pkKanan',
                    'pdKanan',
                    'ictus'
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
            'pr' => 'PR',
            'pd' => 'PD',
            'pp' => 'PP',
            'pk' => 'PK',
            'bprTprAtas' => 'BPR TPR Atas',
            'bprTprBawah' => 'BPR TPR Bawah',
            'bprTprKiri' => 'BPR TPR Kiri',
            'bprTprKanan' => 'BPR TPR Kanan',
            'kprAprAtas' => 'KPR ATR Atas',
            'kprAprBawah' => 'KPR ATR Bawah',
            'kprAprKiri' => 'KPR ATR Kiri',
            'kprAprKanan' => 'KPR ATR Kanan',
            'colVertebralis' => 'Col. Vertebralis',
            'selMulut' => 'Sel. Mulut',
            'kelLimfa' => 'Kel. Limfa',
            'pbtb' => 'PB/TB',
            'cukupBulan' => 'Cukup/Kurang Bulan',
            'dirumahrsrb' => 'Di Rumah/RS/RB',
            'susahBiasa' => 'Susah/Biasa',
            'riwayatPenyakit' => 'Riwayat Penyakit Diinformasikan Oleh',
            'masukBerapaKali' => 'Masuk Bagian Anak disini ke',
            'tanggalPeriksa' => 'Tanggal',
            'jamPeriksa' => 'Jam',
            'tanggalMeninggal' => 'Meninggal Tanggal',
            'jamMeninggal' => 'Jam',
            'tanggalPulang' => 'Pulang Tanggal',
            'jamPulang' => 'Jam',
            'penyakitDiderita' => 'Penyakit yang pernah di derita',
            'ppKanan' => 'PP',
            'prKanan' => 'PR',
            'pkKanan' => 'PK',
            'pdKanan' => 'PD',
            'bangsa' => 'Bangsa/Suku'
        ];
    }
}
