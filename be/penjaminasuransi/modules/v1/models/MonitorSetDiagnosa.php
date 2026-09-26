<?php

namespace app\modules\v1\models;

 use Yii;

class MonitorSetDiagnosa extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'monitorsetdiagnosa_t';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id', 'hak_kelas', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id', 'hak_kelas', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diag_penyerta', 'diag_tindakan', 'additional_data'], 'string'],
            [['total', 'tambahan_biaya', 'persen_tambahan', 'total_naikkelas', 'total_kelaspelayanan'], 'number'],
            [['created_date', 'last_modified_date', 'deleted_date', 'diag_utama_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorsetdiagnosa_id' => 'Monitorsetdiagnosa ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'diag_utama_id' => 'Diag Utama ID',
            'diag_penyerta' => 'Diag Penyerta',
            'diag_tindakan' => 'Diag Tindakan',
            'hak_kelas' => 'Hak Kelas',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'total' => 'Total',
            'tambahan_biaya' => 'Tambahan Biaya',
            'persen_tambahan' => 'Persen Tambahan',
            'total_naikkelas' => 'Total Naikkelas',
            'total_kelaspelayanan' => 'Total Kelaspelayanan',
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
