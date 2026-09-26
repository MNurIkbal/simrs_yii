<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class LukaBakarForm extends Model
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

    public $tipe_luka;
    public $riwayat_luka;
    public $faktor_penghambat;
    public $lokasi_luka;
    public $persentase_luka;
    public $panjang_luka;
    public $lebar_luka;
    public $kedalaman_luka;
    public $lokasi_goa;
    public $panjang_goa;
    public $lokasi_sinus;
    public $panjang_sinus;
    public $hitam;
    public $kuning;
    public $merah;
    public $pink;
    public $terluka;
    public $kulit_luka;
    public $stadium_luka;
    public $tanda_infeksi;
    public $nyeri_luka;
    public $jumlah_exudate;
    public $warna_exudate;
    public $bau_exudate;
    public $pembersihan_luka;
    public $catatan_luka;
    public $tanggal_assesmen;
    public $pegawai_assesmen;
    public $pegawai_assesmen_nama;

    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
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
                    'tipe_luka',
                    'riwayat_luka',
                    'faktor_penghambat',
                    'lokasi_luka',
                    'persentase_luka',
                    'panjang_luka',
                    'lebar_luka',
                    'kedalaman_luka',
                    'lokasi_goa',
                    'panjang_goa',
                    'lokasi_sinus',
                    'panjang_sinus',
                    'hitam',
                    'kuning',
                    'merah',
                    'pink',
                    'terluka',
                    'kulit_luka',
                    'stadium_luka',
                    'tanda_infeksi',
                    'nyeri_luka',
                    'jumlah_exudate',
                    'warna_exudate',
                    'bau_exudate',
                    'pembersihan_luka',
                    'catatan_luka',
                    'tanggal_assesmen',
                    'pegawai_assesmen',
                    'pegawai_assesmen_nama'
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
            
        ];
    }
}
