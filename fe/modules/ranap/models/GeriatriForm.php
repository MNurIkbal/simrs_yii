<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class GeriatriForm extends Model
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
    public $keluhan_yang_menyertai;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_sekarang_lainnya;
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_dahulu_lainnya;
    public $riwayat_imunokompromais;
    public $riwayat_imunokompromais_lainnya;
    public $pengkajian_psikologis;
    public $pengkajian_psikologis_lainnya;
    public $respon_penerimaan_pasien;
    public $kesadaran;
    public $gcsE;
    public $gcsM;
    public $gcsV;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $pemeriksaan_fisik;
    public $adlMakanMinum;
    public $adlMandi;
    public $adlBuangAir;
    public $adlBerpakaian;
    public $adlIstirahat;
    public $adlPenggunaanObat;
    public $pola_istirahat;
    public $pola_olahraga;
    public $jenis_olahraga;
    public $frekuensi_olahraga;
    public $pola_merokok;
    public $frekuensi_merokok_1;
    public $frekuensi_merokok_2;
    public $pola_kopi;
    public $frekuensi_kopi;
    public $ketergantungan_alkohol;
    public $frekuensi_alkohol;
    public $ketergantungan_obat;
    public $jenis_obat;
    public $jenis_obat_lainnya;
    public $orientasi_pasien_keluarga;
    public $orientasi_pasien_keluarga_lainnya;
    public $informasi_pasien_keluarga;
    public $penggunaan_alat_medik;
    public $jenis_alat_medik;
    public $tgl_pasang_infus;
    public $tgl_pasang_kateter;
    public $tgl_pasang_ngt;
    public $diagnosa_keperawatan;
    public $rencana_keperawatan_dan_tindakan;
    public $pengkajian_sistem;
    public $diagnosa_medis;
    public $rencana_terapi_dan_tindakan;
    public $saran_nasehat_dokter;
    public $laboratorium;
    public $radiologi;



    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'frekuensi_nadi', 'frekuensi_nafas'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'formasesmen_code',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'keluhan_utama',
                    'keluhan_yang_menyertai',
                    'riwayat_penyakit_sekarang',
                    'riwayat_penyakit_sekarang_lainnya',
                    'riwayat_penyakit_dahulu',
                    'riwayat_penyakit_dahulu_lainnya',
                    'riwayat_imunokompromais',
                    'riwayat_imunokompromais_lainnya',
                    'pengkajian_psikologis',
                    'pengkajian_psikologis_lainnya',
                    'respon_penerimaan_pasien',
                    'kesadaran',
                    'gcsE',
                    'gcsM',
                    'gcsV',
                    'berat_badan',
                    'tinggi_badan',
                    'tekanan_darah',
                    'suhu_badan',
                    'pemeriksaan_fisik',
                    'adlMakanMinum',
                    'adlMandi',
                    'adlBuangAir',
                    'adlBerpakaian',
                    'adlIstirahat',
                    'adlPenggunaanObat',
                    'pola_istirahat',
                    'pola_olahraga',
                    'jenis_olahraga',
                    'frekuensi_olahraga',
                    'pola_merokok',
                    'frekuensi_merokok_1',
                    'frekuensi_merokok_2',
                    'pola_kopi',
                    'frekuensi_kopi',
                    'ketergantungan_alkohol',
                    'frekuensi_alkohol',
                    'ketergantungan_obat',
                    'jenis_obat',
                    'jenis_obat_lainnya',
                    'orientasi_pasien_keluarga',
                    'orientasi_pasien_keluarga_lainnya',
                    'informasi_pasien_keluarga',
                    'penggunaan_alat_medik',
                    'jenis_alat_medik',
                    'tgl_pasang_infus',
                    'tgl_pasang_kateter',
                    'tgl_pasang_ngt',
                    'diagnosa_keperawatan',
                    'rencana_keperawatan_dan_tindakan',
                    'pengkajian_sistem',
                    'diagnosa_medis',
                    'rencana_terapi_dan_tindakan',
                    'saran_nasehat_dokter',
                    'laboratorium',
                    'radiologi',
                    'status_kesehatan',
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
            'kesadaran' => 'Kesadaran',
            'gcsE' => 'E',
            'gcsM' => 'M',
            'gcsV' => 'V',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'tekanan_darah' => 'Tekanan Darah',
            'frekuensi_nadi' => 'Frekuensi Nadi',
            'frekuensi_nafas' => 'Frekuensi Nafas',
            'suhu_badan' => 'Suhu Badan',
            'adlMakanMinum' => 'Makan/Minum',
            'adlMandi' => 'Mandi',
            'adlBuangAir' => 'BAB/BAK',
            'adlBerpakaian' => 'Berpakaian/Berhias',
            'adlIstirahat' => 'Istirahat dan Tidur',
            'adlPenggunaanObat' => 'Penggunaan Obat',
        ];
    }
}
