<?php

namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "soaprj_t".
 *
 * @property int $soaprj_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $tgl_soaprj
 * @property int $td_systolic pemeriksaanfisik_t.td_systolic
 * @property int $td_diastolic pemeriksaanfisik_t.td_diastolic
 * @property int $pernapasan pemeriksaanfisik_t.pernapasan
 * @property double $beratbadan_kg pemeriksaanfisik_t.beratbadan_kg
 * @property double $tinggibadan_cm pemeriksaanfisik_t.tinggibadan_cm
 * @property double $imt pemeriksaanfisik_t.imt
 * @property int $detaknadi pemeriksaanfisik_t.detaknadi
 * @property double $suhutubuh pemeriksaanfisik_t.suhutubuh
 * @property bool $is_nyeri anamnesa_t.is_nyeri
 * @property int $skala_nyeri anamnesa_t.skala_nyeri
 * @property bool $is_resikojatuh anamnesa_t.is_resikojatuh
 * @property string $subject
 * @property string $object
 * @property string $a_diag_utama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property string $catatan_dokter
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
 * @property bool $is_icd_x
 * @property string $a_diag_utama_text
 */
class SoapRjForm extends \yii\base\Model
{
    const SOAP_PERAWAT = 'soap_perawat';
    const SOAP_DOKTER = 'soap_dokter';
    const SOAP_CPPT = 'soap_cppt';

    public $soaprj_id;
    public $pendaftaran_id;
    public $konsulpoli_id;
    public $pasien_id;
    public $ruangan_id;
    public $pegawai_id;
    public $tgl_soaprj;
    public $td_systolic; // pemeriksaanfisik_t.td_systolic
    public $td_diastolic; // pemeriksaanfisik_t.td_diastolic
    public $pernapasan; // pemeriksaanfisik_t.pernapasan
    public $beratbadan_kg; // pemeriksaanfisik_t.beratbadan_kg
    public $tinggibadan_cm; // pemeriksaanfisik_t.tinggibadan_cm
    public $imt; // pemeriksaanfisik_t.imt
    public $detaknadi; // pemeriksaanfisik_t.detaknadi
    public $suhutubuh; // pemeriksaanfisik_t.suhutubuh
    public $is_nyeri; // anamnesa_t.is_nyeri
    public $skala_nyeri; // anamnesa_t.skala_nyeri
    public $is_resikojatuh; // anamnesa_t.is_resikojatuh
    public $subject;
    public $object;
    public $a_diag_utama;
    public $a_diag_penyerta;
    public $planning;
    public $catatan_dokter;
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
    public $keterangan_imt;
    public $final;
    public $instruksi;
    public $is_icd_x;
	public $a_diag_utama_text;

    public function getCustomScenarios()
    {

      return [
          self::SOAP_PERAWAT => ['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','skala_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh','subject', 'object', 'planning', 'a_diag_utama','a_diag_penyerta','planning','catatan_dokter','instruksi','a_diag_utama_text'],
          self::SOAP_DOKTER => ['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','skala_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh','subject', 'object', 'planning', 'a_diag_utama','a_diag_penyerta','planning','catatan_dokter','instruksi','a_diag_utama_text'],
          self::SOAP_CPPT => ['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','skala_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh','subject', 'object', 'planning', 'a_diag_utama','a_diag_penyerta','planning','catatan_dokter','instruksi','a_diag_utama_text'],
      ];
    }

    public function scenarios()
    {
        $scenarios = $this->getCustomScenarios();
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh'],'required', 'on' => [self::SOAP_DOKTER, self::SOAP_PERAWAT]],
            [['subject','object','planning','a_diag_utama','planning'], 'required'],
            ['a_diag_utama_text', 'required', 'when' => function ($model) {
                return $model->is_icd_x == 0;
            }],
            ['skala_nyeri', 'required', 'when' => function ($model) {
                return $model->is_nyeri == '1';
            }, 'whenClient' => "function (attribute, value) {
                return $('input:radio[name=\"SoapRjForm[is_nyeri]\"]:checked').val() == '1';
            }"],


            [['soaprj_id', 'pendaftaran_id', 'pasien_id', 'ruangan_id', 'pegawai_id', 'skala_nyeri', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_soaprj', 'created_date', 'last_modified_date', 'deleted_date', 'final', 'soaprj_id','a_diag_utama_text'], 'safe'],
            // [['beratbadan_kg', 'tinggibadan_cm', 'imt', 'suhutubuh'], 'number','numberPattern'=>'/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/','message'=>'Format Salah'],
            [['beratbadan_kg','tinggibadan_cm', 'imt', 'td_systolic', 'td_diastolic', 'pernapasan', 'detaknadi', 'suhutubuh'], 'number'],
            [['skala_nyeri'],'number','numberPattern'=>'/^[1-9]*$/'],
            [['is_nyeri', 'is_resikojatuh', 'is_deleted', 'is_active', 'is_icd_x'], 'boolean'],
            [['subject', 'object', 'planning', 'instruksi', 'catatan_dokter', 'additional_data'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'soaprj_id' => 'Soaprj ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_soaprj' => 'Tgl Soaprj',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'pernapasan' => 'Pernapasan',
            'beratbadan_kg' => 'Berat Badan',
            'tinggibadan_cm' => 'Tinggi Badan',
            'imt' => 'Indeks Masa Tubuh',
            'detaknadi' => 'Nadi',
            'suhutubuh' => 'Suhu Tubuh',
            'is_nyeri' => 'Nyeri',
            'skala_nyeri' => 'Skala Nyeri',
            'is_resikojatuh' => 'Resiko Jatuh',
            'subject' => 'Subject',
            'object' => 'Object',
            'a_diag_utama' => 'Diagnosa Utama',
            'a_diag_penyerta' => 'Diagnosa Penyerta',
            'planning' => 'Planning',
            'catatan_dokter' => 'Catatan Dokter',
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
            'is_icd_x' => 'ICD X',
            'a_diag_utama_text' => 'Diagnosa Utama',
        ];
    }
}
