<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class PsikiatrisForm extends Model
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
    public $status_kesehatan;
    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_sekarang_lainnya;
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_dahulu_lainnya;
    public $gangguan_jiwa;
    public $gangguan_jiwa_lainnya;
    public $penganiayaan_fisik;
    public $penganiayaan_fisik_lainnya;
    public $penganiayaan_seksual;
    public $penganiayaan_seksual_lainnya;
    public $kekerasan;
    public $kekerasan_lainnya;
    public $gangguan_konsep;
    public $gangguan_sosial;
    public $gangguan_sosial_lainnya;
    public $penampilan;
    public $pembicaraan;
    public $aktifitas_motorik;
    public $alam_perasaan;
    public $alam_perasaan_lainnya;
    public $afek;
    public $interaksi;
    public $persepsi;
    public $proses_pikir;
    public $isi_pikir;
    public $tingkat_kesadaran;
    public $disorientasi;
    public $memori;
    public $tingkat_konsentrasi;
    public $kemampuan_penilaian;
    public $daya_tilik;
    public $tinggi_badan;
    public $berat_badan;
    public $bb_naik;
    public $keluhan_fisik;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_tubuh;
    public $adl_makan_minum;
    public $adl_mandi;
    public $adl_bab;
    public $adl_berpakaian;
    public $adl_istirahat;
    public $adl_obat;
    public $adaptif;
    public $maladaptif;
    public $diagnosa_keperawatan;
    public $rencana_keperawatan;
    public $pengkajian_sistem;
    public $saran_dokter;
    public $laboratorium;
    public $radiologi;

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
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',
                'keluhan_utama',
                'status_kesehatan',
                'riwayat_penyakit_sekarang',
                'riwayat_penyakit_sekarang_lainnya',
                'riwayat_penyakit_dahulu',
                'riwayat_penyakit_dahulu_lainnya',
                'gangguan_jiwa',
                'penganiayaan_fisik',
                'gangguan_jiwa_lainnya',
                'penganiayaan_fisik_lainnya',
                'penganiayaan_seksual',
                'penganiayaan_seksual_lainnya',
                'kekerasan',
                'kekerasan_lainnya',
                'gangguan_konsep',
                'gangguan_sosial',
                'gangguan_sosial_lainnya',
                'penampilan',
                'pembicaraan',
                'aktifitas_motorik',
                'alam_perasaan',
                'alam_perasaan_lainnya',
                'afek',
                'interaksi',
                'persepsi',
                'proses_pikir',
                'isi_pikir',
                'tingkat_kesadaran',
                'disorientasi',
                'memori',
                'tingkat_konsentrasi',
                'kemampuan_penilaian',
                'daya_tilik',
                'tinggi_badan',
                'berat_badan',
                'bb_naik',
                'keluhan_fisik',
                'tekanan_darah',
                'frekuensi_nadi',
                'frekuensi_nafas',
                'suhu_tubuh',
                'adl_makan_minum',
                'adl_mandi',
                'adl_bab',
                'adl_berpakaian',
                'adl_istirahat',
                'adl_obat',
                'adaptif',
                'maladaptif',
                'diagnosa_keperawatan',
                'rencana_keperawatan',
                'pengkajian_sistem',
                'saran_dokter',
                'laboratorium',
                'radiologi',
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }
}
