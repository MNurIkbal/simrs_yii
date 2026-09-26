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

class PemeriksaanFisikForm extends \yii\base\Model
{

    public $pemeriksaanfisik_id;
    public $gcs_id;
    public $pendaftaran_id;
    public $pegawaiperawat_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $klasifikasitekanandarah_id;
    public $tglperiksafisik;
    public $keadaanumum;
    public $inspeksi;
    public $palpasi;
    public $perkusi;
    public $auskultasi;
    public $tekanandarah;
    public $td_systolic;
    public $td_diastolic;
    public $meanarteripressure;
    public $detaknadi;
    public $heartindex_i1;
    public $heartindex_i2;
    public $heartindex_i3;
    public $suhutubuh;
    public $beratbadan_kg;
    public $tinggibadan_cm;
    public $bb_ideal;
    public $pernapasan;
    public $paramedis_nama;
    public $kelainanpadabagtubuh;
    public $jn_paten;
    public $jn_obstruktifpartial;
    public $jn_obstruktifnormal;
    public $jn_stridor;
    public $pgd_simetri;
    public $pgd_asimetri;
    public $pgp_normal;
    public $pgp_kussmaul;
    public $pgp_takipnea;
    public $pgp_retraktif;
    public $pgp_dangkal;
    public $sirkulasi_nadicarotis;
    public $sirkulasi_nadiradialis;
    public $cfr_kecil_2;
    public $cfr_besar_2;
    public $jn_gargling;
    public $kulit_normal;
    public $kulit_jaundice;
    public $kulit_cyanosis;
    public $kulit_pucat;
    public $kulit_berkeringat;
    public $akral;
    public $gcs_eye;
    public $gcs_verbal;
    public $gcs_motorik;
    public $lila;
    public $lingkarpinggang;
    public $lingkarpinggul;
    public $teballemak;
    public $tinggilutut;
    public $denyutjantung;
    public $lingkarperut_cm;
    public $bentukbadan;
    public $mata_persepsiwarna;
    public $mata_visus_od;
    public $mata_visus_os;
    public $mata_penglihatanjauh;
    public $mata_kelainan;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    // additional attributes
    public $pegawaidokter_id;
    public $gcs_hasil_metode;
    public $gcs_is_kapitis;
    public $gcs_kategori;
    public $tekanandarah_1;
    public $tekanandarah_2;
    public $imt;
    public $bodymassindex_id;
    public $imt_kategori;
    public $meanarteripressurekategori;
    public $tekanandarah_kategori;
    public $nama_dokter;
    public $kategori_pernapasan;
    public $kesadaran;

