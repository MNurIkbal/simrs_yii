<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 09:58:27
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-09 13:55:50
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemeriksaanfisik_t".
 *
 * @property int $pemeriksaanfisik_id
 * @property int $gcs_id
 * @property int $pendaftaran_id
 * @property int $pegawaiperawat_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $klasifikasitekanandarah_id
 * @property string $tglperiksafisik
 * @property string $keadaanumum
 * @property string $inspeksi
 * @property string $palpasi
 * @property string $perkusi
 * @property string $auskultasi
 * @property string $tekanandarah
 * @property int $td_systolic
 * @property int $td_diastolic
 * @property double $meanarteripressure
 * @property int $detaknadi
 * @property int $heartindex_i1
 * @property int $heartindex_i2
 * @property int $heartindex_i3
 * @property string $suhutubuh
 * @property double $beratbadan_kg
 * @property double $tinggibadan_cm
 * @property double $bb_ideal
 * @property string $pernapasan
 * @property string $paramedis_nama
 * @property string $kelainanpadabagtubuh
 * @property bool $jn_paten
 * @property bool $jn_obstruktifpartial
 * @property bool $jn_obstruktifnormal
 * @property bool $jn_stridor
 * @property bool $pgd_simetri
 * @property bool $pgd_asimetri
 * @property bool $pgp_normal
 * @property bool $pgp_kussmaul
 * @property bool $pgp_takipnea
 * @property bool $pgp_retraktif
 * @property bool $pgp_dangkal
 * @property int $sirkulasi_nadicarotis
 * @property int $sirkulasi_nadiradialis
 * @property bool $cfr_kecil_2
 * @property bool $cfr_besar_2
 * @property bool $jn_gargling
 * @property bool $kulit_normal
 * @property bool $kulit_jaundice
 * @property bool $kulit_cyanosis
 * @property bool $kulit_pucat
 * @property bool $kulit_berkeringat
 * @property string $akral
 * @property int $gcs_eye
 * @property int $gcs_verbal
 * @property int $gcs_motorik
 * @property double $lila
 * @property double $lingkarpinggang
 * @property double $lingkarpinggul
 * @property double $teballemak
 * @property double $tinggilutut
 * @property string $denyutjantung
 * @property double $lingkarperut_cm
 * @property string $bentukbadan
 * @property string $mata_persepsiwarna
 * @property double $mata_visus_od
 * @property double $mata_visus_os
 * @property string $mata_penglihatanjauh
 * @property string $mata_kelainan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class PemeriksaanFisik extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemeriksaanfisik_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gcs_id', 'pendaftaran_id', 'pegawaiperawat_id', 'pasienadmisi_id', 'pasien_id', 'klasifikasitekanandarah_id', 'td_systolic', 'td_diastolic', 'detaknadi', 'heartindex_i1', 'heartindex_i2', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['gcs_id', 'pendaftaran_id', 'pegawaiperawat_id', 'pasienadmisi_id', 'pasien_id', 'klasifikasitekanandarah_id', 'td_systolic', 'td_diastolic', 'detaknadi', 'heartindex_i1', 'heartindex_i2', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendaftaran_id', 'pegawaiperawat_id', 'pasien_id', 'tglperiksafisik', 'jn_paten', 'jn_obstruktifpartial', 'jn_obstruktifnormal', 'jn_stridor', 'pgd_simetri', 'pgd_asimetri', 'pgp_normal', 'pgp_kussmaul', 'pgp_takipnea', 'pgp_retraktif', 'pgp_dangkal', 'cfr_kecil_2', 'cfr_besar_2', 'jn_gargling', 'kulit_normal', 'kulit_jaundice', 'kulit_cyanosis', 'kulit_pucat', 'kulit_berkeringat', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis'], 'required'],
            [[
                'tglperiksafisik', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'pemeriksaanfisik_id',
                'bodymassindex_id',
                'is_kapitis',
            ], 'safe'],
            [['meanarteripressure', 'beratbadan_kg', 'tinggibadan_cm', 'bb_ideal', 'lila', 'lingkarpinggang', 'lingkarpinggul', 'teballemak', 'tinggilutut', 'lingkarperut_cm', 'mata_visus_od', 'mata_visus_os'], 'number'],
            [['pernapasan', 'additional_data'], 'string'],
            [['jn_paten', 'jn_obstruktifpartial', 'jn_obstruktifnormal', 'jn_stridor', 'pgd_simetri', 'pgd_asimetri', 'pgp_normal', 'pgp_kussmaul', 'pgp_takipnea', 'pgp_retraktif', 'pgp_dangkal', 'cfr_kecil_2', 'cfr_besar_2', 'jn_gargling', 'kulit_normal', 'kulit_jaundice', 'kulit_cyanosis', 'kulit_pucat', 'kulit_berkeringat', 'is_deleted', 'is_active'], 'boolean'],
            [['keadaanumum', 'inspeksi', 'palpasi', 'perkusi', 'auskultasi', 'paramedis_nama'], 'string', 'max' => 500],
            [['tekanandarah'], 'string', 'max' => 20],
            [['suhutubuh'], 'string', 'max' => 10],
            [['kelainanpadabagtubuh'], 'string', 'max' => 30],
            [['akral'], 'string', 'max' => 200],
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
            'pegawaiperawat_id' => 'Pegawaiperawat ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'klasifikasitekanandarah_id' => 'Klasifikasitekanandarah ID',
            'tglperiksafisik' => 'Tglperiksafisik',
            'keadaanumum' => 'Keadaanumum',
            'inspeksi' => 'Inspeksi',
            'palpasi' => 'Palpasi',
            'perkusi' => 'Perkusi',
            'auskultasi' => 'Auskultasi',
            'tekanandarah' => 'Tekanandarah',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'meanarteripressure' => 'Meanarteripressure',
            'detaknadi' => 'Detaknadi',
            'heartindex_i1' => 'Heartindex I1',
            'heartindex_i2' => 'Heartindex I2',
            'heartindex_i3' => 'Heartindex I3',
            'suhutubuh' => 'Suhutubuh',
            'beratbadan_kg' => 'Beratbadan Kg',
            'tinggibadan_cm' => 'Tinggibadan Cm',
            'bb_ideal' => 'Bb Ideal',
            'pernapasan' => 'Pernapasan',
            'paramedis_nama' => 'Paramedis Nama',
            'kelainanpadabagtubuh' => 'Kelainanpadabagtubuh',
            'jn_paten' => 'Jn Paten',
            'jn_obstruktifpartial' => 'Jn Obstruktifpartial',
            'jn_obstruktifnormal' => 'Jn Obstruktifnormal',
            'jn_stridor' => 'Jn Stridor',
            'pgd_simetri' => 'Pgd Simetri',
            'pgd_asimetri' => 'Pgd Asimetri',
            'pgp_normal' => 'Pgp Normal',
            'pgp_kussmaul' => 'Pgp Kussmaul',
            'pgp_takipnea' => 'Pgp Takipnea',
            'pgp_retraktif' => 'Pgp Retraktif',
            'pgp_dangkal' => 'Pgp Dangkal',
            'sirkulasi_nadicarotis' => 'Sirkulasi Nadicarotis',
            'sirkulasi_nadiradialis' => 'Sirkulasi Nadiradialis',
            'cfr_kecil_2' => 'Cfr Kecil 2',
            'cfr_besar_2' => 'Cfr Besar 2',
            'jn_gargling' => 'Jn Gargling',
            'kulit_normal' => 'Kulit Normal',
            'kulit_jaundice' => 'Kulit Jaundice',
            'kulit_cyanosis' => 'Kulit Cyanosis',
            'kulit_pucat' => 'Kulit Pucat',
            'kulit_berkeringat' => 'Kulit Berkeringat',
            'akral' => 'Akral',
            'gcs_eye' => 'Gcs Eye',
            'gcs_verbal' => 'Gcs Verbal',
            'gcs_motorik' => 'Gcs Motorik',
            'lila' => 'Lila',
            'lingkarpinggang' => 'Lingkarpinggang',
            'lingkarpinggul' => 'Lingkarpinggul',
            'teballemak' => 'Teballemak',
            'tinggilutut' => 'Tinggilutut',
            'denyutjantung' => 'Denyutjantung',
            'lingkarperut_cm' => 'Lingkarperut Cm',
            'bentukbadan' => 'Bentukbadan',
            'mata_persepsiwarna' => 'Mata Persepsiwarna',
            'mata_visus_od' => 'Mata Visus Od',
            'mata_visus_os' => 'Mata Visus Os',
            'mata_penglihatanjauh' => 'Mata Penglihatanjauh',
            'mata_kelainan' => 'Mata Kelainan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
}
