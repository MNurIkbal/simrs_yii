<?php

namespace app\modules\v1\models;

use Yii;

class PesanAmbulan extends \Doco\components\DocoActiveRecord
{
    
    const SCENARIO_LUAR = 'luar';
    const SCENARIO_RS = 'rs';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pesanambulan_t';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LUAR] = [
            'tgl_pesanambulan', 
            'pemesan', 
            'jenis_kelamin', 
            'tgl_lahir', 
            'asal_pasien', 
            'kontak_pj', 
            'nama_pj',
            'status_ambulan'
        ];

        $scenarios[self::SCENARIO_RS] = [
            'tgl_pesanambulan', 
            'pasien_id', 
            'tujuan_pasien',
            'status_ambulan'
        ];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pesanambulan', 'tgl_lahir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['pendaftaran_id', 'pasien_id', 'ambulan_id', 'pemakaianambulan_id', 'jenis_kelamin', 'umur', 'kontak_pj', 'td_systolic', 'td_diastolic', 'detaknadi', 'respirasi', 'saturasi', 'ruangan_id', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'ambulan_id', 'pemakaianambulan_id', 'jenis_kelamin', 'umur', 'td_systolic', 'td_diastolic', 'detaknadi', 'respirasi', 'saturasi', 'ruangan_id', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['ambulan_id'], 'required'],
            [['tgl_pesanambulan', 'pemesan', 'jenis_kelamin', 'tgl_lahir', 'asal_pasien', 'nama_pj', 'kontak_pj'], 'required', 'on' => self::SCENARIO_LUAR],
            [['tgl_pesanambulan', 'pasien_id', 'tujuan_pasien'], 'required', 'on' => self::SCENARIO_RS],
            [['asal_pasien', 'keluhan', 'tujuan_pasien', 'keterangan', 'additional_data'], 'string'],
            [['is_sadar', 'is_nafas', 'is_nadi', 'is_deleted', 'is_active'], 'boolean'],
            [['estimasi_biaya'], 'number'],
            [['no_pesanambulan', 'tempat_lahir', 'nama_pj'], 'string', 'max' => 100],
            [['pemesan', 'kesadaran', 'tanda_vital'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulan_id' => 'Pesanambulan ID',
            'no_pesanambulan' => 'No Pesanambulan',
            'tgl_pesanambulan' => 'Tgl Pesanambulan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'ambulan_id' => 'Ambulan ID',
            'pemakaianambulan_id' => 'Pemakaianambulan ID',
            'pemesan' => 'Pemesan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tgl Lahir',
            'umur' => 'Umur',
            'asal_pasien' => 'Asal Pasien',
            'keluhan' => 'Keluhan',
            'is_sadar' => 'Is Sadar',
            'is_nafas' => 'Is Nafas',
            'is_nadi' => 'Is Nadi',
            'nama_pj' => 'Nama Pj',
            'kontak_pj' => 'Kontak Pj',
            'tujuan_pasien' => 'Tujuan Pasien',
            'kesadaran' => 'Kesadaran',
            'tanda_vital' => 'Tanda Vital',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'detaknadi' => 'Detaknadi',
            'respirasi' => 'Respirasi',
            'saturasi' => 'Saturasi',
            'estimasi_biaya' => 'Estimasi Biaya',
            'keterangan' => 'Keterangan',
            'ruangan_id' => 'Ruangan ID',
            'status_ambulan' => 'Status Ambulan',
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
}