    public $data_anatomi;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gcs_id', 'pendaftaran_id', 'pegawaiperawat_id', 'pasienadmisi_id', 'pasien_id', 'klasifikasitekanandarah_id', 'td_systolic', 'td_diastolic', 'detaknadi', 'pernapasan', 'suhutubuh', 'heartindex_i1', 'heartindex_i2', 'heartindex_i3', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','gcs_hasil_metode'], 'default', 'value' => null],
            [['gcs_id', 'pendaftaran_id', 'pegawaiperawat_id', 'pasienadmisi_id', 'pasien_id', 'klasifikasitekanandarah_id', 'heartindex_i1', 'heartindex_i2', 'heartindex_i3', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','gcs_hasil_metode'], 'integer'],
            [['pendaftaran_id', 'pasien_id', 'tglperiksafisik','nama_dokter', 'td_systolic', 'td_diastolic'], 'required'],
            [['tglperiksafisik', 'created_date', 'last_modified_date', 'deleted_date','gcs_is_kapitis', 'bodymassindex_id', 
            'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis', 'kelainanpadabagtubuh'], 'safe'],
            [['meanarteripressure', 'beratbadan_kg', 'tinggibadan_cm', 'bb_ideal', 'lila', 'lingkarpinggang', 'lingkarpinggul', 'teballemak', 'tinggilutut', 'lingkarperut_cm', 'mata_visus_od', 'mata_visus_os', 'td_systolic', 'td_diastolic', 'detaknadi', 'pernapasan', 'suhutubuh'], 'number'],
            [['additional_data'], 'string'],
            [['jn_paten', 'jn_obstruktifpartial', 'jn_obstruktifnormal', 'jn_stridor', 'pgd_simetri', 'pgd_asimetri', 'pgp_normal', 'pgp_kussmaul', 'pgp_takipnea', 'pgp_retraktif', 'pgp_dangkal', 'cfr_kecil_2', 'cfr_besar_2', 'jn_gargling', 'kulit_normal', 'kulit_jaundice', 'kulit_cyanosis', 'kulit_pucat', 'kulit_berkeringat', 'is_deleted', 'is_active'], 'boolean'],
            [['keadaanumum', 'inspeksi', 'palpasi', 'perkusi', 'auskultasi', 'paramedis_nama'], 'string', 'max' => 500],
            [['tekanandarah'], 'string', 'max' => 20],
            [['kelainanpadabagtubuh', 'kategori_pernapasan', 'kesadaran'], 'string', 'max' => 30],
            [['akral'], 'string', 'max' => 200],
            // [['td_systolic'], 'integer', 'max' => 260],
            // [['td_diastolic'], 'integer', 'max' => 260],
            [['denyutjantung', 'mata_kelainan'], 'string', 'max' => 100],
            [['bentukbadan', 'mata_persepsiwarna', 'mata_penglihatanjauh'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'gcs_id' => 'Gcs ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pegawaiperawat_id' => Yii::t('fe', 'Perawat'),
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'klasifikasitekanandarah_id' => 'Klasifikasitekanandarah ID',
            'tglperiksafisik' => Yii::t('fe', 'Tanggal periksa fisik'),
            'keadaanumum' => Yii::t('fe', 'Keadaan umum'),
            'inspeksi' => Yii::t('fe', 'Inspeksi'),
            'palpasi' => Yii::t('fe', 'Palpasi'),
            'perkusi' => Yii::t('fe', 'Perkusi'),
            'auskultasi' => Yii::t('fe', 'Auskultasi'),
            'tekanandarah' => Yii::t('fe', 'Tekanan darah'),
            'td_systolic' => Yii::t('fe', 'Tekanan darah')." Systolic",
            'td_diastolic' => Yii::t('fe', 'Tekanan darah')." Diastolic",
            'meanarteripressure' => Yii::t('fe', 'Mean arteri pressure'),
            'detaknadi' => Yii::t('fe', 'Detak nadi'),
            'heartindex_i1' => 'Heartindex I1',
            'heartindex_i2' => 'Heartindex I2',
            'heartindex_i3' => 'Heartindex I3',
            'suhutubuh' => Yii::t('fe', 'Suhu tubuh'),
            'beratbadan_kg' => Yii::t('fe', 'Berat badan'),
            'tinggibadan_cm' => Yii::t('fe', 'Tinggi badan'),
            'bb_ideal' => Yii::t('fe', 'Berat badan ideal'),
            'pernapasan' => Yii::t('fe', 'Pernapasan'),
            'paramedis_nama' => Yii::t('fe', 'Nama paramedis'),
            'kelainanpadabagtubuh' => Yii::t('fe', 'Kelainan pada bagian tubuh'),
            'jn_paten' => Yii::t('fe', 'Paten'),
            'jn_obstruktifpartial' => 'Obstruktif Partial',
            'jn_obstruktifnormal' => 'Obstruktif Total',
            'jn_stridor' => 'Stridor',
            'pgd_simetri' => 'Simetri',
            'pgd_asimetri' => 'Asimetri',
            'pgp_normal' => 'Normal',
            'pgp_kussmaul' => 'Kussmaul',
            'pgp_takipnea' => 'Takipnea',
            'pgp_retraktif' => 'Retraktif',
            'pgp_dangkal' => 'Dangkal',
            'sirkulasi_nadicarotis' => Yii::t('fe', 'Sirkulasi').' '.Yii::t('fe', 'Nadicarotis'),
            'sirkulasi_nadiradialis' => Yii::t('fe', 'Sirkulasi').' '.Yii::t('fe', 'Nadiradialis'),
            'cfr_kecil_2' => 'Cfr <= 2',
            'cfr_besar_2' => 'Cfr >= 2',
            'jn_gargling' => 'Gargling',
            'kulit_normal' => Yii::t('fe', 'Kulit').' '.Yii::t('fe', 'Normal'),
            'kulit_jaundice' => Yii::t('fe', 'Kulit').' '.Yii::t('fe', 'Jaundice'),
            'kulit_cyanosis' => Yii::t('fe', 'Kulit').' '.Yii::t('fe', 'Cyanosis'),
            'kulit_pucat' => Yii::t('fe', 'Kulit pucat'),
            'kulit_berkeringat' => Yii::t('fe', 'Kulit berkeringat'),
            'akral' => 'Akral',
            'gcs_eye' => 'Eye',
            'gcs_verbal' => 'Verbal',
            'gcs_motorik' => 'Motorik',
            'lila' => 'Lila',
            'lingkarpinggang' => Yii::t('fe', 'Lingkar pinggang'),
            'lingkarpinggul' => Yii::t('fe', 'Lingkar pinggul'),
            'teballemak' => Yii::t('fe', 'Tebal lemak'),
            'tinggilutut' => Yii::t('fe', 'Tinggi lutut'),
            'denyutjantung' => Yii::t('fe', 'Denyut jantung'),
            'lingkarperut_cm' => Yii::t('fe', 'Lingkar perut'),
            'bentukbadan' => Yii::t('fe', 'Bentuk badan'),
            'mata_persepsiwarna' => Yii::t('fe', 'Mata').'persepsi warna',
            'mata_visus_od' => Yii::t('fe', 'Mata').' visus od',
            'mata_visus_os' => Yii::t('fe', 'Mata').' visus os',
            'mata_penglihatanjauh' => Yii::t('fe', 'Mata penglihatan jauh'),
            'mata_kelainan' => Yii::t('fe', 'Mata kelainan'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
            'gcs_is_kapitis' =>Yii::t('fe', 'Kapitis'),
            'tekanandarah_kategori' =>Yii::t('fe', 'Tekanan darah').' '.Yii::t('fe', 'Kategori'),
            'kategori_pernapasan' => Yii::t('fe', 'Kategori Pernapasan'),
            'kesadaran' => Yii::t('fe', 'Kesadaran')
        ];
    }
}
