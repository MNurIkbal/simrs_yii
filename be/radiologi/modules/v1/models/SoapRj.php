<?php

namespace app\modules\v1\models;

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
 */
class SoapRj extends \Doco\components\DocoActiveRecord
{
    const SOAP_PERAWAT = 'soap_perawat';
    const SOAP_DOKTER = 'soap_dokter';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'soaprj_t';
    }
    
    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ['soaprj_id'];
    }

    public function getCustomScenarios()
    {
      
        $scenarios = parent::scenarios();
        $scenarios[self::SOAP_PERAWAT] = ['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','skala_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh'];
        $scenarios[self::SOAP_DOKTER] = ['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_diastolic','td_systolic','is_nyeri','skala_nyeri','is_resikojatuh','pernapasan','detaknadi','suhutubuh','subject', 'object', 'planning', 'a_diag_utama','a_diag_penyerta','planning','catatan_dokter'];
      return $scenarios;
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
            // [['subject','object','planning','a_diag_utama','planning'], 'required','on'=>self::SOAP_DOKTER],
            [['pendaftaran_id', 'pasien_id', 'ruangan_id', 'pegawai_id', 'skala_nyeri', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['imt','tgl_soaprj', 'created_date', 'last_modified_date', 'deleted_date', 'a_diag_penyerta','a_diag_utama', 'subject','object','planning', 'instruksi', 'final', 'is_icd_x'], 'safe'],
            [['beratbadan_kg', 'tinggibadan_cm', 'imt', 'td_systolic', 'td_diastolic', 'pernapasan', 'detaknadi', 'suhutubuh'], 'number'],
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
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_soaprj' => 'Tgl Soaprj',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'pernapasan' => 'Pernapasan',
            'beratbadan_kg' => 'Beratbadan Kg',
            'tinggibadan_cm' => 'Tinggibadan Cm',
            'imt' => 'IMT',
            'detaknadi' => 'Detaknadi',
            'suhutubuh' => 'Suhutubuh',
            'is_nyeri' => 'Is Nyeri',
            'skala_nyeri' => 'Skala Nyeri',
            'is_resikojatuh' => 'Is Resikojatuh',
            'subject' => 'Subject',
            'object' => 'Object',
            'a_diag_utama' => 'A Diag Utama',
            'a_diag_penyerta' => 'A Diag Penyerta',
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
            'is_icd_x' => 'is icd x',
        ];
    }
}
