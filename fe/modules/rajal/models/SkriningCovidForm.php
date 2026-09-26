<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */

namespace app\modules\rajal\models;

use Yii;

class SkriningCovidForm extends \yii\base\Model
{

    public $skrining_covid_id;
    public $pendaftaran_id;
    public $demam;
    public $batuk_pilek_nyeri_tenggorokan;
    public $sesak_napas;
    public $riwayat_luar_negeri;
    public $riwayat_dalam_negeri;
    public $resiko_kontak_pasien_covid;
    public $kontak_tatap_muka;
    public $kontak_fisik;
    public $kontak_perawatan_tanpa_apd;
    public $swab_positif;
    public $tgl_swab_positif;
    public $swab_negatif;
    public $tgl_swab_negatif;
    public $suspek;
    public $terkonfirmasi;
    public $petugas_pemeriksa;

    public $is_deleted;
    public $additional_data;
    public $kesimpulan_suspek;
    public $kesimpulan_kontak_erat;
    public $kesimpulan_terkonfirmasi;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infokunjunganrj_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'demam', 'batuk_pilek_nyeri_tenggorokan', 'sesak_napas', 
                    'riwayat_luar_negeri', 'riwayat_dalam_negeri', 'resiko_kontak_pasien_covid',
                    'kontak_tatap_muka', 'kontak_fisik', 'kontak_perawatan_tanpa_apd', 
                    'swab_positif','swab_negatif'
                ], 
                'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [['skrining_covid_id','demam','batuk_pilek_nyeri_tenggorokan','sesak_napas','riwayat_luar_negeri','riwayat_dalam_negeri','resiko_kontak_pasien_covid',
                'kontak_tatap_muka','kontak_fisik','kontak_perawatan_tanpa_apd','swab_positif','tgl_swab_positif','swab_negatif','tgl_swab_negatif','suspek','terkonfirmasi','petugas_pemeriksa'], 'default', 'value' => null],
            [['skrining_covid_id','demam','batuk_pilek_nyeri_tenggorokan','sesak_napas','riwayat_luar_negeri','riwayat_dalam_negeri','resiko_kontak_pasien_covid',
                'kontak_tatap_muka','kontak_fisik','kontak_perawatan_tanpa_apd','swab_positif','swab_negatif', 'is_deleted'], 'boolean'],
            [['skrining_covid_id','pendaftaran_id','demam','batuk_pilek_nyeri_tenggorokan','sesak_napas','riwayat_luar_negeri','riwayat_dalam_negeri','resiko_kontak_pasien_covid',
            'kontak_tatap_muka','kontak_fisik','kontak_perawatan_tanpa_apd','swab_positif','tgl_swab_positif','swab_negatif','tgl_swab_negatif','suspek','terkonfirmasi','petugas_pemeriksa','kesimpulan_suspek','kesimpulan_kontak_erat','kesimpulan_terkonfirmasi','additional_data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'skrining_covid_id' => 'Skrining Covid Id',
            'pendaftaran_id' => 'Pendaftaran Id',
            //  GEJALA
            'demam' => 'Demam ≥38 /riwayat demam',
            'batuk_pilek_nyeri_tenggorokan' => 'Batuk/Pilek/nyeri tenggerokan',
            'sesak_napas' => 'Sesak nafas/Pneumonia (ringan-berat)',
            // FAKTOR RESIKO
            'riwayat_luar_negeri' => 'Memiliki riwayat perjalanan 14 hari terakhir atau tinggal di luar negeri yang melaporkan transmisi lokal',
            'riwayat_dalam_negeri' => 'Memiliki riwayat perjalanan 14 hari terakhir atau tinggal di area transmisi lokal di Indonesia',
            'resiko_kontak_pasien_covid' => 'Pada 14 hari terakhir sebelum timbul gejala memiliki riwayat kontak dengan kasus konfirmasi atau probable COVID-19',
            // KONTRAK ERAT
            'kontak_tatap_muka' => 'Kontak tatap muka/berdekatan dengan kasus probable/konfirmasi dalam radius 1 meter dan kurun waktu ≥ 15 menit',
            'kontak_fisik' => 'Sentuhan fisik langsung dengan kasus probable/konfirmasi (seperti bersalaman,berpegangan tangan dan lain-lain)',
            'kontak_perawatan_tanpa_apd' => 'Orang yang memberikan perawatan langsung pada kasus probable/konfirmasi tanpa menggunakan APD yang sesuai standar',
            // HASIL SWAB
            'swab_positif' => 'Pernah melakukan swab (PCR/TCM) 14 hari terakhir dengan hasil <b>positif</b> Jika YA,Tanggal berapa',
            'tgl_swab_positif' => 'Tanggal Swab Positif',
            'swab_negatif' => 'Pernah melakukan swab (PCR/TCM) 14 hari terakhir dengan hasil <b>Negatif</b> Jika YA, Tanggal berapa',
            'tgl_swab_negatif' => 'Tanggal Swab Negatif',
            'suspek' => 'SUSPEK',
            'terkonfirmasi' => 'Terkonfirmasi',
            'petugas_pemeriksa' => 'Petugas Pemeriksa',
            'is_deleted' => 'Is Deleted',
            'additional_data' => 'Additional Data',
            'kesimpulan_suspek' => 'SUSPEK',
            'kesimpulan_kontak_erat' => 'KONTAK ERAT',
            'kesimpulan_terkonfirmasi' => 'TERKONFIRMASI',
        ];
    }
}
