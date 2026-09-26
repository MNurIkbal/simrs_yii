<?php

namespace app\modules\v1\models;

use Yii;

class RujukanPulang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rujukanpulang_t';
    }


    public function rules() 
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['rujukan_dituju', 'pic_rujukan_dituju', 'diagnosa_masuk', 'diagnosa_keluar', 'keluhan_utama', 'riwayat_penyakit_sekarang', 'riwayat_penyakit_dahulu', 'anamnesis_keluhan_utama', 'anamnesis_kesadaran', 
                'anamnesis_saturasi_o2', 'anamnesis_tensi', 'anamnesis_suhu', 'anamnesis_nadi','anamnesis_pernafasan','additional_data',
                'created_by'], 'default', 'value' => null],
            [['rujukanpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],    
            // [['tanggal_rujukan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],    
            [['pendaftaran_id', 'pasienadmisi_id' ,'rujukan_dituju', 'pic_rujukan_dituju', 'diagnosa_masuk', 'diagnosa_keluar', 'keluhan_utama', 'riwayat_penyakit_sekarang', 'riwayat_penyakit_dahulu', 'anamnesis_keluhan_utama', 'anamnesis_kesadaran', 'anamnesis_saturasi_o2', 'anamnesis_tensi', 'anamnesis_suhu', 'anamnesis_nadi','anamnesis_pernafasan',
                'alasan_dirujuk','pemeriksaan_penunjang','tindakan_medis','tindakan_terapi','tindakan_lainnya',
                'derajat_0','derajat_1','derajat_2','derajat_3','tanggal_rujukan','keadaan_umum','kesadaran','tensi','suhu','nadi',
                'pernafasan','saturasi_o2','catatan_penting'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],    
        ];
    }

    public function attributeLabels()
    {
        return [
            'rujukanpulang_id' => 'rujukanpulang id',
            'pendaftaran_id' => 'pendaftaran id',
            'pasienadmisi_id' => 'pasienadmisi id',
            'rujukan_dituju' => 'RS Yang dituju',
            'pic_rujukan_dituju' => 'PIC RS yang dituju',
            'diagnosa_masuk' => 'Diagnosa Masuk RS',
            'diagnosa_keluar' => 'Diagnosa Keluar RS',
            'keluhan_utama' => 'Keluhan Utama',
            'riwayat_penyakit_sekarang' => 'Riwayat Penyakit Sekarang',
            'riwayat_penyakit_dahulu' => 'Riwayat Penyakit Dahulu',
            'anamnesis_keluhan_utama' => 'Keluhan Utama',
            'anamnesis_kesadaran' => 'Kesadaran',
            'anamnesis_saturasi_o2' => 'SpO2',
            'anamnesis_tensi' => 'Tensi',
            'anamnesis_suhu' => 'Suhu',
            'anamnesis_nadi' => 'Nadi',
            'anamnesis_pernafasan' => 'Pernafasan',
            'alasan_dirujuk' => 'Alasan dirujuk',
            'pemeriksaan_penunjang' => 'pemeriksaan penunjang',
            'tindakan_medis' => 'tindakan medis',
            'tindakan_terapi' => 'tindakan terapi',
            'tindakan_lainnya' => 'Tindakan Lainnya',
            'derajat_0' => 'Derajat 0',
            'derajat_1' => 'Derajat 1',
            'derajat_2' => 'Derajat 2',
            'derajat_3' => 'Derajat 3',
            'tanggal_rujukan' => 'Tanggal Rujukan',
            'keadaan_umum' => 'keadaan umum',
            'kesadaran' => 'Kesadaran',
            'tensi' => 'Tensi',
            'suhu' => 'Suhu',
            'nadi' => 'Nadi',
            'pernafasan' => 'Pernafasan',
            'saturasi_o2' => 'SpO2',
            'catatan_penting' => 'catatan penting',
            'pegawai_id' => 'pegawai id',
            'additional_data' => 'additional data',
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
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['rujukan_id' => 'rujukan_id']);
    }
}
